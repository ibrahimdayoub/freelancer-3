<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    //01 View Admins (Admin)
    public function view_admins()
    {
        $admins=Admin::all();

        return response()->json([
            'status'=>200,
            'admins'=>$admins,
        ]);
    }

    //02 Add Admin (Admin)
    public function add_admin(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'first_name'=>['required','string','max:50'],
            'last_name'=>['required','string','max:50'],
            'email'=>['required','string','max:100','email','unique:admins','unique:users'],
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
            $admin=new Admin;
            $admin->first_name=$request->input('first_name');
            $admin->last_name=$request->input('last_name');
            $admin->email=$request->input('email');
            $admin->password=Hash::make($request->input('password'));
            $admin->save();

            return response()->json([
                'status'=>201,
                'admin'=>$admin,
                'message'=>'Admin Added Successfully',
            ]);
        }
    }

    //03 View Admin (Admin)
    public function view_admin($id)
    {
        $admin=Admin::find($id);

        if($admin)
        {
            return response()->json([
                'status'=>200,
                'admin'=>$admin,
                'message'=>'Admin Fetched Successfully',
            ]);
        }
        else
        {
            return response()->json([
                'status'=>404,
                'message'=>'No Admin Id Found',
            ]);
        }
    }

    //04 Update Admin (Admin)
    public function update_admin(Request $request,$id)
    {
        $validationArray=[
            'first_name'=>['required','string','max:50'],
            'last_name'=>['required','string','max:50'],
            'password'=>['required','string','min:8'],
        ];

        $admin_e=Admin::find($id);

        if($admin_e && $admin_e->email==$request->input('email'))
        {
            $validationArray['email']=['required','string','max:100','email','unique:users'];
        }
        else
        {
            $validationArray['email']=['required','string','max:100','email','unique:admins','unique:users'];
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
            $admin=Admin::find($id);
            if($admin)
            {

                $admin->first_name=$request->input('first_name');
                $admin->last_name=$request->input('last_name');
                $admin->email=$request->input('email');
                $admin->password = $request->input('password')==="useOldPassword" ? $admin->password : Hash::make($request->input('password'));
                $admin->save();

                return response()->json([
                    'status'=>200,
                    'admin'=>$admin,
                    'message'=>'Admin Updated Successfully',
                ]);
            }
            else
            {
                return response()->json([
                    'status'=>404,
                    'message'=>'No Admin Id Found',
                ]);
            }

        }
    }

    //05 Delete Admin (Admin)
    public function delete_admin($id)
    {
        $admin=Admin::find($id);
        if($admin)
        {
            $admins=Admin::all();

            if(count($admins)>1)
            {
                if(auth()->user()->id==$id)
                {
                    auth()->user()->tokens()->delete();
                    $admin->delete();
                    return response()->json([
                        'status'=>200,
                        'message'=>'Your Account Deleted Successfully'
                    ]);
                }
                else
                {
                    $admin->delete();
                    return response()->json([
                        'status'=>200,
                        'message'=>'Admin Deleted Successfully',
                    ]);
                }
            }
            else
            {
                return response()->json([
                    'status'=>400,
                    'message'=>'You Are Last Admin In The System, Give Another Admin And Try Latter',
                ]);
            }
        }
        else
        {
            return response()->json([
                'status'=>404,
                'message'=>'Admin Is Not Found',
            ]);
        }
    }
}
