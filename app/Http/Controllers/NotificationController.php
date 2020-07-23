<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notification;
use App\User;
use Validator;

class NotificationController extends Controller
{
    //
    public function index(Request $request) {
        $header = $request->header('Authorization');
        $api_token = str_replace('Bearer ', '', $header);

        $user = User::where('api_token', '=', $api_token)->first();
        if(!$user) {
            return response()->json(['status' => 400, 'errors' => 'invalid token.'], 400);
        }

        $notifications = Notification::all();

        return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => $notifications], 200);
    }

    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
        }

        $notification = new Notification();
        $notification->type = $request->type;
        $notification->message = $request->message;
        $notification->save();

        return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => ''], 200);
    }
}
