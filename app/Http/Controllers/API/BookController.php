<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Exception;

class BookController extends Controller
{
    //01 View Books (Admin and User)
    public function view_books()
    {
        $books=Book::all();

        for($i=0; $i<count($books); $i++)
        {
            if($books[$i]->rate_value>0.8)
            {
                $books[$i]->rate_value=1.0;
            }
            else if($books[$i]->rate_value>0.6)
            {
                $books[$i]->rate_value=0.8;
            }
            else if($books[$i]->rate_value>0.4)
            {
                $books[$i]->rate_value=0.6;
            }
            else if($books[$i]->rate_value>0.2)
            {
                $books[$i]->rate_value=0.4;
            }
            else if($books[$i]->rate_value>0.0)
            {
                $books[$i]->rate_value=0.2;
            }
            else
            {
                $books[$i]->rate_value=0.0;
            }
        }

        return response()->json([
            'status'=>200,
            'books'=>$books,
        ]);
    }

    //02 Add Book (Admin)
    public function add_book(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'title'=>['required','string','max:100','unique:books'],
            'author'=>['required','string','max:100'],
            'description'=>['required','string','max:255'],
            'year'=>['integer'],
            'path'=>['required','file']
        ]);

        if($validator->fails())
        {
            return response()->json([
                'validation_errors'=>$validator->messages(),
            ]);
        }
        else
        {
            $book=new Book;
            $book->title=$request->input('title');
            $book->author=$request->input('author');
            $book->description=$request->input('description');
            $book->year=$request->input('year');

            if($request->hasFile('path'))
            {
                    $file = $request->file('path');
                    $extension = $file->getClientOriginalExtension();

                    if($extension ==="pdf")
                    {
                    $filename = time().'.'.$extension;
                    $file->move('Uploads/Books/',$filename);
                    $book->path = 'Uploads/Books/'.$filename;
                    }
                    else
                    {
                        return response()->json([
                            'status'=>400,
                            'message'=>'We need pdf file (.pdf)',
                        ]);
                    }
            }
            else{
                    return response()->json([
                        'status'=>400,
                        'message'=>'We need pdf file (.pdf)',
                    ]);
            }

            $book->save();
            return response()->json([
                'status'=>200,
                'message'=>'Book Added Successfully',
            ]);
        }
    }

    //03 View Book (Admin and User)
    public function view_book($id)
    {
        $book=Book::find($id);

        if($book)
        {
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

            return response()->json([
                'status'=>200,
                'book'=>$book,
                'message'=>'Book Fetched Successfully',
            ]);
        }
        else
        {
            return response()->json([
                'status'=>404,
                'message'=>'No Book Id Found',
            ]);
        }
    }

    //04 Update Book (Admin)
    public function update_book(Request $request,$id)
    {
        $validationArray =[
            'author'=>['required','string','max:100'],
            'description'=>['required','string','max:255'],
            'year'=>['integer'],
            'path'=>['required','file']
        ];

        $cet_e=Book::find($id);

        if($cet_e && $cet_e->title==$request->input('title'))
        {
            $validationArray['title']=['required','string','max:100'];
        }
        else
        {
            $validationArray['title']=['required','string','max:100','unique:books'];
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
            $book=Book::find($id);
            if($book)
            {
                $book->title=$request->input('title');
                $book->author=$request->input('author');
                $book->description=$request->input('description');
                $book->year=$request->input('year');

                if($request->hasFile('location'))
                {
                    $path = $book->location;
                    if(File::exists($path))
                    {
                        File::delete($path);
                    }
                    $file = $request->file('location');
                    $extension = $file->getClientOriginalExtension();

                    if($extension ==="pdf")
                    {
                        $filename = time().'.'.$extension;
                        $file->move('Uploads/Books/',$filename);
                        $book->location = 'Uploads/Books/'.$filename;
                    }
                    else
                    {
                        return response()->json([
                            'status'=>400,
                            'message'=>'We need pdf file (.pdf)',
                        ]);
                    }
                }
                else
                {
                    $book->location = $book->location;
                }

                $book->save();
                return response()->json([
                    'status'=>200,
                    'message'=>'Book Updated Successfully',
                ]);
            }
            else
            {
                return response()->json([
                    'status'=>404,
                    'message'=>'No Book Id Found',
                ]);
            }

        }
    }

    //05 Delete Book (Admin)
    public function delete_book($id)
    {
        $book=Book::find($id);
        if($book)
        {
            $path = $book->book->location;
            if(File::exists($path))
            {
                File::delete($path);
            }

            $book->delete();

            return response()->json([
                'status'=>200,
                'message'=>'Book Deleted Successfully'
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

    //06 Fetch Pdf Book (Admin and User)
    public function pdf_book($name)
    {
        $file = null;

        try {
            $file = File::get('Uploads/Books/'.$name);
        }
        catch(Exception $e) {
            $response = Response::make($e->getMessage(),404);
            return $response;
        }

        $response = Response::make($file,200);
        $response->header('Content-Type', 'application/pdf');
        return $response;
    }

    //07 Rate Book (User)
    public function rate_book(Request $request,$id)
    {
        $validator=Validator::make($request->all(),[
            'rate'=>['required','numeric'],
        ]);

        if($validator->fails())
        {
            return response()->json([
                'validation_errors'=>$validator->messages(),
            ]);
        }
        else
        {
            if(!(
                $request->input('rate') == 0.2 ||
                $request->input('rate') == 0.4 ||
                $request->input('rate') == 0.6 ||
                $request->input('rate') == 0.8 ||
                $request->input('rate') == 1.0
            ))
            {
                return response()->json([
                    'status'=>400,
                    'message'=>'Rate Value Accept Only This Values [0.2,0.4,0.6,0.8,1.0]',
                ]);
            }
            else
            {
                $book=Book::find($id);
                if($book)
                {
                    $rate = (($book->rate_value*$book->number_voited)+$request->input('rate'))/($book->number_voited+1);
                    $book->rate_value=$rate;
                    $book->number_voited+=1;

                    $book->save();
                    return response()->json([
                        'status'=>200,
                        'message'=>'Book Rated Successfully',
                    ]);
                }
                else
                {
                    return response()->json([
                        'status'=>404,
                        'message'=>'No Book Id Found',
                    ]);
                }
            }
        }
    }

    //08 Search Book (User)
    public function search_book($key)
    {
        $books= Book::where('title','LIKE','%'.$key.'%')->orWhere('author','LIKE','%'.$key.'%')->get();
        return response()->json([
            'status'=>200,
            'book'=>$books,
            'message'=>'Books Fetched Successfully',
        ]);
    }
}
