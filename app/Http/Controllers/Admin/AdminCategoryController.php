<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\NavGroup;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->with('navGroups')->orderBy('sort_order', 'asc')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $nextSortOrder = (Category::max('sort_order') ?? 0) + 1;
        $navGroups = NavGroup::where('is_active', true)->orderBy('sort_order', 'asc')->get();

        return view('admin.categories.create', compact('nextSortOrder', 'navGroups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:500',
            'image_file' => 'required|file|mimes:jpeg,png,jpg,webp,svg,gif|max:10240',
            'hero_file' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg,gif|max:10240',
            'sort_order' => 'nullable|integer',
            'nav_groups' => 'nullable|array',
            'nav_groups.*' => 'exists:nav_groups,id',
        ], [
            'name.required' => 'Category name is required.',
            'image_file.required' => 'Please upload a category circular tile image.',
            'image_file.max' => 'The category image must not exceed 10MB.',
            'image_file.mimes' => 'The category image must be a JPEG, PNG, JPG, WebP, SVG, or GIF file.',
            'image_file.uploaded' => 'The image file failed to upload (it may exceed the server\'s maximum upload limit).',
            'hero_file.max' => 'The hero banner image must not exceed 10MB.',
            'hero_file.mimes' => 'The hero banner image must be a JPEG, PNG, JPG, WebP, SVG, or GIF file.',
            'hero_file.uploaded' => 'The hero file failed to upload (it may exceed the server\'s maximum upload limit).',
        ]);

        $slug = Str::slug($request->name);
        $imagePath = '/images/categories/chains.svg';
        $heroPath = '/images/banners/banner-1.svg';

        $catDir = public_path('uploads/categories');
        if (! file_exists($catDir)) {
            mkdir($catDir, 0777, true);
        }

        if ($request->hasFile('image_file')) {
            $imagePath = ImageUploadService::uploadAndConvertToWebp(
                file: $request->file('image_file'),
                folder: 'uploads/categories',
                prefix: 'cat',
                maxWidth: 1000,
                maxHeight: 1000,
                quality: 82
            );
        }

        if ($request->hasFile('hero_file')) {
            $heroPath = ImageUploadService::uploadAndConvertToWebp(
                file: $request->file('hero_file'),
                folder: 'uploads/categories',
                prefix: 'hero',
                maxWidth: 1600,
                maxHeight: 1600,
                quality: 82
            );
        }

        $category = Category::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'image' => $imagePath,
            'hero_image' => $heroPath,
            'hero_title' => $request->hero_title ?: $request->name,
            'hero_subtitle' => $request->hero_subtitle,
            'sort_order' => (int) ($request->sort_order ?: 0),
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->has('nav_groups')) {
            $category->navGroups()->sync($request->input('nav_groups', []));
        }

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully!');
    }

    public function edit($id)
    {
        $category = Category::with('navGroups')->findOrFail($id);
        $navGroups = NavGroup::where('is_active', true)->orderBy('sort_order', 'asc')->get();

        return view('admin.categories.edit', compact('category', 'navGroups'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:500',
            'image_file' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg,gif|max:10240',
            'hero_file' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg,gif|max:10240',
            'sort_order' => 'nullable|integer',
            'nav_groups' => 'nullable|array',
            'nav_groups.*' => 'exists:nav_groups,id',
        ], [
            'name.required' => 'Category name is required.',
            'image_file.max' => 'The category image must not exceed 10MB.',
            'image_file.mimes' => 'The category image must be a JPEG, PNG, JPG, WebP, SVG, or GIF file.',
            'image_file.uploaded' => 'The image file failed to upload (it may exceed the server\'s maximum upload limit).',
            'hero_file.max' => 'The hero banner image must not exceed 10MB.',
            'hero_file.mimes' => 'The hero banner image must be a JPEG, PNG, JPG, WebP, SVG, or GIF file.',
            'hero_file.uploaded' => 'The hero file failed to upload (it may exceed the server\'s maximum upload limit).',
        ]);

        $catDir = public_path('uploads/categories');
        if (! file_exists($catDir)) {
            mkdir($catDir, 0777, true);
        }

        if ($request->hasFile('image_file')) {
            $category->image = ImageUploadService::uploadAndConvertToWebp(
                file: $request->file('image_file'),
                folder: 'uploads/categories',
                prefix: 'cat',
                maxWidth: 1000,
                maxHeight: 1000,
                quality: 82
            );
        }

        if ($request->hasFile('hero_file')) {
            $category->hero_image = ImageUploadService::uploadAndConvertToWebp(
                file: $request->file('hero_file'),
                folder: 'uploads/categories',
                prefix: 'hero',
                maxWidth: 1600,
                maxHeight: 1600,
                quality: 82
            );
        }

        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->description = $request->description;
        $category->hero_title = $request->hero_title;
        $category->hero_subtitle = $request->hero_subtitle;
        $category->sort_order = (int) ($request->sort_order ?: 0);
        $category->is_active = $request->boolean('is_active');
        $category->save();

        if ($request->has('nav_groups')) {
            $category->navGroups()->sync($request->input('nav_groups', []));
        } else {
            $category->navGroups()->sync([]);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully!');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully!');
    }
}
