import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Chart = Chart;
window.Alpine = Alpine;

// Cache CSRF token once at module level — no DOM query on every request
let _csrfToken = null;
function getCsrf() {
    if (!_csrfToken) {
        _csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }
    return _csrfToken;
}

// Shared fetch helper — JSON POST, returns parsed data or throws
async function jsonPost(url, body) {
    const res = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrf(),
            'Accept': 'application/json',
        },
        body: JSON.stringify(body),
    });
    if (!res.ok) {
        let msg = 'Something went wrong. Please try again.';
        try { const d = await res.json(); if (d.message) msg = d.message; } catch (_) {}
        throw new Error(msg);
    }
    return res.json();
}

// Prefetch a URL with low priority so navigation feels instant
function prefetchUrl(url) {
    if (!url || document.querySelector(`link[rel="prefetch"][href="${url}"]`)) return;
    const link = document.createElement('link');
    link.rel = 'prefetch';
    link.href = url;
    link.as = 'document';
    document.head.appendChild(link);
}

// Global Rayka E-Commerce Store
Alpine.store('rayka', {
    wishlistCount: 0,
    cartCount: 0,
    cartItems: {},
    wishlistItems: [],
    toastMessage: '',
    toastVisible: false,
    toastType: 'success',
    loadingItems: {},
    cartSubtotal: 0,
    cartDiscount: 0,
    cartTotal: 0,
    cartShipping: 0,
    lastFetchedAt: Date.now(),
    _toastTimer: null,

    init() {
        if (window.__INITIAL_RAYKA__) {
            this.cartCount    = window.__INITIAL_RAYKA__.cartCount    || 0;
            this.wishlistCount= window.__INITIAL_RAYKA__.wishlistCount|| 0;
            this.wishlistItems= window.__INITIAL_RAYKA__.wishlistItems|| [];
            this.cartItems    = this._parseCartItems(window.__INITIAL_RAYKA__.cartItems || {});
        } else {
            this.fetchCounts();
        }
    },

    _parseCartItems(raw) {
        const out = {};
        for (const k in raw) { out[parseInt(k)] = parseInt(raw[k]); }
        return out;
    },

    getCartQty(productId) {
        return this.cartItems[parseInt(productId)] || 0;
    },

    isLoading(productId) {
        return Boolean(this.loadingItems[parseInt(productId)]);
    },

    isInWishlist(productId) {
        return Array.isArray(this.wishlistItems) && this.wishlistItems.includes(parseInt(productId));
    },

    showToast(message, type = 'success') {
        if (this._toastTimer) clearTimeout(this._toastTimer);
        this.toastMessage = message;
        this.toastType    = type;
        this.toastVisible = true;
        this._toastTimer  = setTimeout(() => { this.toastVisible = false; }, 3200);
    },

    async fetchCounts() {
        this.lastFetchedAt = Date.now();
        try {
            const res = await fetch('/api/store-counts');
            if (!res.ok) return;
            const data = await res.json();
            this.wishlistCount = data.wishlist_count || 0;
            this.cartCount     = data.cart_count     || 0;
            this.cartItems     = this._parseCartItems(data.cart_items || {});
            this.wishlistItems = data.wishlist_items  || [];
        } catch (e) {
            console.error('Failed to fetch counts', e);
        }
    },

    async toggleWishlist(productId) {
        const pid = parseInt(productId);
        try {
            const data = await jsonPost('/wishlist/toggle', { product_id: pid });
            if (data.status === 'added') {
                this.wishlistCount = data.count;
                if (!this.wishlistItems.includes(pid)) this.wishlistItems.push(pid);
                this.showToast(data.message || 'Added to your royal wishlist!', 'success');
                return true;
            } else if (data.status === 'removed') {
                this.wishlistCount = data.count;
                this.wishlistItems = this.wishlistItems.filter(id => id !== pid);
                this.showToast(data.message || 'Removed from wishlist', 'info');
                return false;
            }
        } catch (e) {
            this.showToast('Something went wrong. Please try again.', 'error');
            return false;
        }
    },

    async addToCart(productId, quantity = 1, variantId = null) {
        const pid     = parseInt(productId);
        const qty     = parseInt(quantity) || 1;
        const prevQty = this.cartItems[pid] || 0;
        const prevCount = this.cartCount;

        // --- Instant optimistic update (0ms perceived latency) ---
        this.cartItems[pid] = prevQty + qty;
        this.cartCount      = this.cartCount + qty;
        this.loadingItems[pid] = true;
        // Force Alpine reactivity without object spread (cheaper)
        this.cartItems = this.cartItems;
        this.loadingItems = this.loadingItems;

        // 3s safety net so spinner never gets stuck
        const failSafe = setTimeout(() => {
            this.loadingItems[pid] = false;
            this.loadingItems = this.loadingItems;
        }, 3000);

        try {
            const data = await jsonPost('/cart/add', {
                product_id: pid,
                quantity:   qty,
                variant_id: variantId,
            });
            if (data.success) {
                this.cartCount = data.cart_count;
                if (data.cart_subtotal !== undefined) this.cartSubtotal = data.cart_subtotal;
                if (data.cart_items) this.cartItems = this._parseCartItems(data.cart_items);
                this.showToast(data.message || 'Added to your shopping bag!', 'success');
                window.dispatchEvent(new CustomEvent('cart-updated'));
                return true;
            } else {
                // Rollback
                this.cartItems[pid] = prevQty;
                this.cartCount      = prevCount;
                this.cartItems      = this.cartItems;
                this.showToast(data.message || 'Failed to add item', 'error');
                return false;
            }
        } catch (e) {
            // Rollback
            this.cartItems[pid] = prevQty;
            this.cartCount      = prevCount;
            this.cartItems      = this.cartItems;
            this.showToast(e.message || 'Failed to add item to bag', 'error');
            return false;
        } finally {
            clearTimeout(failSafe);
            this.loadingItems[pid] = false;
            this.loadingItems      = this.loadingItems;
        }
    },

    async changeQty(productId, change) {
        const pid     = parseInt(productId);
        const chg     = parseInt(change);
        const prevQty = this.cartItems[pid] || 0;
        const nextQty = Math.max(0, prevQty + chg);
        const prevCount = this.cartCount;

        // --- Instant optimistic update ---
        this.cartItems[pid]    = nextQty;
        this.cartCount         = Math.max(0, this.cartCount + chg);
        this.loadingItems[pid] = true;
        this.cartItems         = this.cartItems;
        this.loadingItems      = this.loadingItems;

        // 3s safety net
        const failSafe = setTimeout(() => {
            this.loadingItems[pid] = false;
            this.loadingItems      = this.loadingItems;
        }, 3000);

        try {
            const data = await jsonPost('/cart/product-quantity', {
                product_id: pid,
                change:     chg,
            });
            if (data.success) {
                this.cartCount = data.cart_count;
                if (data.cart_items) {
                    const parsed = this._parseCartItems(data.cart_items);
                    if (nextQty === 0 || !parsed[pid]) parsed[pid] = 0;
                    this.cartItems = parsed;
                }
                if (data.cart_subtotal !== undefined) this.cartSubtotal = data.cart_subtotal;
                if (data.cart_discount !== undefined) this.cartDiscount = data.cart_discount;
                if (data.cart_total    !== undefined) this.cartTotal    = data.cart_total;
                if (data.cart_shipping !== undefined) this.cartShipping = data.cart_shipping;
                if (data.message) this.showToast(data.message, 'success');
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: { productId: pid, nextQty } }));
                return true;
            } else {
                // Rollback
                this.cartItems[pid] = prevQty;
                this.cartCount      = prevCount;
                this.cartItems      = this.cartItems;
                this.showToast(data.message || 'Could not update quantity', 'error');
                return false;
            }
        } catch (e) {
            // Rollback
            this.cartItems[pid] = prevQty;
            this.cartCount      = prevCount;
            this.cartItems      = this.cartItems;
            this.showToast(e.message || 'Network error updating quantity', 'error');
            return false;
        } finally {
            clearTimeout(failSafe);
            this.loadingItems[pid] = false;
            this.loadingItems      = this.loadingItems;
        }
    }
});

