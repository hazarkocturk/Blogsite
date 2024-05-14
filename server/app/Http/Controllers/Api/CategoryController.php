<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category; // Import the Category model

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    public function show($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return response()->json($category);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:categories',
            ]);
            
            $category = Category::create($validatedData);
            return response()->json($category, 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 400);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $id,
            ]);
    
            $category = Category::findOrFail($id);
            $category->update($validatedData);
            return response()->json($category, 200);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 400);
        } catch (\Exception $e) {
        
            // Return an error response
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
    

    public function delete($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}
