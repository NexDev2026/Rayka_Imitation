<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeBanner;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminBannerController extends Controller
{
    public function index()
    {
        $banners = HomeBanner::orderBy('sort_order', 'asc')->get();

        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        $nextSortOrder = (HomeBanner::max('sort_order') ?? 0) + 1;

        return view('admin.banners.create', compact('nextSortOrder'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'badge_text' => 'nullable|string|max:100',
            'button_text' => 'required|string|max:100',
            'button_link' => 'required|string|max:255',
            'banner_image' => 'required|file|mimes:jpeg,png,jpg,webp,svg,gif|max:10240',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ], [
            'title.required' => 'Banner headline is required.',
            'banner_image.required' => 'Please select a hero banner image to upload.',
            'banner_image.max' => 'The banner image must not exceed 10MB.',
            'banner_image.mimes' => 'The banner image must be a JPEG, PNG, JPG, WebP, SVG, or GIF file.',
        ]);

        $imagePath = '/images/banners/banner-1.svg';
        $bannerDir = public_path('uploads/banners');
        if (! file_exists($bannerDir)) {
            mkdir($bannerDir, 0777, true);
        }

        if ($request->hasFile('banner_image')) {
            $imagePath = ImageUploadService::uploadAndConvertToWebp(
                file: $request->file('banner_image'),
                folder: 'uploads/banners',
                prefix: 'banner',
                maxWidth: 1920,
                maxHeight: 1080,
                quality: 82
            );
        }

        HomeBanner::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'badge_text' => $request->badge_text,
            'button_text' => $request->button_text,
            'button_link' => $request->button_link,
            'image_url' => $imagePath,
            'sort_order' => (int) ($request->sort_order ?: 0),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner created successfully!');
    }

    public function edit($id)
    {
        $banner = HomeBanner::findOrFail($id);

        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, $id)
    {
        $banner = HomeBanner::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'badge_text' => 'nullable|string|max:100',
            'button_text' => 'required|string|max:100',
            'button_link' => 'required|string|max:255',
            'banner_image' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg,gif|max:10240',
            'sort_order' => 'nullable|integer',
        ], [
            'title.required' => 'Banner headline is required.',
            'banner_image.max' => 'The banner image must not exceed 10MB.',
            'banner_image.mimes' => 'The banner image must be a JPEG, PNG, JPG, WebP, SVG, or GIF file.',
        ]);

        $bannerDir = public_path('uploads/banners');
        if (! file_exists($bannerDir)) {
            mkdir($bannerDir, 0777, true);
        }

        if ($request->hasFile('banner_image')) {
            $banner->image_url = ImageUploadService::uploadAndConvertToWebp(
                file: $request->file('banner_image'),
                folder: 'uploads/banners',
                prefix: 'banner',
                maxWidth: 1920,
                maxHeight: 1080,
                quality: 82
            );
        }

        $banner->title = $request->title;
        $banner->subtitle = $request->subtitle;
        $banner->badge_text = $request->badge_text;
        $banner->button_text = $request->button_text;
        $banner->button_link = $request->button_link;
        $banner->sort_order = (int) ($request->sort_order ?: 0);
        $banner->is_active = $request->boolean('is_active');
        $banner->save();

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully!');
    }

    public function destroy($id)
    {
        $banner = HomeBanner::findOrFail($id);
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted successfully!');
    }

    public function toggle($id)
    {
        $banner = HomeBanner::findOrFail($id);
        $banner->is_active = ! $banner->is_active;
        $banner->save();

        return back()->with('success', 'Banner status toggled.');
    }
}
