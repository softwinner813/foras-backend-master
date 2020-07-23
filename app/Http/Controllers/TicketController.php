<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Ticket;

class TicketController extends Controller
{
    //
    public function create(Request $request) {
    	if ($request->isMethod('post')) {
			$validator = Validator::make($request->all(), [
                'name' => 'required|string|max:50',
                'email' => 'required|string|email|max:55',
                'subject' => 'required|string|max:50',
                'message' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
            }
            $ticket = new Ticket();
            $ticket->name = $request->name;
            $ticket->email = $request->email;
            $ticket->subject = $request->subject;
            $ticket->message = $request->message;
            $ticket->save();

            return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => $ticket], 200);
		}
    }
}
