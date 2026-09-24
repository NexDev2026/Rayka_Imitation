<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\NavGroup;
use Illuminate\Http\Request;

class AdminNavMenuController extends Controller
{
    public function index()
    {
        $navGroups = NavGroup::with(['categories' => function ($q) {
            $q->orderBy('category_nav_group.sort_order', 'asc');
        }])->orderBy('sort_order', 'asc')->get();

        $allCategories = Category::where('is_active', true)->orderBy('name', 'asc')->get();

        return view('admin.nav_menu.index', compact('navGroups', 'allCategories'));
    }

    public function update(Request $request, $id)
    {
        $navGroup = NavGroup::findOrFail($id);

        $categoryIds = $request->input('categories', []);
        $syncData = [];

        foreach ($categoryIds as $order => $catId) {
            $syncData[$catId] = ['sort_order' => $order + 1];
        }

        $navGroup->categories()->sync($syncData);

        return back()->with('success', "Mega-menu for '{$navGroup->name}' updated successfully!");
    }
}
