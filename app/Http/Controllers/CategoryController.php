<?php

namespace App\Http\Controllers;

use App\Models\Category as Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Response as Response;
use Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAll()
    {
        $category = Category::all();
        if(count($category) == 0)   return Response::sendError('Category not found.');
        else    return Response::sendResponse($category, 'Category retrieved successfully.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // dd($request);
        $validator = Validator::make($request->all(), [
            'name'          => 'required|max:255',
            'description'   => 'max:255',
            'image'         => 'sometimes|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if($validator->fails()){
            return Response::sendError('Validation Error.', $validator->errors());
        }
        $category = Category::where('name', $request->name)->get();
        if(count($category)>1){
            return Response::sendError('Category exists.', $request->name);
        }
        $pathName = NULL;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $pathName = time().mt_rand(10000,99999).'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/images');
            $image->move($destinationPath, $pathName);
        }
        $category = new Category;
        $category->name = $request->name;
        $category->description = $request->description;
        $category->image = $pathName;
        $category->save();
        return Response::sendResponse($category, 'Category created successfully.');
    
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
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        //
    }
}
