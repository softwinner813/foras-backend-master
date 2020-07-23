<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Review;
use DB;
use Validator;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    //
    public function individuals(Request $request) {
        $header = $request->header('Authorization');
        $api_token = str_replace('Bearer ', '', $header);

        $user = User::where('api_token', '=', $api_token)->first();
        if(!$user) {
            return response()->json(['status' => 400, 'errors' => 'invalid token.'], 400);
        }

        $individuals_query = User::where('role', 'individual');
        if($request->search) {
            $individuals_query = $individuals_query->where('name', 'like', '%' . $request->search . '%');
            $individuals_query = $individuals_query->orWhere('email', 'like', '%' . $request->search . '%');
            $individuals_query = $individuals_query->orWhere('address', 'like', '%' . $request->search . '%');
            $individuals_query = $individuals_query->orWhere('city', 'like', '%' . $request->search . '%');
            $individuals_query = $individuals_query->orWhere('state', 'like', '%' . $request->search . '%');
            $individuals_query = $individuals_query->orWhere('country', 'like', '%' . $request->search . '%');
        }

        if($request->orderBy && $request->orderDirection) {
            switch($request->orderBy) {
                case 'distance' : 
                    $individuals_query = $individuals_query->orderBy(DB::raw('ABS(latitude - '.$user->latitude.') + ABS(longitude - '.$user->longitude.')'), $request->orderDirection);
                    break;
                case 'name' : 
                    $individuals_query = $individuals_query->orderBy('name', $request->orderDirection);
                    break;
                case 'hourly_rate' : 
                    $individuals_query = $individuals_query->orderBy('hourly_rate', $request->orderDirection);
                    break;
                case 'star_rate' : 
                    $individuals_query = $individuals_query->orderBy('marks', $request->orderDirection);
                    break;
            }
        }

        $individuals = $individuals_query->get();

        $message = count($individuals) ? 'foras-success' : 'There is no registered individual.';

        return response()->json(['status' => 200, 'message' => $message, 'data' => $individuals], 200);
    }

    public function corporates(Request $request) {
        $header = $request->header('Authorization');
        $api_token = str_replace('Bearer ', '', $header);

        $user = User::where('api_token', '=', $api_token)->first();
        if(!$user) {
            return response()->json(['status' => 400, 'errors' => 'invalid token.'], 400);
        }

        $corporates_query = User::where('role', 'corporate');
        if($request->search) {
            $corporates_query = $corporates_query->where('name', 'like', '%' . $request->search . '%');
            $corporates_query = $corporates_query->orWhere('email', 'like', '%' . $request->search . '%');
            $corporates_query = $corporates_query->orWhere('address', 'like', '%' . $request->search . '%');
            $corporates_query = $corporates_query->orWhere('city', 'like', '%' . $request->search . '%');
            $corporates_query = $corporates_query->orWhere('state', 'like', '%' . $request->search . '%');
            $corporates_query = $corporates_query->orWhere('country', 'like', '%' . $request->search . '%');
            $corporates_query = $corporates_query->orWhere('sector', 'like', '%' . $request->search . '%');
        }
        if ($request->orderBy && $request->orderDirection) {
            switch($request->orderBy) {
                case 'distance' : 
                    $corporates_query = $corporates_query->orderBy(DB::raw('ABS(latitude - '.$user->latitude.') + ABS(longitude - '.$user->longitude.')'), $request->orderDirection);
                    break;
                case 'city' : 
                    $corporates_query = $corporates_query->orderBy('city', $request->orderDirection);
                    break;
                case 'star_rate' : 
                    $corporates_query = $corporates_query->orderBy('marks', $request->orderDirection);
                    break;
            }
        }
        $corporates = $corporates_query->get();

        $message = count($corporates) ? 'foras-success' : 'There is no registered corporate.';

        return response()->json(['status' => 200, 'message' => $message, 'data' => $corporates], 200);
    }

    public function details(Request $request, $id) {
        $header = $request->header('Authorization');
        $api_token = str_replace('Bearer ', '', $header);

        $user = User::where('api_token', '=', $api_token)->first();
        if(!$user) {
            return response()->json(['status' => 400, 'errors' => 'invalid token.'], 400);
        }

        if (!$id) {
            return response()->json(['status' => 400, 'errors' => 'invalid params.'], 400);
        }

        $detail['user'] = User::with(['jobs', 'reviewsReceived', 'reviewsReceived.provider', 'reviewsReceived.receiver'])->find($id);
        $detail['reviews'] = Review::with(['provider', 'receiver'])->where('receiver_id', $id)->get();
        return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => $detail], 200);
    }

    public function profileUpdate(Request $request) {
        $header = $request->header('Authorization');
        $api_token = str_replace('Bearer ', '', $header);

        $user = User::where('api_token', '=', $api_token)->first();
        if(!$user) {
            return response()->json(['status' => 400, 'errors' => 'invalid token.'], 400);
        }
        
        if ($request->role == 'individual') {
            $validator = Validator::make($request->all(), [
                // 'logo' => 'required',
                'name' => 'required|string|max:255',
                'work_area' => 'required|string|min:1|max:255',
                'city' => 'required|string|min:1|max:191',
                'hourly_rate' => 'required|integer|min:1',
                'available_work_from_time' => 'required|string|min:1|max:255',
                'experience' => 'required|string|min:1|max:255',
                'languages' => 'required|string|min:1|max:255',
                'skills' => 'required|string|min:1|max:255',
                'about_me' => 'required|string|min:1',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                // 'logo' => 'required',
                'name' => 'required|string|max:255',
                'work_area' => 'required|string|min:1|max:255',
                'city' => 'required|string|min:1|max:191',
                'about_me' => 'required|string|min:1',
            ]);
        }

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
        }

        if (User::where('api_token', '!=', $api_token)->where('name', '=', $request->name)->first()) {
            return response()->json(['status' => 400, 'errors' => ["name" => ["The name has already been taken."]]], 400);
        }

        if ($request->file('logo')) {
            $file = $request->file('logo');
            $img_folder = 'public/images/profiles';
            $path = $request->file('logo')->store($img_folder);
            $user->logo = url('/') . Storage::url($path);
        }

        $user->name = $request->name;
        $user->work_area = $request->work_area;
        $user->city = $request->city;
        $user->hourly_rate = $request->hourly_rate;
        $user->available_work_from_time = $request->available_work_from_time;
        $user->experience = $request->experience;
        $user->languages = $request->languages;
        $user->skills = $request->skills;
        $user->about_me = $request->about_me;
        $user->save();

        $data = User::with(['jobs', 'favIndividuals', 'favCorporates', 'favJobs', 'reviewsReceived'])->find($user->id);

        return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => $data], 200);

    }

    public function profileUpload(Request $request) {
        $header = $request->header('Authorization');
        $api_token = str_replace('Bearer ', '', $header);

        $user = User::where('api_token', '=', $api_token)->first();
        if(!$user) {
            return response()->json(['status' => 400, 'errors' => 'invalid token.'], 400);
        }
        
        if ($request->file('logo')->isValid()) {
            $file = $request->file('logo');
            $img_folder = 'public/images/profiles';
            $path = $request->file('logo')->store($img_folder);
            $user->logo = url('/') . Storage::url($path);
            $user->save();

            $data = User::with(['jobs', 'favIndividuals', 'favCorporates', 'favJobs', 'reviewsReceived'])->find($user->id);

            return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => $data], 200);
        }

        return response()->json(['status' => 500, 'errors' => 'foras-failed'], 500);
    }

}
