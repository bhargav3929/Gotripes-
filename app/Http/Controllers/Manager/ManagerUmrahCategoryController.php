<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\UmrahCategory;
use Illuminate\Http\Request;

class ManagerUmrahCategoryController extends Controller
{
    public function index()
    {
        $categories = UmrahCategory::orderBy('created_at', 'desc')->paginate(20);
        return view('manager.umrah.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('manager.umrah.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['isActive'] = $request->has('isActive') ? 1 : 0;
        
        UmrahCategory::create($data);

        return redirect()->route('manager.umrah.categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(UmrahCategory $category)
    {
        return view('manager.umrah.categories.edit', compact('category'));
    }

    public function update(Request $request, UmrahCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['isActive'] = $request->has('isActive') ? 1 : 0;
        
        $category->update($data);

        return redirect()->route('manager.umrah.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(UmrahCategory $category)
    {
        $category->delete();
        return redirect()->route('manager.umrah.categories.index')->with('success', 'Category deleted successfully.');
    }
}
