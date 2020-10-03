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
     * Create category
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
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
     * Edit category
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'name'          => 'required|max:255',
            'description'   => 'max:255',
            'image'         => 'sometimes|nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if($validator->fails()){
            return Response::sendError('Validation Error.', $validator->errors());
        }
        $category = Category::find($id);
        if(empty($category)){
            return Response::sendError('Category not found');
        }
        else{
            $name = NULL;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $name = time().mt_rand(10000,99999).'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/images');
                $image->move($destinationPath, $name);
            }
            if($name == NULL && $category->image != NULL) CategoryController::removeImage($category->image);
            $category->name = $request->name;
            $category->description = $request->description;
            $category->image = $name;
            $category->save();
            return Response::sendResponse($category, 'Product updated successfully.');
        }
    }
    /**
     * Remove the specified category
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $category = Category::find($id);
        //TODO - check if Product are in category
        // return Response::sendError('Change category in existed product');
        if(empty($category)){
            return Response::sendError('Category not found');
        }
        else{
            if(!empty($category->image))  CategoryController::removeImage($category->image);
            $category->forceDelete();
            return Response::sendResponse(null,'Category permanently deleted successfully.');
        }
    }
    private static function remogeImage($name){
        File::delete('images/'.$name);
    }
}
