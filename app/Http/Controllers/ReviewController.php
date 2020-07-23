<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\User;
use App\Review;

class ReviewController extends Controller
{
    // Read a user reviews
    public function index(Request $request) {
        $header = $request->header('Authorization');
        $api_token = str_replace('Bearer ', '', $header);

        $user = User::where('api_token', '=', $api_token)->first();
        if(!$user) {
            return response()->json(['status' => 400, 'errors' => 'invalid token.'], 400);
        }

        $reviews = Review::with(['provider', 'receiver'])->where('receiver_id', $user->id)->get();

        return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => $reviews], 200);
    }
    // Create a new review
    public function create(Request $request) {
        $header = $request->header('Authorization');
        $api_token = str_replace('Bearer ', '', $header);

        $user = User::where('api_token', '=', $api_token)->first();
        if(!$user) {
            return response()->json(['status' => 400, 'errors' => 'invalid token.'], 400);
        }

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                // 'job_id' => 'required',
                'receiver_id' => 'required',
                'marks' => 'required',
                'comment' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
            }

            $review = Review::where('provider_id', $user->id)
                            ->where('receiver_id', $request->receiver_id)
                            ->first();
                            
            if($review) {
                return response()->json(['status' => 400, 'errors' => 'You have already left the review'], 400);    
            }

            $new_review = new Review();
            $new_review->provider_id = $user->id;
            $new_review->receiver_id = $request->receiver_id;
            $new_review->marks = $request->marks;
            $new_review->comment = $request->comment;
            $new_review->save();

            // update the average marks of user
            $user->marks = Review::where('receiver_id', $user->id)->avg('marks');
            $user->save();

            return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => ''], 200);
        } else {
            return response()->json(['status' => 400, 'errors' => 'wrong method'], 400);
        }
    }
}
