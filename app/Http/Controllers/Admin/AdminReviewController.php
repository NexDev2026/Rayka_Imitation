<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $query = Review::with('product');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $reviews = $query->latest()->paginate(15)->withQueryString();
        $pendingCount = Review::where('status', 'pending')->count();

        return view('admin.reviews.index', compact('reviews', 'pendingCount', 'status'));
    }

    public function updateStatus(Request $request, $id)
    {
        $review = Review::findOrFail($id);
        $request->validate([
            'status' => 'required|in:approved,rejected,pending',
        ]);

        $review->status = $request->status;
        $review->save();

        return back()->with('success', "Review marked as {$review->status}!");
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return back()->with('success', 'Review deleted.');
    }
}
