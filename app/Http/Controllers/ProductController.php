<?php

namespace App\Http\Controllers;

use App\Models\Product as Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Response as Response;
use Illuminate\Support\Facades\DB;
use Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            
            'brand'       => 'required|max:255',
            'name'        => 'required|max:255',
            'description' => 'max:255',
            'image'       => 'sometimes|nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'categoryid'  => 'required|exists:categories,id',
            'quantity'    => 'required|min:0|max:10000',
            'price'       => 'required|min:1|max:10000',
            'discount'    => 'min:1|max:100',
        ]);
        if($validator->fails()){
            return Response::sendError('Validation Error.', $validator->errors());
        }
        $pathName = NULL;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $pathName = time().mt_rand(10000,99999).'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/images');
            $image->move($destinationPath, $pathName);
        }
        $product = new Product;
        $product->brand = $request->brand;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->image = $pathName;
        $product->categoryid = $request->categoryid;
        $product->quantity = $request->quantity;
        $product->price = $request->price;
        $product->discount = $request->discount;
        $product->save();
        return Response::sendResponse($product, 'Product created successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }
/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAll()
    {
        $product = Product::all();
        if(count($product) == 0)   return Response::sendError('Product not found.');
        else    return Response::sendResponse($product, 'Product retrieved successfully.');
    }

    public function showByCategory($categoryid, Request $request)
    {
        $product = DB::table('products')->where('categoryid', '=', $categoryid)->get();
        if(count($product) == 0)   return Response::sendError('Product not found.');
        else    return Response::sendResponse($product, 'Product retrieved successfully.');
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'brand'       => 'required|max:255',
            'name'        => 'required|max:255',
            'description' => 'max:255',
            'image'       => 'sometimes|nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'categoryid'  => 'required|exists:categories,id',
            'quantity'    => 'required|min:0|max:10000',
            'price'       => 'required|min:1|max:10000',
            'discount'    => 'min:1|max:100',
        ]);
        if($validator->fails()){
            return Response::sendError('Validation Error.', $validator->errors());
        }
        if($product->state == 0){
            return Response::sendError('Product was discontinued earlier');
        }
        $product = Product::find($id);
        if(empty($product)){
            return Response::sendError('Product not found');
        }
        else{
            $pathName = NULL;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $name = time().mt_rand(10000,99999).'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/images');
                $image->move($destinationPath, $name);
            }
            if($pathName == NULL && $product->image != NULL) ProductController::removeImage($product->image);
            $product->brand = $request->brand;
            $product->name = $request->name;
            $product->description = $request->description;
            $product->image = $pathName;
            $product->categoryid = $request->categoryid;
            $product->quantity = $request->quantity;
            $product->price = $request->price;
            $product->discount = $request->discount;
            $product->save();
            return Response::sendResponse($product, 'Product updated successfully.');
        }
    }

    public function addQuantity($id, Request $request){
        $validator = Validator::make($request->all(), [
            'quantity'    => 'required|min:0|max:10000',
        ]);
        if($validator->fails()){
            return Response::sendError('Validation Error.', $validator->errors());
        }
        if($product->state == 0){
            return Response::sendError('Product was discontinued earlier');
        }
        $product = Product::find($id);
        if(empty($product)){
            return Response::sendError('Product not found');
        }
        else{
            $product->quantity =  $request->quantity;
            $product->save();
            return Response::sendResponse($product, 'Quantity updated successfully');
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function addDiscount($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'discount'    => 'required|min:1|max:100',
        ]);
        if($validator->fails()){
            return Response::sendError('Validation Error.', $validator->errors());
        }
        if($product->state == 0){
            return Response::sendError('Product was discontinued earlier');
        }
        $product =  Product::find($id);
        if(empty($product)){
            return Response::sendError('Product not found');
        }
        else{
            $product->discount =  $request->discount;
            $product->save();
            return Response::sendResponse($product, 'Discount updated successfully');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function delete($id, Request $request)
    {
        $product =  Product::find($id);
        if(empty($product)){
            return Response::sendError('Product not found');
        }
        if($product->state == 0){
            return Response::sendError('Product was discontinued earlier');
        }
        else{
            $product->state = 0;
            $product->quantity = 0;
            $product->discount = 0;
            $product->save();
            return Response::sendResponse(null,'Product discontinued successfully.');
        }
    }
    
    private static function removeImage($name){
        File::delete('images/'.$name);
    }
}
