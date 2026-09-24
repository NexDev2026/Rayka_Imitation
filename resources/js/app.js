import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Chart = Chart;
window.Alpine = Alpine;

// Global Rayka E-Commerce Store
Alpine.store('rayka', {
    wishlistCount: 0,
    cartCount: 0,
    cartItems: {},
    wishlistItems: [],
    toastMessage: '',
    toastVisible: false,
    toastType: 'success', // success, error, info
    loadingItems: {},
    cartSubtotal: 0,
    cartDiscount: 0,
    cartTotal: 0,
    cartShipping: 0,

    init() {
        if (window.__INITIAL_RAYKA__) {
            this.cartCount = window.__INITIAL_RAYKA__.cartCount || 0;
            this.wishlistCount = window.__INITIAL_RAYKA__.wishlistCount || 0;
            this.wishlistItems = window.__INITIAL_RAYKA__.wishlistItems || [];
            const raw = window.__INITIAL_RAYKA__.cartItems || {};
            const parsed = {};
            Object.keys(raw).forEach(k => { parsed[parseInt(k)] = parseInt(raw[k]); });
            this.cartItems = parsed;
        }
        this.fetchCounts();
    },

    getCartQty(productId) {
        if (!this.cartItems) return 0;
        const pid = parseInt(productId);
        return this.cartItems[pid] || 0;
    },

    isInWishlist(productId) {
        return Array.isArray(this.wishlistItems) && this.wishlistItems.includes(parseInt(productId));
    },

    showToast(message, type = 'success') {
        this.toastMessage = message;
        this.toastType = type;
        this.toastVisible = true;
        setTimeout(() => {
            this.toastVisible = false;
        }, 3500);
    },

    async fetchCounts() {
        try {
            const res = await fetch('/api/store-counts');
            if (res.ok) {
                const data = await res.json();
                this.wishlistCount = data.wishlist_count || 0;
                this.cartCount = data.cart_count || 0;
                // Ensure all keys are integers for consistent lookup
                const raw = data.cart_items || {};
                const parsed = {};
                Object.keys(raw).forEach(k => { parsed[parseInt(k)] = parseInt(raw[k]); });
                this.cartItems = parsed;
                this.wishlistItems = data.wishlist_items || [];
            }
        } catch (e) {
            console.error('Failed to fetch counts', e);
        }
    },

    async toggleWishlist(productId, btnEl = null) {
        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch('/wishlist/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ product_id: productId })
            });
            const data = await res.json();
            const pid = parseInt(productId);
            if (data.status === 'added') {
                this.wishlistCount = data.count;
                if (!this.wishlistItems.includes(pid)) {
                    this.wishlistItems.push(pid);
                }
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
        console.log('addToCart called!', { productId, quantity, variantId });
        const pid = parseInt(productId);
        const qty = parseInt(quantity) || 1;
        const prevQty = this.cartItems[pid] || 0;
        const newQty = prevQty + qty;
        const prevItems = { ...this.cartItems };
        const prevCount = this.cartCount;

        // Instant optimistic update for 0ms response
        this.cartItems = { ...this.cartItems, [pid]: newQty };
        this.cartCount = this.cartCount + qty;
        this.loadingItems = { ...this.loadingItems, [pid]: true };

        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: pid,
                    quantity: qty,
                    variant_id: variantId
                })
            });
            if (!res.ok) {
                let msg = 'Could not add to bag. Please refresh and try again.';
                try {
                    const errData = await res.json();
                    if (errData.message) msg = errData.message;
                } catch(ign) {}
                this.cartItems = prevItems;
                this.cartCount = prevCount;
                this.showToast(msg, 'error');
                return false;
            }
            const data = await res.json();
            if (data.success) {
                this.cartCount = data.cart_count;
                if (data.cart_subtotal !== undefined) this.cartSubtotal = data.cart_subtotal;
                if (data.cart_items) {
                    const parsed = {};
                    Object.keys(data.cart_items).forEach(k => { parsed[parseInt(k)] = parseInt(data.cart_items[k]); });
                    this.cartItems = parsed;
                }
                this.showToast(data.message || 'Added to your shopping bag!', 'success');
                window.dispatchEvent(new CustomEvent('cart-updated'));
                return true;
            } else {
                this.cartItems = prevItems;
                this.cartCount = prevCount;
                this.showToast(data.message || 'Failed to add item', 'error');
                return false;
            }
        } catch (e) {
            this.cartItems = prevItems;
            this.cartCount = prevCount;
            this.showToast('Failed to add item to bag', 'error');
            return false;
        } finally {
            this.loadingItems = { ...this.loadingItems, [pid]: false };
        }
    },

    async changeQty(productId, change) {
        const pid = parseInt(productId);
        const chg = parseInt(change);
        const prevQty = this.cartItems[pid] || 0;
        const nextQty = Math.max(0, prevQty + chg);
        const prevItems = { ...this.cartItems };
        const prevCount = this.cartCount;

        // Instant optimistic update for 0ms response
        if (nextQty === 0) {
            this.cartItems = { ...this.cartItems, [pid]: 0 };
        } else {
            this.cartItems = { ...this.cartItems, [pid]: nextQty };
        }
        this.cartCount = Math.max(0, this.cartCount + chg);
        this.loadingItems = { ...this.loadingItems, [pid]: true };

        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch('/cart/product-quantity', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: pid,
                    change: chg
                })
            });
            if (!res.ok) {
                let msg = 'Could not update quantity. Please try again.';
                try {
                    const errData = await res.json();
                    if (errData.message) msg = errData.message;
                } catch(ign) {}
                this.cartItems = prevItems;
                this.cartCount = prevCount;
                this.showToast(msg, 'error');
                return false;
            }
            const data = await res.json();
            if (data.success) {
                this.cartCount = data.cart_count;
                if (data.cart_items) {
                    const parsed = {};
                    Object.keys(data.cart_items).forEach(k => { parsed[parseInt(k)] = parseInt(data.cart_items[k]); });
                    if (nextQty === 0 || !parsed[pid]) {
                        parsed[pid] = 0;
                    }
                    this.cartItems = parsed;
                }
                if (data.cart_subtotal !== undefined) this.cartSubtotal = data.cart_subtotal;
                if (data.cart_discount !== undefined) this.cartDiscount = data.cart_discount;
                if (data.cart_total !== undefined) this.cartTotal = data.cart_total;
                if (data.cart_shipping !== undefined) this.cartShipping = data.cart_shipping;
                if (data.message) { this.showToast(data.message, 'success'); }
                
                // Dispatch event so local components (like cart page) can sync if they need to
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: { productId: pid, nextQty: nextQty } }));
                return true;
            } else {
                this.cartItems = prevItems;
                this.cartCount = prevCount;
                this.showToast(data.message || 'Could not update quantity', 'error');
                return false;
            }
        } catch (e) {
            this.cartItems = prevItems;
            this.cartCount = prevCount;
            this.showToast('Network error updating quantity', 'error');
            return false;
        } finally {
            this.loadingItems = { ...this.loadingItems, [pid]: false };
        }
    }
});

Alpine.start();

// Instant BFCache & Tab Focus Synchronization
// Reset loadingItems first to clear any stuck loading states from BF cache restore
window.addEventListener('pageshow', () => {
    if (window.Alpine && Alpine.store('rayka')) {
        Alpine.store('rayka').loadingItems = {};
        Alpine.store('rayka').fetchCounts();
    }
});
window.addEventListener('focus', () => {
    if (window.Alpine && Alpine.store('rayka')) {
        Alpine.store('rayka').fetchCounts();
    }
});

