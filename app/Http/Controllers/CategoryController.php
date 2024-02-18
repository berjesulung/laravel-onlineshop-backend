<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;

class CategoryController extends Controller
{
    //index
    public function index(Request $request)
    {
        $categories = \App\Models\Category::paginate(5);
        return view('pages.category.index', compact('categories'));
    }

    //create
    public function create()
    {
        return view('pages.category.create');
    }

    //store
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
        ]);

        $category = \App\Models\Category::create($validated);

        return redirect()->route('category.index')->with('success', 'Category created successfully');
    }

    //edit
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('pages.category.edit', compact('category'));
    }

    //update
    // public function update(Request $request, $id)
    // {
    //     $validated = $request->validate([
    //         'name' => 'required|max:100',
    //         'description' => 'required|max:255',

    //     ]);

    //     $category = \App\Models\Category::findOrFail($id);
    //     $category->update($validated);

    //     return redirect()->route('category.index')->with('success', 'Category updated successfully');
    // }
    public function update(Request $request, $id)
    {
        $request->validate([
                    'name' => 'required|max:100',
                    'description' => 'required|max:255',
                    'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ]);

        $category = \App\Models\Category::findOrFail($id);
        if ($request->hasFile('image')) {
            Storage::delete('public/categories/' . $category->image);
            $filename = time() . '.' . $request->image->extension();
            $request->image->storeAs('public/categories', $filename);
            $data = $request->except('image');
            $data['image'] = $filename;
            $category->update($data);

        } else {
            $data = $request->all();
            $category->update($data);
        }
        return redirect()->route('category.index')->with('success', 'Category updated successfully');
    }

    //destroy
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return redirect()->route('category.index')->with('success', 'Category deleted successfully');
    }
}
