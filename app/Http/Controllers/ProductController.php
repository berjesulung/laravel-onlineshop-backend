<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{
    //index
    public function index(Request $request)
    {
        $products = \App\Models\Product::paginate(5);
        return view('pages.product.index', compact('products'));

    }
    //create
    public function create()
    {
        $categories = \App\Models\Category::all();
        return view('pages.product.create', compact('categories'));
    }
    //store
    public function store(Request $request)
    {
        $filename = time() . '.' . $request->image->extension();
        $request->image->storeAs('public/products', $filename);
        // $data = $request->all();

        $product = new \App\Models\Product;
        $product->name = $request->name;
        $product->price = (int) $request->price;
        $product->stock = (int) $request->stock;
        $product->category_id = $request->category_id;
        $product->image = $filename;
        $product->save();

        return redirect()->route('product.index')->with('success', 'Category added successfully');
    }
    //edit
    public function edit($id)
    {
        $product = \App\Models\Product::findOrFail($id);
        $categories = \App\Models\Category::all();
        return view('pages.product.edit', compact('product', 'categories'));
    }

    //update
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);

        $product = \App\Models\Product::findOrFail($id);
        if ($request->hasFile('image')) {
            Storage::delete('public/products/' . $product->image);
            $filename = time() . '.' . $request->image->extension();
            $request->image->storeAs('public/products', $filename);
            $data = $request->except('image');
            $data['image'] = $filename;
            $product->update($data);

        } else {
            $data = $request->all();
            $product->update($data);
        }
        return redirect()->route('product.index')->with('success', 'Category updated successfully');
        // return redirect()->route('user.index');


    }

    //destroy
    public function destroy($id)
    {
        $product = \App\Models\Product::findOrFail($id);
        Storage::delete('public/products/' . $product->image);
        $product->delete();
        return redirect()->route('product.index')->with('success', 'Category deleted successfully');
    }
}
