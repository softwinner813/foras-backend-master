<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Auth;
// use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyMail;
use Location;

class AuthController extends Controller
{
    // user login
    public function login(Request $request) {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255',
                'password' => 'required|string|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
            }
            
            $credentials = ['email' => $request->email, 'password' => $request->password];
            
            if (Auth::attempt($credentials, $request->remember)) {
                $data = User::with(['jobs', 'favIndividuals', 'favCorporates', 'favJobs', 'reviewsReceived'])->find(Auth::user()->id);
                return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => $data], 200);
            } else {
                return response()->json(['status' => 400, 'errors' => 'User does not exist.'], 400);
            }
        } else {
            return response()->json(['status' => 400, 'errors' => 'wrong method.'], 400);
        }
    }

    // user login by social
    public function loginBySocial(Request $request) {
        if ($request->isMethod('post')) {
            // === from web, linkedin login

            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255',
                'login_type' => 'required|string|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
            }
            
            $user = User::with(['jobs', 'favIndividuals', 'favCorporates', 'favJobs', 'reviewsReceived'])->where('email', $request->email)->first();

            if ($user) {
                if ($request->role) {
                    $new_user->role = $request->role;
                }
                $user->save();
                return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => $user], 200);
            }

            // register as new user by social account
            $register_validator = Validator::make($request->all(), [
                // 'role' => 'required|string|max:255',
                'name' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'login_type' => 'required|string|max:255',
            ]);

            if ($register_validator->fails()) {
                return response()->json(['status' => 400, 'errors' => $register_validator->errors()], 400);
            }

            $logo = $request->role == 'individual' ? 'https://foras.app/profile-images/default-individual.png' 
                                                    : 'https://foras.app/profile-images/default-corporate.png';

            $new_user = new User();
            if ($request->role) {
                $new_user->role = $request->role;
            }
            $new_user->name = $request->name;
            $new_user->email = $request->email;
            $new_user->first_name = $request->first_name ? $request->first_name : '';
            $new_user->last_name = $request->last_name ? $request->last_name : '';
            $new_user->logo = $request->logo ? $request->logo : $logo;
            $new_user->registered_by = $request->login_type;
            $new_user->api_token = Str::random(80);
            $new_user->password = Hash::make(Str::random(20));
            // $new_user->latitude = Location::get($request->ip()) ? Location::get($request->ip())->latitude : null;
            // $new_user->longitude = Location::get($request->ip()) ? Location::get($request->ip())->longitude : null;
            $new_user->save();

            $data = User::with(['jobs', 'favIndividuals', 'favCorporates', 'favJobs', 'reviewsReceived'])->find($new_user->id);

            return response()->json(['status' => 200, 'message' => $request->login_type, 'data' => $data], 200);

        } else {
            return response()->json(['status' => 400, 'errors' => 'wrong method.'], 400);
        }
    }

    // user logout
    public function logout(Request $request) {
        if ($request->isMethod('post')) {
            $header = $request->header('Authorization');
            $api_token = str_replace('Bearer ', '', $header);

            if (Auth::check() && Auth::user()->api_token == $api_token) {
                Auth::logout();
                $data = Auth::user();
                return response()->json(['status' => 200, 'message' => 'Successfully loged out.', 'data' => $data], 200);
            } else {
                return response()->json(['status' => 400, 'message' => 'Invalide Token.'], 400);
            }
        } else {
            return response()->json(['status' => 400, 'errors' => 'wrong method.'], 400);
        }
    }

    // User Register 1
    public function register(Request $request) {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'role' => 'required|string|max:255',
                'name' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:1|confirmed',
            ]);
            
            if ($validator->fails()) {
                return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
            }

            $logo = $request->role == 'individual' ? 'https://foras.app/profile-images/default-individual.png' 
                                                    : 'https://foras.app/profile-images/default-corporate.png';
    
            $user = User::forceCreate([
                'role' => $request->role,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'api_token' => Str::random(80),
                'logo' => $request->logo ? $request->logo : $logo,
            ]);
    
            if (!$user) {
                return response()->json(['status' => 500, 'errors' => 'Internal Server Error.'], 500);
            }
            
            $credentials = ['email' => $request->email, 'password' => $request->password];
            
            if (Auth::attempt($credentials, $request->remember)) {
                $data = ['email' => $user->email, 'name' => $user->name, 'role' => $user->role, 'api_token' => $user->api_token];
                return response()->json(['status' => 200, 'message' => 'Successfully registered.', 'data' => $data], 200);
            } else {
                return response()->json(['status' => 400, 'errors' => 'User does not exist.'], 400);
            }
        } else {
            return response()->json(['status' => 400, 'errors' => 'wrong method.'], 400);
        }
    }

    // verify email request
    public function verifyEmail(Request $request) {
        if ($request->isMethod('post')) {
            $header = $request->header('Authorization');
            $api_token = str_replace('Bearer ', '', $header);

            $user = User::where('api_token', '=', $api_token)->first();
            $user->email_verify_token = Str::random(80);
            $user->save();
            
            \Mail::to($user->email)->send(new VerifyMail($user));
            return response()->json(['status' => 200, 'message' => 'Successfully sent verification link.'], 200);
        }
    }

    // verify phone request
    public function verifyPhone(Request $request) {
        if ($request->isMethod('post')) {

        }
    }

    // verify user
    public function verifyUser(Request $request) {
        switch ($request->type) {
            case 'email': 
                $email_verify_token = $request->token;
                $user_id = $request->user_id;
                $expire_at = $request->expire_at;
                $now = strtotime('now');
                // check user
                if (!User::find($user_id)) {
                    return response()->json(['status' => 400, 'errors' => 'Does not exist user.'], 400);
                }
                // check email verify token
                if (!User::where('email_verify_token', '=', $email_verify_token)->first()) {
                    return response()->json(['status' => 400, 'errors' => 'Does not exist user.'], 400);
                }
                // check token expire
                if ($expire_at < $now) {
                    return response()->json(['status' => 400, 'errors' => 'Token is expired.'], 400);
                }

                $user = User::where('id', '=', $user_id)
                            ->where('email_verify_token', '=', $email_verify_token)
                            ->first();
                // check if match user and email token
                if (!$user) {
                    return response()->json(['status' => 400, 'errors' => 'Token is expired.'], 400);
                }
                // update the email verify status
                $user->email_verified_at = $now;
                $user->save();
                // check email verify status
                if (!$user->email_verified_at) {
                    return response()->json(['status' => 500, 'errors' => 'Internal Server Error.'], 500);
                }
                
                return response()->json(['status' => 200, 'message' => 'Your email was successfully verified.'], 200);
                
                break;
            case 'phone': 
                $secret_code = $request->code;
                dd($secret_code);
                break;
            default : 
                return response()->json(['status' => 400, 'errors' => 'wrong path.'], 400);
                break;
        }
    }

    // more info for `individule` or `corporate` user
    public function registerProfile(Request $request) {
        if ($request->isMethod('post')) {
            $header = $request->header('Authorization');
            $api_token = str_replace('Bearer ', '', $header);
            
            $user = User::where('api_token', '=', $api_token)->first();
            
            if (!$user) {
                return response()->json(['status' => 400, 'errors' => 'Invalid token.'], 400);
            }

            $validator = ($user->role == "individual") 
                ? Validator::make($request->all(), [
                    'address' => 'required|string|max:255',
                    'state' => 'required|string|max:255',
                    'city' => 'required|string|max:255',
                    'gender' => 'required|string|max:255',
                    'phone' => 'string|max:255|unique:users',
                    'cv' => 'string|max:255',
                ]) 
                : Validator::make($request->all(), [
                    'address' => 'required|string|max:255',
                    'state' => 'required|string|max:255',
                    'city' => 'required|string|max:255',
                    'phone' => 'string|max:255|unique:users',
                    'mobile' => 'string|max:255',
                    'commercial_registeration' => 'string|max:255',
                    'sector' => 'string|max:255',
                ]);
            
            if ($validator->fails()) {
                return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
            }
            
            $user->address = $request->address ? $request->address : '';
            $user->state = $request->state ? $request->state : '';
            $user->city = $request->city ? $request->city : '';
            $user->gender = $request->gender ? $request->gender : '';
            $user->phone = $request->phone ? $request->phone : '';
            // $user->cv = $request->cv ? $request->cv : '';
            $user->mobile = $request->mobile ? $request->mobile : '';
            $user->commercial_registeration = $request->commercial_registeration ? $request->commercial_registeration : '';
            $user->sector = $request->sector ? $request->sector : '';
            $user->latitude = $request->latitude ? $request->latitude : '';
            $user->longitude = $request->longitude ? $request->longitude : '';
            $user->save();

            $data = User::with(['jobs', 'favIndividuals', 'favCorporates', 'favJobs', 'reviewsReceived'])->find($user->id);
            return response()->json(['status' => 200, 'message' => 'Successfully added profile informations.', 'data' => $data], 200);
        } else {
            return response()->json(['status' => 400, 'errors' => 'wrong method.'], 400);
        }
    }



    // =========== Web API ============ //
    public function registerFromWeb (Request $request) {
        if($request->isMethod('post')) {
            $validator = ($request->role == "individual") 
                ? Validator::make($request->all(), [
                    'role' => 'required|string|max:255',
                    'name' => 'required|string|max:255|unique:users',
                    'email' => 'required|string|email|max:255|unique:users',
                    'password' => 'required|string|min:1|confirmed',
                    'address' => 'required|string|max:255',
                    'state' => 'required|string|max:255',
                    'city' => 'required|string|max:255',
                    'gender' => 'required|string|max:255',
                    'phone' => 'required|string|max:255|unique:users',
                ]) 
                : Validator::make($request->all(), [
                    'role' => 'required|string|max:255',
                    'name' => 'required|string|max:255|unique:users',
                    'email' => 'required|string|email|max:255|unique:users',
                    'password' => 'required|string|min:1|confirmed',
                    'address' => 'required|string|max:255',
                    'state' => 'required|string|max:255',
                    'city' => 'required|string|max:255',
                    'phone' => 'required|string|max:255|unique:users',
                    'mobile' => 'string|max:255',
                    'commercial_registeration' => 'string|max:255',
                    'sector' => 'string|max:255',
                ]);
            
            if ($validator->fails()) {
                return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
            }

            // create new user
            $user = new User();
            $user->role = $request->role;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->api_token = Str::random(80);
            $user->address = $request->address;
            $user->state = $request->state;
            $user->city = $request->city;
            $user->phone = $request->phone;
            $user->mobile = $request->mobile ? $request->mobile : null;
            $user->gender = $request->gender ? $request->gender : null;
            $user->commercial_registeration = $request->commercial_registeration ? $request->commercial_registeration : null;
            $user->sector = $request->sector;
            $user->latitude = Location::get($request->ip()) ? Location::get($request->ip())->latitude : null;
            $user->longitude = Location::get($request->ip()) ? Location::get($request->ip())->longitude : null;
            $user->save();

            $data = User::with(['jobs', 'favIndividuals', 'favCorporates', 'favJobs', 'reviewsReceived'])->find($user->id);

            return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => $data], 200);
        }
    }

    // user login by social
    public function loginBySocialFromWeb(Request $request) {
        if ($request->isMethod('post')) {
            // === from web, linkedin login === //
            if ($request->login_type && $request->login_type == 'linkedin' && $request->access_token) {
                $access_token = $request->access_token;

                // getting profile info
                $endpoint1 = "https://api.linkedin.com/v2/me?projection=(id,firstName,lastName,localizedFirstName,localizedLastName,profilePicture(displayImage~:playableStreams))";
                $client1 = new \GuzzleHttp\Client();
                $response1 = $client1->request('GET', $endpoint1, ['headers' => [
                    'Authorization' => 'Bearer ' . $access_token, 
                ]]);

                $profileStatusCode = $response1->getStatusCode();
                $profileContent = (array)json_decode($response1->getBody());
                // getting emailAddress
                $endpoint2 = "https://api.linkedin.com/v2/emailAddress?q=members&projection=(elements*(handle~))";
                $client2 = new \GuzzleHttp\Client();
                $response2 = $client2->request('GET', $endpoint2, ['headers' => [
                    'Authorization' => 'Bearer ' . $access_token, 
                ]]);
                
                $emailStatusCode = $response2->getStatusCode();
                $emailContent = json_decode($response2->getBody());


                if($profileStatusCode == 200 && $emailStatusCode == 200) {
                    $emailInfo = (array)$emailContent->elements[0];
                    $socialUser['email'] = $emailInfo['handle~']->emailAddress;
                    $socialUser['name'] = $profileContent['localizedFirstName'] . ' ' . $profileContent['localizedLastName'];
                    $socialUser['first_name'] = $profileContent['localizedFirstName'];
                    $socialUser['last_name'] = $profileContent['localizedLastName'];
                    $profilePicture = (array)$profileContent['profilePicture'];
                    $socialUser['logo'] = $profilePicture['displayImage~']->elements[0]->identifiers[0]->identifier;

                    $user = User::with(['favIndividuals', 'favCorporates', 'favJobs', 'reviewsReceived'])->where('email', $socialUser['email'])->first();
                    
                    if ($user) {
                        if ($request->role) {
                            $user->role = $request->role;
                        }
                        return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => $user], 200);
                    }

                    $logo = $request->role == 'individual' ? 'https://foras.app/profile-images/default-individual.png' 
                                                    : 'https://foras.app/profile-images/default-corporate.png';

                    $new_user = new User();
                    if ($request->role) {
                        $new_user->role = $request->role;
                    }
                    $new_user->name = $socialUser['name'];
                    $new_user->email = $socialUser['email'];
                    $new_user->first_name = $socialUser['first_name'];
                    $new_user->last_name = $socialUser['last_name'];
                    $new_user->logo = $socialUser['logo'];
                    $new_user->registered_by = $request->login_type;
                    $new_user->api_token = Str::random(80);
                    $new_user->password = Hash::make(Str::random(20));
                    $new_user->latitude = Location::get($request->ip()) ? Location::get($request->ip())->latitude : null;
                    $new_user->longitude = Location::get($request->ip()) ? Location::get($request->ip())->longitude : null;
                    $new_user->save();

                    $data = User::with(['jobs', 'favIndividuals', 'favCorporates', 'favJobs', 'reviewsReceived'])->find($new_user->id);

                    return response()->json(['status' => 200, 'message' => $request->login_type, 'data' => $data], 200);
                }

                return response()->json(['status' => 400, 'errors' => 'wrong method.'], 400);
            }

            // === FB, Google Login === //

            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255',
                'login_type' => 'required|string|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
            }
            
            $user = User::with(['jobs', 'favIndividuals', 'favCorporates', 'favJobs', 'reviewsReceived'])->where('email', $request->email)->first();

            if ($user) {
                if ($request->role) {
                    $user->role = $request->role;
                }
                $user->save();
                return response()->json(['status' => 200, 'message' => 'foras-success', 'data' => $user], 200);
            }

            // register as new user by social account
            $register_validator = Validator::make($request->all(), [
                // 'role' => 'required|string|max:255',
                'name' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
            ]);

            if ($register_validator->fails()) {
                return response()->json(['status' => 400, 'errors' => $register_validator->errors()], 400);
            }

            $logo = $request->role == 'individual' ? 'https://foras.app/profile-images/default-individual.png' 
                                                    : 'https://foras.app/profile-images/default-corporate.png';

            $new_user = new User();
            if ($request->role) {
                $new_user->role = $request->role;
            }
            $new_user->name = $request->name;
            $new_user->email = $request->email;
            $new_user->first_name = $request->first_name ? $request->first_name : '';
            $new_user->last_name = $request->last_name ? $request->last_name : '';
            $new_user->logo = $request->logo ? $request->logo : $logo;
            $new_user->registered_by = $request->login_type;
            $new_user->api_token = Str::random(80);
            $new_user->password = Hash::make(Str::random(20));
            $new_user->latitude = Location::get($request->ip()) ? Location::get($request->ip())->latitude : null;
            $new_user->longitude = Location::get($request->ip()) ? Location::get($request->ip())->longitude : null;
            $new_user->save();

            $data = User::with(['jobs', 'favIndividuals', 'favCorporates', 'favJobs', 'reviewsReceived'])->find($new_user->id);

            return response()->json(['status' => 200, 'message' => $request->login_type, 'data' => $data], 200);

        } else {
            return response()->json(['status' => 400, 'errors' => 'wrong method.'], 400);
        }
    }
}
