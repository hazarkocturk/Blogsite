<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Services\UploadImageService;
use Illuminate\Support\Facades\Validator; 
use Illuminate\Support\Facades\DB;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::paginate(3);
        return response()->json(['data' => $blogs], 200);
    }

    public function show($id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json(['message' => 'Blog not found'], 404);
        }

        $this->incrementViewsAndLikes($blog);

        return response()->json(['data' => $blog], 200);
    }

    public function getBlogsByCategory($category_id)
    {
        $blogs = Blog::where('category_id', $category_id)->get();
        return response()->json(['data' => $blogs], 200);
    }

    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'category_id' => 'required|exists:categories,id',
    ]);

    if ($validator->fails()) {
        return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 400);
    }

    // Ensure $images is an array
    $images = $request->file('image');
    if ($images instanceof \Illuminate\Http\UploadedFile) {
        $images = [$images];
    }

    $uploadImageService = new UploadImageService();
    $uploadedImages = $uploadImageService->uploadMultipleImages($images);
    $request->merge(['image' => $uploadedImages]);

    $blog = Blog::create($request->all());
    return response()->json(['data' => $blog], 201);
}


public function update(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'title' => 'sometimes|required|string|max:255',
        'content' => 'sometimes|required|string',
        'category_id' => 'sometimes|required|exists:categories,id',
        'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048', // Example validation for image upload
    ]);

    if ($validator->fails()) {
        return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 400);
    }

    $blog = Blog::findOrFail($id);

    // Handle file upload if image is present in the request
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $uploadImageService = new UploadImageService();
        $uploadedImage = $uploadImageService->uploadSingleImage($image);
        $blog->image = $uploadedImage;
    }

    if ($request->has('title')) {
        $blog->title = $request->input('title');
    }

    if ($request->has('content')) {
        $blog->content = $request->input('content');
    }

    if ($request->has('category_id')) {
        $blog->category_id = $request->input('category_id');
    }

    $blog->save();

    return response()->json(['data' => $blog], 200);
}



    public function delete($id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json(['message' => 'Blog not found'], 404);
        }

        $blog->delete();

        // Reset auto-increment value
        $firstAvailableId = $this->getFirstAvailableId();
        DB::statement("ALTER TABLE blogs AUTO_INCREMENT = $firstAvailableId");

        return response()->json(null, 204);
    }

    private function incrementViewsAndLikes($blog)
    {
        $blog->increment('views');
        $blog->increment('likes');
    }

    private function getFirstAvailableId()
    {
        $ids = DB::table('blogs')->pluck('id')->toArray();
        sort($ids);
        $expectedId = 1;

        foreach ($ids as $id) {
            if ($id != $expectedId) {
                return $expectedId;
            }
            $expectedId++;
        }

        return $expectedId;
    }
}
