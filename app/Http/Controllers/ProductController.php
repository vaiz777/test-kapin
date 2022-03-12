<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(10);
        return view('product.index', compact('products'));
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */

    public function store(Request $request)
    {
        $this->validate($request, [
            'image'     => 'required|image|mimes:png,jpg,jpeg',
            'title'     => 'required',
            'price'     => 'required'
        ]);

        $image = $request->file('image');
        $image->storeAs('public/products', $image->hashName());

        $products = Product::create([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'price'     => $request->price
        ]);

        if ($products) {
            //redirect dengan pesan sukses
            return redirect()->route('product.index')->with(['success' => 'Data Berhasil Disimpan!']);
        } else {
            //redirect dengan pesan error
            return redirect()->route('product.index')->with(['error' => 'Data Gagal Disimpan!']);
        }
    }

    public function create()
    {
        return view('product.create');
    }

    /**
     * edit
     *
     * @param  mixed $blog
     * @return void
     */
    public function edit(Product $product)
    {
        return view('product.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $this->validate($request, [
            'title'     => 'required',
            'price'     => 'required'
        ]);

        //get data Blog by ID
        $product = Product::findOrFail($product->id);

        if ($request->file('image') == "") {

            $product->update([
                'title'     => $request->title,
                'price'     => $request->price
            ]);
        } else {

            //hapus old image
            Storage::disk('local')->delete('public/products/' . $product->image);

            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/products', $image->hashName());

            $product->update([
                'image'     => $image->hashName(),
                'title'     => $request->title,
                'price'     => $request->price
            ]);
        }

        if ($product) {
            return redirect()->route('product.index')->with(['success' => 'Data Berhasil Diupdate!']);
        } else {
            return redirect()->route('product.index')->with(['error' => 'Data Gagal Diupdate!']);
        }
    }

    /**
     * destroy
     *
     * @param  mixed $id
     * @return void
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        Storage::disk('local')->delete('public/products/' . $product->image);
        $product->delete();

        if ($product) {
            //redirect dengan pesan sukses
            return redirect()->route('product.index')->with(['success' => 'Data Berhasil Dihapus!']);
        } else {
            //redirect dengan pesan error
            return redirect()->route('product.index')->with(['error' => 'Data Gagal Dihapus!']);
        }
    }
}
