<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Suggestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SuggestionController extends Controller
{
    //01 Ask Suggestion (User)
    public function ask_suggestion(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'title'=>['required','string','max:100'],
            'author'=>['required','string','max:100'],
            'description'=>['required','string','max:255'],
            'year'=>['integer'],
        ]);

        if($validator->fails())
        {
            return response()->json([
                'validation_errors'=>$validator->messages(),
            ]);
        }
        else
        {
            $suggestion=new Suggestion();
            $suggestion->title=$request->input('title');
            $suggestion->author=$request->input('author');
            $suggestion->description=$request->input('description');
            $suggestion->year=$request->input('year');
            $suggestion->user_id=auth()->user()->id;

            $suggestion->save();
            return response()->json([
                'status'=>200,
                'message'=>'Suggestion Asked Successfully',
            ]);
        }
    }

    //02 Show Suggestion (User)
    public function show_suggestion($id)
    {
        $suggestion=Suggestion::find($id);

        if($suggestion)
        {
            return response()->json([
                'status'=>200,
                'suggestion'=>$suggestion,
                'message'=>'Suggestion Fetched Successfully',
            ]);
        }
        else
        {
            return response()->json([
                'status'=>404,
                'message'=>'No Suggestion Id Found',
            ]);
        }
    }

    //03 Update Suggestion (User)
    public function update_suggestion(Request $request,$id)
    {
        $validator=Validator::make($request->all(),[
            'title'=>['required','string','max:100'],
            'author'=>['required','string','max:100'],
            'description'=>['required','string','max:255'],
            'year'=>['integer'],
        ]);

        if($validator->fails())
        {
            return response()->json([
                'validation_errors'=>$validator->messages(),
            ]);
        }
        else
        {
            $suggestion=Suggestion::find($id);
            if($suggestion)
            {
                $suggestion->title=$request->input('title');
                $suggestion->author=$request->input('author');
                $suggestion->description=$request->input('description');
                $suggestion->year=$request->input('year');

                $suggestion->save();
                return response()->json([
                    'status'=>200,
                    'message'=>'Suggestion Updated Successfully',
                ]);
            }
            else
            {
                return response()->json([
                    'status'=>404,
                    'message'=>'No Suggestion Id Found',
                ]);
            }

        }
    }

    //04 Show Suggestions (Admin and User)
    public function show_suggestions()
    {
        if(auth()->user()->tokenCan('server:admin'))
        {
            $suggestions=Suggestion::all();

            foreach ($suggestions as $suggestion) {
                $suggestion->user=Suggestion::find($suggestion->id)->user;
            }

            return response()->json([
                'status'=>200,
                'suggestions'=>$suggestions,
            ]);
        }
        else if(auth()->user()->tokenCan('server:user'))
        {
            $suggestions= Suggestion::where('user_id',auth()->user()->id)->get();

            return response()->json([
                'status'=>200,
                'suggestions'=>$suggestions,
            ]);
        }
        else
        {
            return response()->json([
                'status'=>400,
                'message'=>'No Perrmision To Show That',
            ]);
        }
    }

    //05 Delete Suggestions (Admin and User)
    public function delete_suggestions($id)
    {
        $suggestion=Suggestion::find($id);
        if($suggestion)
        {
            if(
                auth()->user()->tokenCan('server:admin') ||
                (
                    auth()->user()->tokenCan('server:user') &&
                    $suggestion->user_id==auth()->user()->id
                )
            )
            {
                $suggestion->delete();

                return response()->json([
                    'status'=>200,
                    'message'=>'Suggestion Deleted Successfully'
                ]);
            }
            else
            {
                return response()->json([
                    'status'=>400,
                    'message'=>'No Perrmision To Delete That',
                ]);
            }
        }
        else
        {
            return response()->json([
                'status'=>404,
                'message'=>'Book Is Not Found',
            ]);
        }
    }

    //06 Answer Suggestion (Admin)
    public function answer_suggestion(Request $request,$id)
    {
        $validator=Validator::make($request->all(),[
            'accepted'=>['required','integer','max:1'] //0 or 1
        ]);

        if($validator->fails())
        {
            return response()->json([
                'validation_errors'=>$validator->messages(),
            ]);
        }
        else
        {
            $suggestion=Suggestion::find($id);
            if($suggestion)
            {
                $suggestion->accepted=$request->input('accepted');

                $suggestion->save();
                return response()->json([
                    'status'=>200,
                    'message'=>'Suggestion Updated Successfully',
                ]);
            }
            else
            {
                return response()->json([
                    'status'=>404,
                    'message'=>'No Suggestion Id Found',
                ]);
            }
        }
    }
}
