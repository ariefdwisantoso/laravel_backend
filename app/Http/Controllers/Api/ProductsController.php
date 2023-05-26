<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use OpenApi\Annotations as OA;
use Laravel\Sanctum\Sanctum;


class ProductsController extends Controller
{

    /**
     * @OA\PathItem(
     *   path="/api/products",
     *   @OA\Get(
     *     tags={"Products"},
     *     summary="Get Products List",
     *     description="Get Products List",
     *     security={
     *       {"sanctum": {}}
     *     },
     *     @OA\Response(
     *       response=200,
     *       description="Success"
     *     ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found"
     *      ),
     *   )
     * )
    */

    public function index(Request $request)
    {
        Sanctum::actingAs($request->user());
        $product        = Product::all();        
        $appUrl         = url('/');
        $productData    = [];
        foreach ($product as $item) {
            $image = $item->image != null ? "$appUrl/storage/products/$item->image" : " ";
        
            $productData[] = [
                "id"            => $item->id,
                "name_products" => $item->name_products,
                "description"   => $item->description,
                "image"         => $image
            ];
        }
        return response()->json([
            "success"   => true,
            "message"   => "Product List",
            "data"      => $productData
        ]);
    }

    public function create()
    {
        //
    }

    /**
     * @OA\PathItem(
     *   path="/api/products",
     *   @OA\Post(
     *     tags={"Products"},
     *     summary="Post Products",
     *     description="Post Products",
     *     security={
     *         {"sanctum": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name_products", "description", "image"},
     *                 @OA\Property(
     *                     property="name_products",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="image",
     *                     type="file",
     *                     format="binary"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found"
     *     )
     *  )
     * )
    */
    public function store(Request $request)
    {
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
        $appUrl = url('/');
        return response()->json([
            "success"   => true,
            "message"   => "Product created successfully.",
            "data"      => [
                "id"            => $product->id,
                "name_products" => $product->name_products,
                "description"   => $product->description,
                "image"         => $product->image != null ? "$appUrl/storage/products/$product->image" : ""
            ]
        ]);
    }

   /**
     * @OA\PathItem(
     *   path="/api/products/{id}",
     *   @OA\Get(
     *     tags={"Products"},
     *     summary="Show Products By Id",
     *     description="Show Products By Id",
     *     security={
     *         {"sanctum": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the product",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found"
     *     )
     *   )
     * )
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
        $appUrl = url('/');
        return response()->json([
            "success"   => true,
            "message"   => "Product created successfully.",
            "data"      => [
                "id"            => $product->id,
                "name_products" => $product->name_products,
                "description"   => $product->description,
                "image"         => $product->image != null ? "$appUrl/storage/products/$product->image" : ""
            ]
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
     * @OA\PathItem(
     *   path="/api/products/{id}",
     * @OA\Post(
    *     operationId="updateProduct",
    *     tags={"Products"},
    *     summary="Update a product",
    *     description="Update a specific product by its ID",
    *     security={
    *         {"sanctum": {}}
    *     },
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         required=true,
    *         description="ID of the product to update",
    *         @OA\Schema(
    *             type="integer"
    *         )
    *     ),
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\MediaType(
    *             mediaType="multipart/form-data",
    *             @OA\Schema(
    *                 required={"name_products", "description", "image"},
    *                 @OA\Property(
    *                     property="name_products",
    *                     type="string"
    *                 ),
    *                 @OA\Property(
    *                     property="description",
    *                     type="string"
    *                 ),
    *                 @OA\Property(
    *                     property="image",
    *                     type="file",
    *                     format="binary"
    *                 )
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Success"
    *     ),
    *     @OA\Response(
    *         response=400,
    *         description="Bad Request"
    *     ),
    *     @OA\Response(
    *         response=401,
    *         description="Unauthenticated"
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Not found"
    *     )
    * )
     * )
    */
    
    public function update(Request $request, $id)
    {
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
        
        $product                = Product::findOrFail($id);
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
        $appUrl = url('/');
        return response()->json([
            "success"   => true,
            "message"   => "Product created successfully.",
            "data"      => [
                "id"            => $product->id,
                "name_products" => $product->name_products,
                "description"   => $product->description,
                "image"         => $product->image != null ? "$appUrl/storage/products/$product->image" : ""
            ]
        ]);
    }

    /**
     * @OA\PathItem(
     *   path="/api/products/{id}",
     *   @OA\Delete(
     *     tags={"Products"},
     *     summary="Delete Products",
     *     description="Delete Products",
     *     security={
     *         {"sanctum": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the product",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found"
     *     )
     *   )
     * )
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        $path = public_path("storage/products/{$product->image}");
        File::delete($path);
        $appUrl = url('/');

        return response()->json([
            "success"   => true,
            "message"   => "Product deleted successfully.",
            "data"      => [
                "id"            => $product->id,
                "name_products" => $product->name_products,
                "description"   => $product->description,
                "image"         => $product->image != null ? "$appUrl/storage/products/$product->image" : ""
            ]
        ]);
    }
}
