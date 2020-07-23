<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;

class CategoryController extends Controller
{
    // Get category list
    public function index (Request $request) {
        $categories = Category::all();
        $message = count($categories) ? 'foras-success' : 'There is no registered category.';

        return response()->json(['status' => 200, 'message' => $message, 'data' => $categories], 200);
    }

    public function create (Request $request) {
    	$header = $request->header('Authorization');
        $api_token = str_replace('Bearer ', '', $header);

        $user = User::where('api_token', '=', $api_token)->first();
        if(!$user) {
            return response()->json(['status' => 400, 'errors' => 'invalid token.'], 400);
        }

        $validator = Validator::make($request->all(), [
            'category_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
        }

        $category = new Category();
        $category->category_name;
        $category->save();

        $categories = Category::all();

        return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => $categories]);
    }
}
