<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Job;
use App\User;
use App\Category;
use App\Review;
use DB;

class JobController extends Controller
{
    public function index (Request $request) {
        $header = $request->header('Authorization');
        $api_token = str_replace('Bearer ', '', $header);

        $user = User::where('api_token', '=', $api_token)->first();
        if(!$user) {
            return response()->json(['status' => 400, 'errors' => 'invalid token.'], 400);
        }

        $jobs_query = Job::join('users', 'users.id', '=', 'jobs.user_id')
                        ->with(['users', 'categories']);
        // 1 - filter by workdays
        if ($request->workdays) {
            $workdays = explode(',', $request->workdays);
            foreach($workdays as $index => $workday) {
                $index == 0 ?
                $jobs_query = $jobs_query->where('workdays', 'like', '%' . $workday . '%') : 
                $jobs_query = $jobs_query->orWhere('workdays', 'like', '%' . $workday . '%');
            }
        }
        // 2 - filter by workhours
        if ($request->workhours) {
            $jobs_query = $jobs_query->where('workhours', $request->workhours);
        }
        // 3 - filter by salary type and amount
        if ($request->salary_type) {
            $jobs_query = $jobs_query->where('salary_type', $request->salary_type);
        }
        if ($request->salary_amount) {
            $jobs_query = $jobs_query->where('salary_amount', '<=', $request->salary_amount);
        }
        // 4 - filter by period
        // if ($request->period) {
        //     switch ($request->period) {
        //         case 'days':
        //             $jobs_query = $jobs_query->where('end_date', '<=', DB::raw('DATEADD(day, 7, start_date)'));
        //             break;
        //         case 'weeks':
        //             $jobs_query = $jobs_query->where('end_date', '<=', DB::raw('DATEADD(week, 4, start_date)'));
        //             break;
                
        //         default:
        //             $jobs_query = $jobs_query->where('end_date', '>=', DB::raw('DATEADD(month, 1, start_date)'));
        //             break;
        //     }
        //     $jobs_query = $jobs_query->where('workhours', $request->workhours);
        // }

        // ========= Sort Job List ========= //
        if ($request->orderBy && $request->orderDirection) {
            switch($request->orderBy) {
                // sort 1 - order by distance
                case 'distance' :
                    $jobs_query = $jobs_query->orderBy(DB::raw('ABS(latitude - '.$user->latitude.') + ABS(longitude - '.$user->longitude.')'), $request->orderDirection);
                    break;
                // sort 2 - order by postion name
                case 'position_name' :
                    $jobs_query = $jobs_query->orderBy('job_name', $request->orderDirection);
                    break;
                // sort 3 - order by salary
                case 'salary' :
                    // $jobs_query = $jobs_query->orderBy('salary_amount', $request->orderDirection);
                    $jobs_query = $jobs_query->orderBy('salary_rate', $request->orderDirection);
                    break;
                // sort 4 - order by star rate
                case 'star_rate' :
                    $jobs_query = $jobs_query->orderBy('marks', $request->orderDirection);
                    break;
                // sort 5 - order by start date
                case 'start_date' :
                    $jobs_query = $jobs_query->orderBy('start_date', $request->orderDirection);
                    // $jobs_query = $jobs_query->latest('start_date');
                    break;
                // sort 6 - order by workhours
                case 'workhours' :
                    $jobs_query = $jobs_query->orderBy('workhours', $request->orderDirection);
                    break;
                // default - order by
                default : 
                    $jobs_query = $jobs_query->latest('jobs.created_at');
                    break;
            }
        }

        $jobs = $jobs_query->get(['jobs.id AS job_id', 'jobs.*', 'users.*']);

        $message = count($jobs) ? 'foras-success' : 'There is no posted job.';

        return response()->json(['status' => 200, 'message' => $message, 'data' => $jobs], 200);
    }

    // Create a new job
    public function create (Request $request) {
        $header = $request->header('Authorization');
        $api_token = str_replace('Bearer ', '', $header);

        $user = User::where('api_token', '=', $api_token)->first();
        if(!$user) {
            return response()->json(['status' => 400, 'errors' => 'invalid token.'], 400);
        }

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'job_name' => 'required',
                'category_id' => 'required',
                'location' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
                'workdays' => 'required',
                'workhours' => 'required',
                'salary_type' => 'required',
                'salary_amount' => 'required',
                'languages' => 'required',
                'job_details' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
            }

