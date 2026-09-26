<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttributeGroup;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductDocument;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ReflectionClass;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

class AdminProductController extends Controller
{
    /**
     * Sanitize uploaded file collections from the request.
     * Drops empty HTML file upload placeholders (UPLOAD_ERR_NO_FILE) and clears
     * Laravel's internal convertedFiles cache so validation evaluates only real files.
     */
    protected function sanitizeUploadedFiles(Request $request): void
    {
        foreach (['images', 'product_images', 'doc_files'] as $key) {
            if ($request->files->has($key)) {
                $raw = $request->files->get($key);
                $clean = array_filter(is_array($raw) ? $raw : [$raw], function ($f) {
                    return $f instanceof SymfonyUploadedFile && $f->isValid() && $f->getError() !== UPLOAD_ERR_NO_FILE;
                });
                if (empty($clean)) {
                    $request->files->remove($key);
                } else {
                    $request->files->set($key, array_values($clean));
                }
            }
        }

        $ref = new ReflectionClass($request);
        while ($ref) {
            if ($ref->hasProperty('convertedFiles')) {
                $prop = $ref->getProperty('convertedFiles');
                $prop->setAccessible(true);
                $prop->setValue($request, null);
                break;
            }
            $ref = $ref->getParentClass();
        }
    }

