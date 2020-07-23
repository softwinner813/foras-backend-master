<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\AdminUser;
use App\User;
use App\Job;
use App\Notification;
use App\Message;
use App\Ticket;

class AdminController extends Controller
{
    // admin login
	public function login(Request $request) {
		if ($request->isMethod('post')) {
			$validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255',
                'password' => 'required|string|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
            }

            $admin = AdminUser::where('email', $request->email)->where('password', $request->password)->first();

            if(!$admin) {
            	return response()->json(['status' => 400, 'errors' => 'Does not exist administrator.'], 400);
            }

            return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => $admin], 200);
		}
	}
	// individuals management
	public function getIndividuals(Request $request) {
		if ($request->isMethod('post')) {
            $header = $request->header('Authorization');
            $api_token = str_replace('Bearer ', '', $header);

            $admin = AdminUser::where('api_token', '=', $api_token)->first();

            if(!$admin) {
            	return response()->json(['status' => 400, 'errors' => 'Invalid errors'], 400);
            }



            $individuals_query = User::where('role', 'individual');
            if ($request->search) {
                $individuals_query = $individuals_query->Where('name', 'like', '%'. $request->search . '%');
                $individuals_query = $individuals_query->orWhere('email', 'like', '%'. $request->search . '%');
                $individuals_query = $individuals_query->orWhere('address', 'like', '%'. $request->search . '%');
                $individuals_query = $individuals_query->orWhere('city', 'like', '%'. $request->search . '%');
                $individuals_query = $individuals_query->orWhere('country', 'like', '%'. $request->search . '%');
                $individuals_query = $individuals_query->orWhere('gender', 'like', '%'. $request->search . '%');
                $individuals_query = $individuals_query->orWhere('phone', 'like', '%'. $request->search . '%');
                $individuals_query = $individuals_query->orWhere('hourly_rate', 'like', '%'. $request->search . '%');
                $individuals_query = $individuals_query->orWhere('registered_by', 'like', '%'. $request->search . '%');
            }
            $individuals = $individuals_query->get();
                

            return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => $individuals], 200);
        }
	}
	// corporates management
	public function getCorporates(Request $request) {
		if ($request->isMethod('post')) {
            $header = $request->header('Authorization');
            $api_token = str_replace('Bearer ', '', $header);

            $admin = AdminUser::where('api_token', '=', $api_token)->first();

            if(!$admin) {
            	return response()->json(['status' => 400, 'errors' => 'Invalid errors'], 400);
            }

            $corporates_query = User::where('role', 'corporate');
            if ($request->search) {
                $corporates_query = $corporates_query->Where('name', 'like', '%'. $request->search . '%');
                $corporates_query = $corporates_query->orWhere('email', 'like', '%'. $request->search . '%');
                $corporates_query = $corporates_query->orWhere('address', 'like', '%'. $request->search . '%');
                $corporates_query = $corporates_query->orWhere('city', 'like', '%'. $request->search . '%');
                $corporates_query = $corporates_query->orWhere('country', 'like', '%'. $request->search . '%');
                $corporates_query = $corporates_query->orWhere('gender', 'like', '%'. $request->search . '%');
                $corporates_query = $corporates_query->orWhere('phone', 'like', '%'. $request->search . '%');
                $corporates_query = $corporates_query->orWhere('commercial_registration', 'like', '%'. $request->search . '%');
                $corporates_query = $corporates_query->orWhere('sector', 'like', '%'. $request->search . '%');
            }
            $corporates = $corporates_query->get();

            return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => $corporates], 200);
        }
	}
	// update user permission
	public function updateUserPemission(Request $request) {
		if ($request->isMethod('post')) {
            $header = $request->header('Authorization');
            $api_token = str_replace('Bearer ', '', $header);

            $admin = AdminUser::where('api_token', '=', $api_token)->first();

            if(!$admin) {
            	return response()->json(['status' => 400, 'errors' => 'Invalid errors'], 400);
            }

            $validator = Validator::make($request->all(), [
                'permission' => 'required',
                'id' => 'required',
                'role' => 'required',
            ]);
            
            if ($validator->fails()) {
                return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
            }

            $user = User::find($request->id);
            $user->permission = $request->permission;
            $user->save();

            $data = User::where('role', $request->role)->get();

            return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => $data], 200);
        }
	}
	// jobs management
	public function getJobs(Request $request) {
		if ($request->isMethod('post')) {
            $header = $request->header('Authorization');
            $api_token = str_replace('Bearer ', '', $header);

            $admin = AdminUser::where('api_token', '=', $api_token)->first();
            
            if(!$admin) {
            	return response()->json(['status' => 400, 'errors' => 'Invalid errors'], 400);
            }

            $jobs_query = Job::with(['users', 'categories']);
            if($request->search) {
                $jobs_query->where('job_name', 'like', '%' . $request->search . '%');
                $jobs_query->orWhere('location', 'like', '%' . $request->search . '%');
                $jobs_query->orWhere('start_date', 'like', '%' . $request->search . '%');
                $jobs_query->orWhere('end_date', 'like', '%' . $request->search . '%');
                $jobs_query->orWhere('workdays', 'like', '%' . $request->search . '%');
                $jobs_query->orWhere('workhours', 'like', '%' . $request->search . '%');
                $jobs_query->orWhere('salary_type', 'like', '%' . $request->search . '%');
                $jobs_query->orWhere('salary_amount', 'like', '%' . $request->search . '%');
                $jobs_query->orWhere('languages', 'like', '%' . $request->search . '%');
                $jobs_query->orWhere('job_details', 'like', '%' . $request->search . '%');
            }
            $jobs = $jobs_query->get();

            return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => $jobs], 200);
        }
	}
	// update job status
	public function updateJobStatus(Request $request) {
		if ($request->isMethod('post')) {
            $header = $request->header('Authorization');
            $api_token = str_replace('Bearer ', '', $header);

            $admin = AdminUser::where('api_token', '=', $api_token)->first();

            if(!$admin) {
            	return response()->json(['status' => 400, 'errors' => 'Invalid errors'], 400);
            }

            $validator = Validator::make($request->all(), [
                'status' => 'required',
                'id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
            }

            $job = Job::find($request->id);
            $job->status = $request->status;
            $job->save();

            $jobs = Job::with(['users', 'categories'])->get();

            return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => $jobs], 200);
        }
	}
	// notifications management
	public function getNotifications(Request $request) {
		if ($request->isMethod('post')) {
            $header = $request->header('Authorization');
            $api_token = str_replace('Bearer ', '', $header);

            $admin = AdminUser::where('api_token', '=', $api_token)->first();
            
            if(!$admin) {
            	return response()->json(['status' => 400, 'errors' => 'Invalid errors'], 400);
            }

            if(!$request->search) {
                $notifications = Notification::all();
            } else {
                $notifications = Notification::where('type', 'like', '%'. $request->search . '%')
                                            ->orWhere('message', 'like', '%'. $request->search . '%')
                                            ->orWhere('created_by', 'like', '%'. $request->search . '%')
                                            ->get();
            }
            

            return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => $notifications], 200);
        }
	}
	// new notification
	public function newNotification(Request $request) {
		if ($request->isMethod('post')) {
            $header = $request->header('Authorization');
            $api_token = str_replace('Bearer ', '', $header);

            $admin = AdminUser::where('api_token', '=', $api_token)->first();

            if(!$admin) {
            	return response()->json(['status' => 400, 'errors' => 'Invalid errors'], 400);
            }

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

            $notifications = Notification::all();

            return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => $notifications], 200);
        }
	}
	// messages management
	public function getMessages(Request $request) {
		if ($request->isMethod('post')) {
            $header = $request->header('Authorization');
            $api_token = str_replace('Bearer ', '', $header);

            $admin = AdminUser::where('api_token', '=', $api_token)->first();
            
            if(!$admin) {
            	return response()->json(['status' => 400, 'errors' => 'Invalid errors'], 400);
            }

            $messages_query = Message::with(['sender', 'receiver']);
            if($request->search) {
                $messages_query->where('subject', 'like', '%'. $request->search .'%');
                $messages_query->orWhere('message', 'like', '%'. $request->search .'%');
            }
            $messages = $messages_query->get();

            return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => $messages], 200);
        }
	}
	// tickets management
	public function getTickets(Request $request) {
		if ($request->isMethod('post')) {
            $header = $request->header('Authorization');
            $api_token = str_replace('Bearer ', '', $header);

            $admin = AdminUser::where('api_token', '=', $api_token)->first();
            
            if(!$admin) {
            	return response()->json(['status' => 400, 'errors' => 'Invalid errors'], 400);
            }

            if(!$request->search) {
                $tickets = Ticket::all();
            } else {
                $tickets = Ticket::where('name', 'like', '%'. $request->search .'%')
                                ->orWhere('email', 'like', '%'. $request->search .'%')
                                ->orWhere('subject', 'like', '%'. $request->search .'%')
                                ->orWhere('message', 'like', '%'. $request->search .'%')
                                ->get();
            }

            return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => $tickets], 200);
        }
	}
}