Alpine.start();

// ─── BFCache & Tab Focus Sync ─────────────────────────────────────────────────
window.addEventListener('pageshow', (e) => {
    if (window.Alpine && Alpine.store('rayka')) {
        const s = Alpine.store('rayka');
        // Clear any stuck loading states from BFCache restore
        s.loadingItems = {};
        if (e.persisted) s.fetchCounts();
    }
});

window.addEventListener('focus', () => {
    if (window.Alpine && Alpine.store('rayka')) {
        const s = Alpine.store('rayka');
        // Re-verify only if tab was inactive for more than 90 seconds
        if (Date.now() - (s.lastFetchedAt || 0) > 90000) {
            s.fetchCounts();
        }
    }
});

// ─── Prefetch cart & checkout on first hover/touch of any add-to-cart btn ────
let _prefetched = false;
document.addEventListener('mouseover', (e) => {
    if (_prefetched) return;
    if (e.target.closest('[data-prefetch-cart]') || e.target.closest('button')) {
        _prefetched = true;
        prefetchUrl('/cart');
        prefetchUrl('/checkout');
    }
}, { passive: true });
document.addEventListener('touchstart', () => {
    if (_prefetched) return;
    _prefetched = true;
    prefetchUrl('/cart');
    prefetchUrl('/checkout');
}, { once: true, passive: true });