            $job = new Job();
            $job->user_id = $user->id;
            $job->job_name = $request->job_name;
            $job->category_id = $request->category_id;
            $job->location = $request->location;
            $job->start_date = substr($request->start_date, 0, 10);
            $job->end_date = substr($request->end_date, 0, 10);
            $job->workdays = $request->workdays;
            $job->workhours = $request->workhours;
            $job->salary_type = $request->salary_type;
            $job->salary_amount = $request->salary_amount;
            $job->salary_rate = $request->salary_type == 'ساعة' ? $request->salary_amount : ($request->salary_amount / (30 * 24));
            $job->languages = $request->language;
            $job->job_details = $request->job_details;
            $job->status = 'open';
            $job->save();

            return response()->json(['status' => 200, 'message' => 'Successfully posted.', 'data' => $job], 200);
        } else {
            return response()->json(['status' => 400, 'errors' => 'invalid method.'], 400);
        }
    }

    // Update a job
    public function update (Request $request, $id) {
        $header = $request->header('Authorization');
        $api_token = str_replace('Bearer ', '', $header);

        $user = User::where('api_token', '=', $api_token)->first();
        if(!$user) {
            return response()->json(['status' => 400, 'errors' => 'invalid token.'], 400);
        }

        if (!$id) {
            return response()->json(['status' => 400, 'errors' => 'invalid params.'], 400);
        }

        if ($request->isMethod('put')) {
            $validator = Validator::make($request->all(), [
                'job_name' => 'required',
                'category_id' => 'required',
                'location' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
                'workdays' => 'required',
                'workhours' => 'required',
                'salary_type' => 'required',
                'salary_amount' => 'required',
                'languages' => 'required',
                'job_details' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
            }

            $job = Job::find($id);
            $job->user_id = $user->id;
            $job->job_name = $request->job_name;
            $job->category_id = $request->category_id;
            $job->location = $request->location;
            $job->start_date = $request->start_date;
            $job->end_date = $request->end_date;
            $job->workdays = $request->workdays;
            $job->workhours = $request->workhours;
            $job->salary_type = $request->salary_type;
            $job->salary_amount = $request->salary_amount;
            $job->language = $request->language;
            $job->job_details = $request->job_details;
            $job->status = 'open';
            $job->save();

            return response()->json(['status' => 200, 'message' => 'Successfully updated.', 'data' => $job], 200);
        } else {
            return response()->json(['status' => 400, 'errors' => 'invalid method.'], 400);
        }
    }

    // Delete a job
    public function delete (Request $request, $id) {
        $header = $request->header('Authorization');
        $api_token = str_replace('Bearer ', '', $header);

        $user = User::where('api_token', '=', $api_token)->first();
        if(!$user) {
            return response()->json(['status' => 400, 'errors' => 'invalid token.'], 400);
        }

        if (!$id) {
            return response()->json(['status' => 400, 'errors' => 'invalid params.'], 400);
        }

        $job = Job::find($id);
        $job->delete();

        return response()->json(['status' => 200, 'message' => 'Successfully deleted.', 'data' => ''], 200);
    }

    // Get a job detail
    public function details (Request $request, $id) {
        $header = $request->header('Authorization');
        $api_token = str_replace('Bearer ', '', $header);

        $user = User::where('api_token', '=', $api_token)->first();
        if(!$user) {
            return response()->json(['status' => 400, 'errors' => 'invalid token.'], 400);
        }

        if (!$id) {
            return response()->json(['status' => 400, 'errors' => 'invalid params.'], 400);
        }

        $job = Job::with(['users', 'users.reviewsReceived', 'users.reviewsReceived.provider', 'users.reviewsReceived.receiver', 'categories'])->find($id);
        $data['job'] = $job;
        $data['similars'] = Job::with(['users', 'categories'])
                                ->where('category_id', $job->category_id)
                                ->take(4)
                                ->get();
        $data['reviews'] = Review::with(['provider', 'receiver'])
                                ->where('receiver_id', $job->user_id)
                                ->get();

        return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => $data], 200);
    }
}
