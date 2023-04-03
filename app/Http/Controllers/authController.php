<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use App\Mail\ForgotPassword;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;

class authController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:api', ['except' => ['login', 'register', 'forgotPassword']]);
        $this->middleware('requireUser:api', ['except' => ['login', 'register', 'forgotPassword','dashboard']]);
    }
    public function register(Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                'first_name' => 'required|min:2|max:10|string',
                'last_name' => 'min:2|max:10|string',
                'email' => 'required|email|unique:users,email',
                "account_type" => 'boolean',
                'password' => 'required|min:8|max:20',
            ]);
            $requestData = $request->all();
            $requestData['password'] = bcrypt($request->password);
            $user = User::create($requestData);
            $credentials = $request->only('email', 'password');
            $token = auth()->attempt($credentials);
            $tokenResponse = $this->createNewToken($token);
            return response()->json(['status' => 200, 'message' => 'User data stored successfully', 'data' => $user, 'token' => $tokenResponse]);
        } catch (\Exception $ex) {
            return response()->json(['status' => 500, 'message' => $ex->getMessage()]);
        }
    }
    public function login(Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                'email' => 'required',
                'password' => 'required',
            ]);
            $credentials = $request->all();
            if (Auth::attempt($credentials)) {
                $user = auth()->user();
                $token = auth()->attempt($credentials);
                $tokenResponse = $this->createNewToken($token);
                return response()->json(['status' => 200, 'message' => 'Logged In successfully', 'data' => $user, 'token' => $tokenResponse]);
            } else {
                return response()->json(['status' => 400, 'message' => 'Please try again']);
            }
        } catch (\Exception $ex) {
            return response()->json(['status' => 500, 'message' => $ex->getMessage()]);
        }
    }
    public function forgotPassword(Request $request)
    {
        try {
            $this->validate($request, [
                'email' => 'required|email|exists:users,email',
            ]);
            $requestData = $request->all();
            $user = User::where('email', $request->email)->first();
            $token = auth()->login($user);
            // $token = auth()->user()->id;
            Mail::to($requestData['email'])->send(new ForgotPassword([
                'token' => $token,
                'email' => $request->email
            ]));
            // view('reset_password', ['token' => $token,'email' => $request->email]);
            return response()->json(['status' => 200, "message" => "please check your email"]);
        } catch (\Exception $ex) {
            return response()->json(['status' => 500, 'message' => $ex->getMessage()]);
        }
    }
    // public function getResetPassword(Request $request): JsonResponse
    // {
    //     try {
    //         $token = $request->route('token');
    //         $user = auth()->setToken($token)->user();
    //         $password = bcrypt($request->password);
    //         User::where('email', $user->email)->update(['password' => $password]);
    //         return response()->json(['status' => 200, "message" => "please reset your password", 'user' => $user]);
    //     } catch (\Exception $ex) {
    //         return response()->json(['status' => 500, 'message' => $ex->getMessage()]);
    //     }
    // }
    public function getUser(): JsonResponse
    {
        try {
            $user = Auth::guard('api')->user();
            return response()->json(['status' => 200, 'message' => 'User retrieved successfully', 'data' => $user]);
        } catch (\Exception $ex) {
            return response()->json(['status' => 500, 'message' => $ex->getMessage()]);
        }
    }
    public function createNewToken($token): JsonResponse
    {
        try {
            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 24
            ]);
        } catch (\Exception $ex) {
            return response()->json(['status' => 500, 'message' => $ex->getMessage()]);
        }
    }
    public function resetPassword(Request $request): JsonResponse
    {
        try {
            $token = $request->route('token');
            $user = auth()->setToken($token)->user();
            $password = bcrypt($request->password);
            User::where('email', $user->email)->update(['password' => $password]);
            return response()->json(['status' => 200, "message" => "Password Reset Successfully", 'user' => $user]);
        } catch (\Exception $ex) {
            return response()->json(['status' => 500, 'message' => $ex->getMessage()]);
        }
    }

    public function updatePassword(Request $request): JsonResponse
    {
        try {
            $user = $request->user;
            $password = bcrypt($request->password);
            User::where('id', $user)->update(['password' => $password]);
            return response()->json(['status' => 200, "message" => "Password Reset Successfully"]);
        } catch (\Exception $ex) {
            return response()->json(['status' => 500, 'message' => $ex->getMessage()]);
        }
    }

    public function dashboard(Request $request): JsonResponse
    {
        try {
            $account_type = $request->account_type;
            $search = $request->search;
            // switch (true) {
            //         case ($account_type && !$search):
            //                 $user = User::where('account_type',$account_type)->withCount('properties')->get();
            //                 return response()->json(['status' => 200, "user" => $user]);

            //         case ($account_type && $search):
            //             $user = User::where('first_name','LIKE',"%{$search}%")->orWhere('last_name','LIKE',"%{$search}%")->orWhere('email','LIKE',"%{$search}%")->where('account_type',$account_type)->withCount('properties')->get();
            //             return response()->json(['status' => 200, "user" => $user]);

            //         case ($search && !$account_type):
            //             $user = User::where('first_name','LIKE',"%{$search}%")->orWhere('last_name','LIKE',"%{$search}%")->orWhere('email','LIKE',"%{$search}%")->withCount('properties')->get();
            //             return response()->json(['status' => 200, "user" => $user]);

            //         default:
            //             $user = User::withCount('properties')->paginate();
            //             return response()->json(['status' => 200, "user" => $user]);
            // }

            if ($account_type && $search) {
                $user = User::where('account_type', $account_type)
                ->where(function($query) use ($search) {
                    $query->where('first_name','LIKE',"%{$search}%")
                        ->orWhere('last_name','LIKE',"%{$search}%")
                        ->orWhere('email','LIKE',"%{$search}%");
                })->withCount('properties')->get();
                    return response()->json(['status' => 200, "user" => $user]);
            }

            if ($account_type && !$search) {
                $user = User::where('account_type',$account_type)->withCount('properties')->get();
                        return response()->json(['status' => 200, "user" => $user]);
            }

            if($search && !$account_type) {
                $user = User::where('first_name','LIKE',"%{$search}%")->orWhere('last_name','LIKE',"%{$search}%")->orWhere('email','LIKE',"%{$search}%")->withCount('properties')->get();
                    return response()->json(['status' => 200, "user" => $user]);
            }

            if(!$search && !$account_type) {
                $user = User::withCount('properties')->paginate();
                    return response()->json(['status' => 200, "user" => $user]);
            }
        } catch (\Exception $ex) {
            return response()->json(['status' => 500, 'message' => $ex->getMessage()]);
        }
    }
}
