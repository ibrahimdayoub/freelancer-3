<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsController extends Controller
{
    //01 View News (Admin and User)
    public function view_all_news()
    {
        $news=News::all();
        $responses=[];

        if(count($news)>0)
        {
            foreach ($news as $one_news) {
                if($one_news->time<time())
                {
                    $one_news->delete();
                    continue;
                }

                $response=[];
                $response['type']=$one_news->type;
                $response['content']=$one_news->content;
                $response['time']=ceil((($one_news->time-time())/3600)/24);

                array_push( $responses, $response);
            }
        }

        return response()->json([
            'status'=>200,
            'news'=>$responses,
        ]);
    }

    //02 Add News (Admin)
    public function add_one_news(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'type'=>['required','string','max:50'],
            'content'=>['required','string','max:255'],
            'time'=>['required','integer','max:30'] //as days
        ]);

        if($validator->fails())
        {
            return response()->json([
                'validation_errors'=>$validator->messages(),
            ]);
        }
        else
        {
            $news=new News;
            $news->type=$request->input('type');
            $news->content=$request->input('content');
            $time=$request->input('time');
            $news->time=time()+$time*24*3600;
            $news->save();

            return response()->json([
                'status'=>201,
                'message'=>'News Added Successfully',
            ]);
        }
    }

    //03 View News (Admin)
    public function view_one_news($id)
    {
        $news=News::find($id);

        if($news)
        {
            if($news->time<time())
            {
                $news->delete();
                return response()->json([
                    'status'=>404,
                    'message'=>'News Is Not Found',
                ]);
            }

            $news->time=ceil((($news->time-time())/3600)/24);

            return response()->json([
                'status'=>200,
                'news'=>$news,
                'message'=>'News Fetched Successfully',
            ]);
        }
        else
        {
            return response()->json([
                'status'=>404,
                'message'=>'News Is Not Found',
            ]);
        }
    }

    //04 Update News (Admin)
    public function update_one_news(Request $request,$id)
    {
        $validator=Validator::make($request->all(),[
            'type'=>['required','string','max:50'],
            'content'=>['required','string','max:255'],
            'time'=>['required','integer','max:30'] //as days
        ]);

        if($validator->fails())
        {
            return response()->json([
                'validation_errors'=>$validator->messages(),
            ]);
        }
        else
        {
            $news=News::find($id);
            if($news)
            {
                $news->type=$request->input('type');
                $news->content=$request->input('content');
                $time=$request->input('time');
                $news->time=time()+$time*24*3600;
                $news->save();

                return response()->json([
                    'status'=>201,
                    'message'=>'News Updated Successfully',
                ]);

            }
            else
            {
                return response()->json([
                    'status'=>404,
                    'message'=>'News Is Not Found',
                ]);
            }

        }
    }

    //05 Delete News (Admin)
    public function delete_one_news($id)
    {
        $news=News::find($id);
        if($news)
        {
            $news->delete();
            return response()->json([
                'status'=>200,
                'message'=>'News Deleted Successfully',
            ]);
        }
        else
        {
            return response()->json([
                'status'=>404,
                'message'=>'News Is Not Found',
            ]);
        }
    }
}
