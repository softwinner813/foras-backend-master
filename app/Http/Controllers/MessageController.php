<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\User;
use App\Message;

class MessageController extends Controller
{
    //
    public function index(Request $request) {
        $header = $request->header('Authorization');
        $api_token = str_replace('Bearer ', '', $header);

        $user = User::where('api_token', '=', $api_token)->first();
        if(!$user) {
            return response()->json(['status' => 400, 'errors' => 'invalid token.'], 400);
        }

        $messages = Message::with(['sender', 'receiver'])->where('receiver_id', $user->id)->latest('created_at')->get();

        return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => $messages], 200);
    }

    public function create(Request $request) {
        $header = $request->header('Authorization');
        $api_token = str_replace('Bearer ', '', $header);

        $user = User::where('api_token', '=', $api_token)->first();
        if(!$user) {
            return response()->json(['status' => 400, 'errors' => 'invalid token.'], 400);
        }

        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
        }

        $message = new Message();
        $message->sender_id = $user->id;
        $message->receiver_id = $request->receiver_id;
        $message->job_id = $request->job_id ? $request->job_id : '';
        $message->type = $request->type ? $request->type : '';
        $message->subject = $request->subject ? $request->subject : '';
        $message->message = $request->message;
        $message->save();

        return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => ''], 200);
    }

    public function delete(Request $request, $id) {
        $header = $request->header('Authorization');
        $api_token = str_replace('Bearer ', '', $header);

        $user = User::where('api_token', '=', $api_token)->first();
        if(!$user) {
            return response()->json(['status' => 400, 'errors' => 'invalid token.'], 400);
        }

        $message = Message::find($id)->delete();

        return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => ''], 200);
    }
}
