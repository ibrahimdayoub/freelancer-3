<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //01 View Users (Admin)
    public function view_users()
    {
        $users=User::all();

        return response()->json([
            'status'=>200,
            'users'=>$users,
        ]);
    }

    //02 Add User (Admin)
    public function add_user(Request $request)
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
            $user=new User;
            $user->first_name=$request->input('first_name');
            $user->last_name=$request->input('last_name');
            $user->age=$request->input('age');
            $user->address=$request->input('address');
            $user->num_borrow=$request->input('num_borrow');
            $user->email=$request->input('email');
            $user->password=Hash::make($request->input('password'));
            $user->save();

            return response()->json([
                'status'=>201,
                'user'=>$user,
                'message'=>'User Added Successfully',
            ]);
        }
    }

    //03 View User (Admin and User)
    public function view_user($id)
    {
        $user=User::find($id);

        if($user)
        {
            return response()->json([
                'status'=>200,
                'user'=>$user,
                'message'=>'User Fetched Successfully',
            ]);
        }
        else
        {
            return response()->json([
                'status'=>404,
                'message'=>'No User Id Found',
            ]);
        }
    }

    //04 Update User (Admin)
    public function update_user(Request $request,$id)
    {
        $validationArray=[
            'first_name'=>['required','string','max:50'],
            'last_name'=>['required','string','max:50'],
            'address'=>['required','string','max:100'],
            'age'=>['required','integer','max:100'],
            'num_borrow'=>['required','integer'],
            'password'=>['required','string','min:8'],
        ];

        $user_e=User::find($id);

        if($user_e && $user_e->email==$request->input('email'))
        {
            $validationArray['email']=['required','string','max:100','email','unique:admins'];
        }
        else
        {
            $validationArray['email']=['required','string','max:100','email','unique:users','unique:admins'];
        }

        $validator=Validator::make($request->all(),$validationArray);

        if($validator->fails())
        {
            return response()->json([
                'validation_errors'=>$validator->messages(),
            ]);
        }
        else
        {
            $user=User::find($id);
            if($user)
            {

                $user->first_name=$request->input('first_name');
                $user->last_name=$request->input('last_name');
                $user->age=$request->input('age');
                $user->address=$request->input('address');
                $user->num_borrow=$request->input('num_borrow');
                $user->email=$request->input('email');
                $user->password = $request->input('password')==="useOldPassword" ? $user->password : Hash::make($request->input('password'));
                $user->save();

                return response()->json([
                    'status'=>200,
                    'user'=>$user,
                    'message'=>'User Updated Successfully',
                ]);
            }
            else
            {
                return response()->json([
                    'status'=>404,
                    'message'=>'No User Id Found',
                ]);
            }

        }
    }

    //05 Delete User (Admin)
    public function delete_user($id)
    {
        $user=User::find($id);
        if($user)
        {
            $user->delete();

            return response()->json([
                'status'=>200,
                'message'=>'User Deleted Successfully'
            ]);
        }
        else
        {
            return response()->json([
                'status'=>404,
                'message'=>'User Is Not Found',
            ]);
        }
    }
}