    public function index(Request $request)
    {
        $query = Product::with(['category', 'images']);

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('type')) {
            $query->where('product_type', $request->type);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('sku', 'like', "%{$s}%");
            });
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $types = Product::whereNotNull('product_type')->distinct()->pluck('product_type');

        return view('admin.products.index', compact('products', 'categories', 'types'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $attributeGroups = AttributeGroup::with('values')->get();

        return view('admin.products.create', compact('categories', 'attributeGroups'));
    }

    public function store(Request $request)
    {
        $this->sanitizeUploadedFiles($request);

        $messages = [
            'name.required' => 'Product title / name is required.',
            'category_id.required' => 'Please select a valid category.',
            'sku.required' => 'SKU is required.',
            'sku.unique' => 'This SKU has already been taken by another product.',
            'price.required' => 'Selling price is required.',
            'mrp.required' => 'MRP price is required.',
            'images.required' => 'Please upload at least one product image.',
            'images.*.max' => 'Each product image must not exceed 10MB.',
            'images.*.mimes' => 'Product images must be in JPEG, PNG, JPG, WebP, SVG, or GIF format.',
            'images.*.uploaded' => 'One or more images failed to upload. Check that file size is within server limits.',
            'product_images.*.max' => 'Each product image must not exceed 10MB.',
            'product_images.*.mimes' => 'Product images must be in JPEG, PNG, JPG, WebP, SVG, or GIF format.',
            'product_images.*.uploaded' => 'One or more images failed to upload. Check that file size is within server limits.',
            'doc_files.*.max' => 'Each document file must not exceed 15MB.',
            'doc_files.*.mimes' => 'Documents must be PDF, DOC, DOCX, XLS, XLSX, or TXT format.',
            'doc_files.*.uploaded' => 'One or more documents failed to upload. Check that file size is within server limits.',
        ];

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required|string|unique:products,sku|max:100',
            'price' => 'required|numeric|min:0',
            'mrp' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'specifications' => 'nullable',
            'spec_keys' => 'nullable|array',
            'spec_values' => 'nullable|array',
            'care_instructions' => 'nullable|string',
            'youtube_url' => 'nullable|string|max:500',
            'images' => 'required|array|min:1',
            'images.*' => 'file|mimes:jpeg,png,jpg,webp,svg,gif|max:10240', // Max 10MB
            'product_images' => 'nullable|array',
            'product_images.*' => 'file|mimes:jpeg,png,jpg,webp,svg,gif|max:10240',
            'doc_files' => 'nullable|array',
            'doc_files.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,txt|max:15360', // Max 15MB
            'attributes' => 'nullable|array',
            'attributes.*' => 'exists:attribute_values,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ], $messages);

        $product = Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name).'-'.strtolower(Str::random(4)),
            'category_id' => $request->category_id,
            'sku' => strtoupper($request->sku),
            'price' => $request->price,
            'mrp' => $request->mrp,
            'stock_quantity' => $request->stock_quantity,
            'short_description' => $request->short_description,
            'description' => $request->description,
            'specifications' => $this->processSpecifications($request),
            'care_instructions' => $this->processCareInstructions($request),
            'youtube_url' => $request->youtube_url,
            'is_featured' => $request->boolean('is_featured'),
            'is_trending' => $request->boolean('is_trending'),
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
            'meta_title' => $request->meta_title ?: $request->name,
            'meta_description' => $request->meta_description ?: $request->short_description,
        ]);

        // Handle Multi-image upload (Drag & Drop) - Up to 10MB each
        $imageFiles = $request->file('images') ?: $request->file('product_images');
        if ($imageFiles) {
            if (! is_array($imageFiles)) {
                $imageFiles = [$imageFiles];
            }
            $productDir = public_path('uploads/products');
            if (! file_exists($productDir)) {
                mkdir($productDir, 0777, true);
            }
            $uploadedCount = 0;
            foreach ($imageFiles as $file) {
                if ($file && $file->isValid()) {
                    $imageUrl = ImageUploadService::uploadAndConvertToWebp(
                        file: $file,
                        folder: 'uploads/products',
                        prefix: 'prod',
                        maxWidth: 1400,
                        maxHeight: 1400,
                        quality: 82
                    );

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url' => $imageUrl,
                        'is_primary' => $uploadedCount === 0,
                        'is_secondary' => $uploadedCount === 1,
                        'sort_order' => $uploadedCount + 1,
                    ]);
                    $uploadedCount++;
                }
            }
        }

        // Handle Product Documents (PDFs / Files) - Up to 15MB each
        if ($request->hasFile('doc_files')) {
            $files = $request->file('doc_files');
            if (! is_array($files)) {
                $files = [$files];
            }
            $titles = $request->input('doc_titles', []);
            $types = $request->input('doc_types', []);
            $docDir = public_path('uploads/documents');
            if (! file_exists($docDir)) {
                mkdir($docDir, 0777, true);
            }
            foreach ($files as $index => $file) {
                if ($file && $file->isValid()) {
                    $filename = 'doc_'.time().'_'.$index.'_'.Str::random(5).'.'.$file->getClientOriginalExtension();
                    $file->move($docDir, $filename);

                    // Mirror document/PDF to external rayka_uploads/documents/
                    ImageUploadService::mirrorToExternalUploads($docDir . DIRECTORY_SEPARATOR . $filename, 'documents', $filename);

                    ProductDocument::create([
                        'product_id' => $product->id,
                        'title' => ! empty($titles[$index]) ? $titles[$index] : $file->getClientOriginalName(),
                        'file_path' => '/uploads/documents/'.$filename,
                        'type' => ! empty($types[$index]) ? $types[$index] : 'other',
                        'sort_order' => $index,
                    ]);
                }
            }
        }

        // Attach Attribute Values
        if ($request->filled('attributes')) {
            $attrIds = AttributeValue::whereIn('id', (array) $request->input('attributes', []))->pluck('id')->all();
            $product->attributeValues()->sync($attrIds);
        }

        // Add Variants (e.g. sizes)
        if ($request->filled('variant_names') && is_array($request->variant_names)) {
            foreach ($request->variant_names as $k => $vVal) {
                if (! empty($vVal)) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'name' => 'Size / Option',
                        'value' => $vVal,
                        'stock_quantity' => (int) ($request->variant_stocks[$k] ?? 10),
                        'price_override' => ! empty($request->variant_prices[$k]) ? $request->variant_prices[$k] : null,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Jewellery product created successfully!');
    }

    public function edit($id)
    {
        $product = Product::with(['images', 'variants', 'attributeValues'])->findOrFail($id);
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $attributeGroups = AttributeGroup::with('values')->get();

        return view('admin.products.edit', compact('product', 'categories', 'attributeGroups'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $this->sanitizeUploadedFiles($request);

        $messages = [
            'name.required' => 'Product title / name is required.',
            'category_id.required' => 'Please select a valid category.',
            'sku.required' => 'SKU is required.',
            'sku.unique' => 'This SKU has already been taken by another product.',
            'price.required' => 'Selling price is required.',
            'mrp.required' => 'MRP price is required.',
            'images.*.max' => 'Each product image must not exceed 10MB.',
            'images.*.mimes' => 'Product images must be in JPEG, PNG, JPG, WebP, SVG, or GIF format.',
            'images.*.uploaded' => 'One or more images failed to upload. Check that file size is within server limits.',
            'product_images.*.max' => 'Each product image must not exceed 10MB.',
            'product_images.*.mimes' => 'Product images must be in JPEG, PNG, JPG, WebP, SVG, or GIF format.',
            'product_images.*.uploaded' => 'One or more images failed to upload. Check that file size is within server limits.',
            'doc_files.*.max' => 'Each document file must not exceed 15MB.',
            'doc_files.*.mimes' => 'Documents must be PDF, DOC, DOCX, XLS, XLSX, or TXT format.',
            'doc_files.*.uploaded' => 'One or more documents failed to upload. Check that file size is within server limits.',
        ];

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required|string|max:100|unique:products,sku,'.$product->id,
            'price' => 'required|numeric|min:0',
            'mrp' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'specifications' => 'nullable',
            'spec_keys' => 'nullable|array',
            'spec_values' => 'nullable|array',
            'care_instructions' => 'nullable|string',
            'youtube_url' => 'nullable|string|max:500',
            'images' => 'nullable|array',
            'images.*' => 'file|mimes:jpeg,png,jpg,webp,svg,gif|max:10240', // Max 10MB
            'product_images' => 'nullable|array',
            'product_images.*' => 'file|mimes:jpeg,png,jpg,webp,svg,gif|max:10240',
            'doc_files' => 'nullable|array',
            'doc_files.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,txt|max:15360', // Max 15MB
            'attributes' => 'nullable|array',
            'attributes.*' => 'exists:attribute_values,id',
        ], $messages);

        $updateData = [
            'name' => $request->name,
            'category_id' => $request->category_id,
            'sku' => strtoupper($request->sku),
            'price' => $request->price,
            'mrp' => $request->mrp,
            'stock_quantity' => $request->stock_quantity,
            'short_description' => $request->short_description,
            'description' => $request->has('description') ? $request->description : $product->description,
            'youtube_url' => $request->youtube_url,
            'is_featured' => $request->boolean('is_featured'),
            'is_trending' => $request->boolean('is_trending'),
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
            'meta_title' => $request->meta_title ?: $request->name,
            'meta_description' => $request->meta_description ?: $request->short_description,
        ];

        if ($request->has('specifications') || $request->has('spec_keys')) {
            $updateData['specifications'] = $this->processSpecifications($request);
        }

        if ($request->has('care_instructions')) {
            $updateData['care_instructions'] = $this->processCareInstructions($request);
        }

        $product->update($updateData);

        // Upload additional / replacement images (Drag & Drop) - Up to 10MB each
        $uploadedImages = $request->file('images') ?: $request->file('product_images');
        if ($uploadedImages) {
            if (! is_array($uploadedImages)) {
                $uploadedImages = [$uploadedImages];
            }

            // Remove any dummy/placeholder rows so they don't block new images
            $product->images()->where(function ($q) {
                $q->where('image_url', 'like', '%/images/products/p1-a.svg%')
                    ->orWhere('image_url', 'like', '%placeholder%');
            })->delete();

            $existingImages = $product->images()->orderBy('sort_order')->get();
            $existingCount = $existingImages->count();

            // Check if newly uploaded images should become Primary
            $makeFirstNewPrimary = $request->boolean('set_new_primary', true) || $existingCount === 0 || ! $existingImages->contains('is_primary', true);

            if ($makeFirstNewPrimary) {
                ProductImage::where('product_id', $product->id)->update(['is_primary' => false]);
            }

            $productDir = public_path('uploads/products');
            if (! file_exists($productDir)) {
                mkdir($productDir, 0777, true);
            }
            $uploadedIndex = 0;
            foreach ($uploadedImages as $file) {
                if ($file && $file->isValid()) {
                    $imageUrl = ImageUploadService::uploadAndConvertToWebp(
                        file: $file,
                        folder: 'uploads/products',
                        prefix: 'prod',
                        maxWidth: 1400,
                        maxHeight: 1400,
                        quality: 82
                    );

                    $isPrimary = ($makeFirstNewPrimary && $uploadedIndex === 0);
                    $isSecondary = (! $isPrimary && (($existingCount === 0 && $uploadedIndex === 1) || ($existingCount === 1 && $uploadedIndex === 0)));

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url' => $imageUrl,
                        'is_primary' => $isPrimary,
                        'is_secondary' => $isSecondary,
                        'sort_order' => $existingCount + $uploadedIndex + 1,
                    ]);
                    $uploadedIndex++;
                }
            }

            // Ensure exactly one image is primary if images exist
            $allImages = ProductImage::where('product_id', $product->id)->orderBy('sort_order')->get();
            if ($allImages->isNotEmpty() && ! $allImages->contains('is_primary', true)) {
                $allImages[0]->update(['is_primary' => true]);
            }
            if ($allImages->count() > 1 && ! $allImages->contains('is_secondary', true)) {
                $candidates = $allImages->where('is_primary', false)->values();
                if ($candidates->isNotEmpty()) {
                    $candidates[0]->update(['is_secondary' => true]);
                }
            }
        }

        // Handle Product Documents (PDFs / Files) - Up to 15MB each
        if ($request->hasFile('doc_files')) {
            $files = $request->file('doc_files');
            if (! is_array($files)) {
                $files = [$files];
            }
            $titles = $request->input('doc_titles', []);
            $types = $request->input('doc_types', []);
            $existingDocCount = $product->documents()->count();
            $docDir = public_path('uploads/documents');
            if (! file_exists($docDir)) {
                mkdir($docDir, 0777, true);
            }
            foreach ($files as $index => $file) {
                if ($file && $file->isValid()) {
                    $filename = 'doc_'.time().'_'.$index.'_'.Str::random(5).'.'.$file->getClientOriginalExtension();
                    $file->move($docDir, $filename);

                    // Mirror document/PDF to external rayka_uploads/documents/
                    ImageUploadService::mirrorToExternalUploads($docDir . DIRECTORY_SEPARATOR . $filename, 'documents', $filename);

                    ProductDocument::create([
                        'product_id' => $product->id,
                        'title' => ! empty($titles[$index]) ? $titles[$index] : $file->getClientOriginalName(),
                        'file_path' => '/uploads/documents/'.$filename,
                        'type' => ! empty($types[$index]) ? $types[$index] : 'other',
                        'sort_order' => $existingDocCount + $index,
                    ]);
                }
            }
        }

        // Sync Attributes
        if ($request->has('attributes')) {
            $attrIds = AttributeValue::whereIn('id', (array) $request->input('attributes', []))->pluck('id')->all();
            $product->attributeValues()->sync($attrIds);
        } else {
            $product->attributeValues()->sync([]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully!');
    }

    public function setPrimaryImage($id)
    {
        $image = ProductImage::findOrFail($id);
        $productId = $image->product_id;

        ProductImage::where('product_id', $productId)->update(['is_primary' => false]);
        $image->update([
            'is_primary' => true,
            'is_secondary' => false,
        ]);

        return back()->with('success', 'Primary cover image set successfully!');
    }

    public function setSecondaryImage($id)
    {
        $image = ProductImage::findOrFail($id);
        $productId = $image->product_id;

        ProductImage::where('product_id', $productId)->update(['is_secondary' => false]);
        $image->update([
            'is_secondary' => true,
            'is_primary' => false,
        ]);

        // Ensure at least one image remains primary
        $hasPrimary = ProductImage::where('product_id', $productId)->where('is_primary', true)->exists();
        if (! $hasPrimary) {
            $other = ProductImage::where('product_id', $productId)->where('id', '!=', $image->id)->first();
            if ($other) {
                $other->update(['is_primary' => true]);
            }
        }

        return back()->with('success', 'Hover-swap secondary image updated successfully!');
    }

    public function deleteImage($id)
    {
        $image = ProductImage::findOrFail($id);
        $productId = $image->product_id;
        $wasPrimary = $image->is_primary;

        if (str_starts_with($image->image_url, '/storage/')) {
            $storagePath = substr($image->image_url, 9);
            Storage::disk('public')->delete($storagePath);
        } elseif (file_exists(public_path(ltrim($image->image_url, '/')))) {
            @unlink(public_path(ltrim($image->image_url, '/')));
        }

        $image->delete();

        // Ensure at least one image is primary if images exist
        $remaining = ProductImage::where('product_id', $productId)->orderBy('sort_order')->get();
        if ($remaining->isNotEmpty()) {
            if ($wasPrimary || ! $remaining->contains('is_primary', true)) {
                $remaining[0]->update(['is_primary' => true, 'is_secondary' => false]);
            }
            if ($remaining->count() > 1 && ! $remaining->contains('is_secondary', true)) {
                $secondaryCandidate = $remaining->where('is_primary', false)->first();
                if ($secondaryCandidate) {
                    $secondaryCandidate->update(['is_secondary' => true]);
                }
            }
        }

        return back()->with('success', 'Product image removed.');
    }

    public function deleteDocument($id)
    {
        $doc = ProductDocument::findOrFail($id);

        if (str_starts_with($doc->file_path, '/storage/')) {
            $storagePath = substr($doc->file_path, 9);
            Storage::disk('public')->delete($storagePath);
        } elseif (file_exists(public_path(ltrim($doc->file_path, '/')))) {
            @unlink(public_path(ltrim($doc->file_path, '/')));
        }

        $doc->delete();

        return back()->with('success', 'Product document removed.');
    }

    protected function processSpecifications(Request $request): ?string
    {
        if ($request->has('spec_keys') && is_array($request->spec_keys)) {
            $specs = [];
            $keys = $request->spec_keys;
            $values = $request->input('spec_values', []);
            foreach ($keys as $i => $k) {
                $k = trim((string) $k);
                $v = isset($values[$i]) ? trim((string) $values[$i]) : '';
                if ($k !== '' && $v !== '') {
                    $specs[$k] = $v;
                }
            }
            if (! empty($specs)) {
                return json_encode($specs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }
        }

        $raw = trim((string) $request->input('specifications', ''));
        if ($raw === '') {
            return null;
        }

        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            $clean = [];
            foreach ($decoded as $k => $v) {
                $k = trim((string) $k);
                $v = is_scalar($v) ? trim((string) $v) : '';
                if ($k !== '' && $v !== '') {
                    $clean[$k] = $v;
                }
            }

            return ! empty($clean) ? json_encode($clean, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null;
        }

        return $raw;
    }

    protected function processCareInstructions(Request $request): ?string
    {
        $care = $request->input('care_instructions');
        if ($care === null) {
            return null;
        }

        $clean = strip_tags((string) $care);
        $clean = preg_replace('/<\/?svg[^>]*>.*?<\/svg>/is', '', $clean);
        $clean = trim($clean);

        return $clean !== '' ? $clean : null;
    }
}
