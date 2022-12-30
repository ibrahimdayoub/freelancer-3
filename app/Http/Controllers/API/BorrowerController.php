<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrower;
use App\Models\User;
use Illuminate\Http\Request;

class BorrowerController extends Controller
{
    //01 Borrow Book (User)
    public function borrow_book($id)
    {
        $borrowerBook= Borrower::where('user_id',auth()->user()->id)->Where('book_id',$id)->first();
        if($borrowerBook)
        {
            return response()->json([
                'status'=>400,
                'message'=>'You Can Not Borrowes This Book Twice',
            ]);
        }

        $book=Book::find($id);
        if($book)
        {
            $borrower = new Borrower();
            $borrower->start_date=time();
            $borrower->end_date=time()+15*24*3600; //after 15 days
            $borrower->user_id=auth()->user()->id;
            $user=User::find(auth()->user()->id);
            $user->num_borrow++;
            $user->save;
            $borrower->book_id=$id;
            $borrower->save();

            return response()->json([
                'status'=>200,
                'message'=>'Book Borrowes Successfully',
            ]);
        }
        else
        {
            return response()->json([
                'status'=>404,
                'message'=>'Book Is Not Found',
            ]);
        }
    }

    //02 Unborrow Book (User)
    public function un_borrow_book($id)
    {
        $borrowerBook= Borrower::where('user_id',auth()->user()->id)->Where('book_id',$id)->first();
        if($borrowerBook)
        {
            $borrowerBook->delete();
            $user=User::find(auth()->user()->id);
            $user->num_borrow--;
            $user->save;
            return response()->json([
                'status'=>200,
                'message'=>'Book Unborrowers Successfully',
            ]);
        }
        else
        {
            return response()->json([
                'status'=>404,
                'message'=>'Book Borrowers Is Not Found',
            ]);
        }
    }

    //03 Show Borrow Books (User)
    public function show_borrow_books()
    {
        $borrowerBooks= Borrower::where('user_id',auth()->user()->id)->get();
        $responses=[];

        if(count($borrowerBooks)>0)
        {
            foreach ($borrowerBooks as $borrowerBook ) {

                if($borrowerBook->end_date<time())
                {
                    $borrowerBook->delete();
                    $user=User::find(auth()->user()->id);
                    $user->num_borrow--;
                    $user->save;
                    continue;
                }
                $book=Book::find($borrowerBook->book_id);
                if(!$book)
                {
                    $borrowerBook->delete();
                    continue;
                }

                $response=[];
                $response['start_date']=date('Y-m-d H:i:s',$borrowerBook->start_date);
                $response['end_date']=date('Y-m-d H:i:s',$borrowerBook->end_date);

                if($book->rate_value>0.8)
                {
                    $book->rate_value=1.0;
                }
                else if($book->rate_value>0.6)
                {
                    $book->rate_value=0.8;
                }
                else if($book->rate_value>0.4)
                {
                    $book->rate_value=0.6;
                }
                else if($book->rate_value>0.2)
                {
                    $book->rate_value=0.4;
                }
                else if($book->rate_value>0.0)
                {
                    $book->rate_value=0.2;
                }
                else
                {
                    $book->rate_value=0.0;
                }

                $response['book']=[
                    'title'=>$book->title,
                    'author'=>$book->author,
                    'description'=>$book->description,
                    'year'=>$book->year,
                    'rate_value'=>$book->rate_value,
                    'number_voited'=>$book->number_voited,
                ];

                array_push( $responses, $response);
            }
        }

        return response()->json([
            'status'=>200,
            'Borrower Books'=>$responses,
        ]);
    }
}
