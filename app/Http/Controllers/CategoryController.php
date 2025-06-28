<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::paginate(10);
        return Inertia::render('categories/Index', [
            'categories' => $categories
        ]);
    }

    public function create()
    {
        // Only admin can create categories
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Only administrators can create categories.');
        }

        return Inertia::render('categories/Create');
    }

    public function store(Request $request)
    {
        // Only admin can store categories
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Only administrators can create categories.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        $data = ['name' => $validated['name']];

        // Handle image upload
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imagePath = $imageFile->store('categories', 'public');
            $data['image'] = $imagePath;
        }

        Category::create($data);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        // Only admin can edit categories
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Only administrators can edit categories.');
        }

        return Inertia::render('categories/Edit', [
            'category' => $category
        ]);
    }

    public function update(Request $request, Category $category)
    {
        // Only admin can update categories
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Only administrators can update categories.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        $data = ['name' => $validated['name']];

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $imageFile = $request->file('image');
            $imagePath = $imageFile->store('categories', 'public');
            $data['image'] = $imagePath;
        }

        $category->update($data);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        // Only admin can delete categories
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Only administrators can delete categories.');
        }

        // Delete associated image if exists
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
