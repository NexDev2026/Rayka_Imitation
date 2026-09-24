<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttributeGroup;
use App\Models\AttributeValue;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminAttributeController extends Controller
{
    public function index()
    {
        $groups = AttributeGroup::with(['values', 'categories'])->orderBy('sort_order', 'asc')->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $nextSortOrder = AttributeGroup::max('sort_order') + 1;

        return view('admin.attributes.index', compact('groups', 'categories', 'nextSortOrder'));
    }

    public function storeGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'categories' => 'nullable|array',
        ]);

        $group = AttributeGroup::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'sort_order' => AttributeGroup::count() + 1,
        ]);

        if ($request->filled('categories')) {
            $group->categories()->sync($request->categories);
        }

        return back()->with('success', "Filter group '{$group->name}' created successfully!");
    }

    public function storeValue(Request $request)
    {
        $request->validate([
            'attribute_group_id' => 'required|exists:attribute_groups,id',
            'value' => 'required|string|max:255',
        ]);

        AttributeValue::create([
            'attribute_group_id' => $request->attribute_group_id,
            'value' => $request->value,
        ]);

        return back()->with('success', 'Filter option value added successfully!');
    }

    public function updateGroupCategories(Request $request, $id)
    {
        $group = AttributeGroup::findOrFail($id);
        $group->categories()->sync($request->input('categories', []));

        return back()->with('success', "Assigned categories updated for '{$group->name}'!");
    }

    public function destroyValue($id)
    {
        $value = AttributeValue::findOrFail($id);
        $value->delete();

        return back()->with('success', 'Attribute value deleted.');
    }

    public function destroyGroup($id)
    {
        $group = AttributeGroup::findOrFail($id);
        $group->delete();

        return back()->with('success', 'Attribute group deleted.');
    }
}
