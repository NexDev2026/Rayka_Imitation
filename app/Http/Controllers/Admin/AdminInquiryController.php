<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;

class AdminInquiryController extends Controller
{
    public function index()
    {
        $inquiries = Inquiry::latest()->paginate(15);

        return view('admin.inquiries.index', compact('inquiries'));
    }

    public function update(Request $request, $id)
    {
        $inquiry = Inquiry::findOrFail($id);
        $request->validate([
            'status' => 'required|in:new,replied',
            'admin_notes' => 'nullable|string',
        ]);

        $inquiry->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ]);

        return back()->with('success', 'Inquiry updated successfully.');
    }

    public function destroy($id)
    {
        $inquiry = Inquiry::findOrFail($id);
        $inquiry->delete();

        return back()->with('success', 'Inquiry deleted.');
    }
}
