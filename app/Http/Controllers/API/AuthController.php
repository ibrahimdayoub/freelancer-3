<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Mail\Message; //With Mail Hog
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    //01 Register (User)
    public function register(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'first_name'=>['required','string','max:50'],
            'last_name'=>['required','string','max:50'],
            'address'=>['required','string','max:100'],
            'age'=>['required','integer','max:100'],
            'num_borrow'=>['required','integer'],
            'email'=>['required','string','max:100','email','unique:users','unique:admins'],
            'password'=>['required','string','min:8'],
        ]);

        if($validator->fails())
        {
            return response()->json([
                'validation_errors'=>$validator->messages(),
            ]);
        }
        else
        {
            $user=User::create([
                'first_name'=>$request->first_name,
                'last_name'=>$request->last_name,
                'address'=>$request->address,
                'age'=>$request->age,
                'num_borrow'=>$request->num_borrow,
                'email'=>$request->email,
                'password'=>Hash::make($request->password)
            ]);

            $token=$user->createToken($user->email.'User_Token',['server:user'])->plainTextToken;

            return response()->json([
                'status'=>201,
                'token'=>$token,
                'name'=>$user->first_name.' '.$user->last_name,
                'role'=>'User',
                'message'=>'Registered Successfully',
            ]);
        }
    }//Ok

    //02 Login (All)
    public function login(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'email'=>['required','string','max:100','email'],
            'password'=>['required','string','min:8'],
        ]);

        if($validator->fails())
        {
            return response()->json([
                'validation_errors'=>$validator->messages(),
            ]);
        }
        else
        {
            $user=User::where('email',$request->email)->first();
            $admin=Admin::where('email',$request->email)->first();

            if( (! $user || ! Hash::check($request->password,$user->password))
              &&(! $admin   || ! Hash::check($request->password,$admin->password))
            )
            {
                return response()->json([
                    'status'=>401,
                    'message'=>'Invalid Credentials',
                ]);
            }
            else if($user)
            {
                $token=$user->createToken($user->email.'_User_Token',['server:user'])->plainTextToken;

                return response()->json([
                    'status'=>200,
                    'token'=>$token,
                    'name'=>$user->first_name.' '.$user->last_name,
                    'role'=>'User',
                    'message'=>'Logged In Successfully',
                ]);
            }
            else if($admin)
            {
                $token=$admin->createToken($admin->email.'_Admin_Token',['server:admin'])->plainTextToken;

                return response()->json([
                    'status'=>200,
                    'token'=>$token,
                    'name'=>$admin->first_name.' '.$admin->last_name,
                    'role'=>'Admin',
                    'message'=>'Logged In Successfully',
                ]);
            }
        }
    }//Ok

    //03 Forgot Password (All)
    public function forgot (Request $request)
    {
        $validator=Validator::make($request->all(),[
            'email'=>['required','string','max:100','email'],
        ]);

        if($validator->fails())
        {
            return response()->json([
                'validation_errors'=>$validator->messages(),
            ]);
        }
        else
        {
            $email =$request->input('email');

            if (
                Admin::where('email', $email)->doesntExist() &&
                User::where('email', $email)->doesntExist()
            )
            {
                return response([
                    'status'=>404,
                    'message' => 'Account Doesn\'t Exists'
                ]);
            }

            $token =Str::random(25);

            try
            {
                DB::table('password_resets')->insert([
                    'email'=>$email,
                    'token'=>$token,
                    'created_at'=>Carbon::now()
                ]);


                //Gmail Or Mail Hog,  Only Change .env File
                Mail::send('reset',['token'=>$token],function(Message $message) use ($email){
                    $message->subject('Reset Password');
                    $message->to($email);
                });

                return response([
                    'status'=>200,
                    'message'=>'Check Your Email'
                ]);
            }
            catch (\Exception $e)
            {
                return response([
                    'status'=>404,
                    'message' => $e->getMessage()
                ]);
            }
        }
    }//Ok

    //04 Reset Password (All)
    public function reset (Request $request)
    {
        $validator=Validator::make($request->all(),[
            'token'=>['required','string'],
            'password'=>['required','string','min:8'],
            'password_confirm'=>['required','string','min:8','same:password']
        ]);

        if($validator->fails())
        {
            return response()->json([
                'validation_errors'=>$validator->messages(),
            ]);
        }
        else
        {
            $passwordResets =DB::table('password_resets')->where('token',$request->input('token'))->first();

            if(!$passwordResets)
            {
                return response([
                    'status'=>401,
                    'message'=>'Invalid Credentials'
                ]);
            }

            $admin =Admin::where('email',$passwordResets->email)->first();
            $user =User::where('email',$passwordResets->email)->first();

            //Delete to prevent many resets by same token
            DB::table('password_resets')->where('token',$request->input('token'))->delete();

            if(!$admin && !$user)
            {
                return response([
                    'status'=>404,
                    'message'=>'Account Is Not Found'
                ]);
            }
            else if($user)
            {
                $user->password=Hash::make($request->input('password'));
                $user->save();
            }
            else if($admin)
            {
                $admin->password=Hash::make($request->input('password'));
                $admin->save();
            }

            return response([
                'status'=>200,
                'message'=>'Password Changed Successfully'
            ]);
        }
    }//Ok

    //05 Logout (All)
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'status'=>200,
            'message'=>'Logged Out Successfully'
        ]);
    }//Ok
}
