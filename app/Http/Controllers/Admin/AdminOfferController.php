<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryOffer;
use Illuminate\Http\Request;

class AdminOfferController extends Controller
{
    public function index()
    {
        $offers = CategoryOffer::with(['categories', 'category'])->latest('starts_at')->paginate(15);
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.offers.index', compact('offers', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'discount_percentage' => 'required|numeric|min:1|max:99',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'badge_text' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $categoryIds = (array) $request->category_ids;

        $offer = CategoryOffer::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'category_id' => $categoryIds[0] ?? null,
            'discount_percentage' => $request->discount_percentage,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
            'badge_text' => $request->badge_text,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $offer->categories()->sync($categoryIds);

        return redirect()->route('admin.offers.index')->with('success', 'Timed multi-category offer campaign launched successfully!');
    }

    public function toggle($id)
    {
        $offer = CategoryOffer::findOrFail($id);
        $offer->update(['is_active' => ! $offer->is_active]);

        return back()->with('success', 'Offer status updated to '.($offer->is_active ? 'Active' : 'Disabled'));
    }

    public function destroy($id)
    {
        $offer = CategoryOffer::findOrFail($id);
        $offer->delete();

        return back()->with('success', 'Offer deleted successfully.');
    }
}
