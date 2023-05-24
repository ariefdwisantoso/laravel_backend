<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        return response()->json([
            "success"   => true,
            "message"   => "Product List",
            "data"      => $products
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input      = $request->all();
        $validator  = Validator::make($request->all(), [
            'name_products' => 'required',
            'description'   => 'required',
            'image'         => 'required|mimes:jpg,png,jpeg,gif,svg',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => [],
                'message' => $validator->errors(),
                'success' => false
            ]);
        }
        $product                    = new Product();
        $product->name_products     = $request->name_products;
        $product->description       = $request->description;

        $ext                        = $request->file('image')->extension();
        $slug                       = Str::slug($product->name_products, '-');
        $slug                       = "$slug.$ext";
        $request->file('image')->move(public_path('storage/products'), $slug);

        $product->image             = $slug;
        $product->save();
        
        return response()->json([
            "success"   => true,
            "message"   => "Product created successfully.",
            "data"      => $product
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);
        if (is_null($product)) {
            return response()->json([
                'data' => [],
                'message' => "Data not found.",
                'success' => false
            ]);
        }
        return response()->json([
            "success" => true,
            "message" => "Product retrieved successfully.",
            "data" => $product
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name_products' => 'required',
            'description'   => 'required',
            'image'         => 'required|mimes:jpg,png,jpeg,gif,svg',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => [],
                'message' => $validator->errors(),
                'success' => false
            ]);
        }
        

        $product                = Product::find($id);
        $product->name_products = $request->name_products;
        $product->description   = $request->description;

        if ($request->hasFile('image')) {
            $path                       = public_path("storage/products/$product->image");
            File::delete($path);
            $ext                        = $request->file('image')->extension();
            $slug                       = Str::slug($product->name_products, '-');
            $slug                       = "$slug.$ext";
            $request->file('image')->move(public_path('storage/products'), $slug);

            $product->image             = $slug;
        }
        $product->save();


        return response()->json([
            "success"   => true,
            "message"   => "Product updated successfully.",
            "data"      => $product
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);
        $product->delete();
        return response()->json([
            "success"   => true,
            "message"   => "Product deleted successfully.",
            "data"      => $product
        ]);
    }
}
