<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\FavoriteJob;
use App\FavoriteIndividual;
use App\FavoriteCorporate;
use App\User;

class FavoriteController extends Controller
{
    // // Get favorite job list
    // public function index (Request $request) {
    //     $header = $request->header('Authorization');
    //     $api_token = str_replace('Bearer ', '', $header);

    //     $user = User::where('api_token', '=', $api_token)->first();
    //     if(!$user) {
    //         return response()->json(['status' => 400, 'errors' => 'invalid token.'], 400);
    //     }

    //     $favorites = Favorite::with('jobs')
    //                         ->where('user_id', $user->id)
    //                         ->get();

    //     return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => $favorites], 200);
    // }

    // =================================================================== //
    // Get favorite individual list
    public function favIndividuals (Request $request) {
        $header = $request->header('Authorization');
        $api_token = str_replace('Bearer ', '', $header);

        $user = User::where('api_token', '=', $api_token)->first();
        if(!$user) {
            return response()->json(['status' => 400, 'errors' => 'invalid token.'], 400);
        }

        $fav_individuals = FavoriteIndividual::with('individuals')
                                                ->where('user_id', $user->id)
                                                ->get();

        return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => $fav_individuals], 200);
    }

    // toggle favorite individual
    public function toggleFavIndividual (Request $request) {
        $header = $request->header('Authorization');
        $api_token = str_replace('Bearer ', '', $header);

        $user = User::where('api_token', '=', $api_token)->first();
        if(!$user) {
            return response()->json(['status' => 400, 'errors' => 'invalid token.'], 400);
        }

        $validator = Validator::make($request->all(), [
            'individual_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
        }

        $favorite = FavoriteIndividual::where('user_id', $user->id)
                                        ->where('individual_id', $request->individual_id)
                                        ->first();

        if (!$favorite) {
            FavoriteIndividual::create([
                'user_id' => $user->id,
                'individual_id' => $request->individual_id,
            ]);
        } else {
            $favorite->delete();
        }

        $data = User::with(['favIndividuals', 'favIndividuals.individuals', 'favJobs', 'favJobs.jobs'])->find($user->id);
        
        return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => $data], 200);
    }

    // ================================================================ //
    // Get favorite corporate list
    public function favCorporates (Request $request) {
        $header = $request->header('Authorization');
        $api_token = str_replace('Bearer ', '', $header);

        $user = User::where('api_token', '=', $api_token)->first();
        if(!$user) {
            return response()->json(['status' => 400, 'errors' => 'invalid token.'], 400);
        }

        $fav_corporates = FavoriteCorporate::with('corporates')
                                                ->where('user_id', $user->id)
                                                ->get();

        return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => $fav_corporates], 200);
    }

    // toggle favorite corporate
    public function toggleFavCorporate (Request $request) {
        $header = $request->header('Authorization');
        $api_token = str_replace('Bearer ', '', $header);

        $user = User::where('api_token', '=', $api_token)->first();
        if(!$user) {
            return response()->json(['status' => 400, 'errors' => 'invalid token.'], 400);
        }

        $validator = Validator::make($request->all(), [
            'corporate_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
        }

        $favorite = FavoriteCorporate::where('user_id', $user->id)
                                        ->where('corporate_id', $request->corporate_id)
                                        ->first();

        if (!$favorite) {
            FavoriteCorporate::create([
                'user_id' => $user->id,
                'corporate_id' => $request->corporate_id,
            ]);
        } else {
            $favorite->delete();
        }

        $data = User::with(['favIndividuals', 'favIndividuals.individuals', 'favCorporates', 'favCorporates.corporates', 'favJobs', 'favJobs.jobs'])->find($user->id);
        
        return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => $data], 200);
    }

    // ==================================================================== //
    // Get favorite job list
    public function favJobs (Request $request) {
        $header = $request->header('Authorization');
        $api_token = str_replace('Bearer ', '', $header);

        $user = User::where('api_token', '=', $api_token)->first();
        if(!$user) {
            return response()->json(['status' => 400, 'errors' => 'invalid token.'], 400);
        }

        $favorite_jobs = FavoriteJob::with(['jobs', 'jobs.users'])
                                    ->where('user_id', $user->id)
                                    ->get();

        return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => $favorite_jobs], 200);
    }

    // toggle favorite job
    public function toggleFavJob (Request $request) {
        $header = $request->header('Authorization');
        $api_token = str_replace('Bearer ', '', $header);

        $user = User::where('api_token', '=', $api_token)->first();
        if(!$user) {
            return response()->json(['status' => 400, 'errors' => 'invalid token.'], 400);
        }

        $validator = Validator::make($request->all(), [
            'job_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
        }
        
        $favorite = FavoriteJob::where('user_id', $user->id)
                                ->where('job_id', $request->job_id)
                                ->first();
                                
        if (!$favorite) {
            FavoriteJob::create([
                'user_id' => $user->id,
                'job_id' => $request->job_id,
            ]);
        } else {
            $favorite->delete();
        }
        
        $data = User::with(['favJobs', 'favJobs.jobs', 'favIndividuals', 'favIndividuals.individuals'])->find($user->id);
        
        return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => $data], 200);
    }

    // // add a new job to favorite list
    // public function create (Request $request) {
    //     $header = $request->header('Authorization');
    //     $api_token = str_replace('Bearer ', '', $header);

    //     $user = User::where('api_token', '=', $api_token)->first();
    //     if(!$user) {
    //         return response()->json(['status' => 400, 'errors' => 'invalid token.'], 400);
    //     }

    //     $validator = Validator::make($request->all(), [
    //         'job_id' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
    //     }

    //     $favorite = Favorite::where('user_id', $user->id)
    //                         ->where('job_id', $request->job_id)
    //                         ->first();
    //     if (!$favorite) {
    //         Favorite::create([
    //             'user_id' => $user->id,
    //             'job_id' => $request->job_id,
    //         ]);
    //     }

    //     return response()->json(['status' => 200, 'message' => 'Successfully added to favorite list.', 'data' => ''], 200);
    // }

    // // delete a job from favorite list
    // public function delete (Request $request, $id) {
    //     $header = $request->header('Authorization');
    //     $api_token = str_replace('Bearer ', '', $header);

    //     $user = User::where('api_token', '=', $api_token)->first();
    //     if(!$user) {
    //         return response()->json(['status' => 400, 'errors' => 'invalid token.'], 400);
    //     }

    //     if (!$id) {
    //         return response()->json(['status' => 400, 'errors' => 'invalid params'], 400);
    //     }

    //     $favorite = Favorite::find($id);
    //     $favorite->delete();

    //     return response()->json(['status' => 200, 'message' => 'Successfully deleted.', 'data' => ''], 200);
    // }
}
