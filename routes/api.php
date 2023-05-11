<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\BookController;
use App\Http\Controllers\API\BorrowerController;
use App\Http\Controllers\API\SuggestionController;
use App\Http\Controllers\API\NewsController;
use Illuminate\Support\Facades\Route;

//Register User
Route::post('register',[AuthController::class,'register']); #1
//Login
Route::post('login',[AuthController::class,'login']); #2
//Forgot Password
Route::post('forgot',[AuthController::class,'forgot']); #3
//Reset Password
Route::post('reset',[AuthController::class,'reset']); #4

#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-

//Middleware if role admin or user
Route::middleware(['auth:sanctum'])->group(function(){
    //Logout
    Route::post('logout',[AuthController::class,'logout']); #5

    //Users
    Route::get('view_user/{id}',[UserController::class,'view_user']); #6

    //Books
    Route::get('view_books',[BookController::class,'view_books']); #7
    Route::get('view_book/{id}',[BookController::class,'view_book']); #8
    Route::get('pdf_book/{name}',[BookController::class,'pdf_book']); #9

    //News
    Route::get('view_all_news',[NewsController::class,'view_all_news']); #25

    //Suggestions
    Route::get('show_suggestions',[SuggestionController::class,'show_suggestions']); #10
    Route::delete('delete_suggestion/{id}',[SuggestionController::class,'delete_suggestion']); #11
});

#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-

//Middleware if role admin
Route::middleware(['auth:sanctum','isAdmin'])->group(function(){
    //Admins
    Route::get('view_admins',[AdminController::class,'view_admins']); #12
    Route::post('add_admin',[AdminController::class,'add_admin']); #13
    Route::get('view_admin/{id}',[AdminController::class,'view_admin']); #14
    Route::put('update_admin/{id}',[AdminController::class,'update_admin']); #15
    Route::delete('delete_admin/{id}',[AdminController::class,'delete_admin']); #16

    //Users
    Route::get('view_users',[UserController::class,'view_users']); #17
    Route::post('add_user',[UserController::class,'add_user']); #18
    Route::put('update_user/{id}',[UserController::class,'update_user']); #19
    Route::delete('delete_user/{id}',[UserController::class,'delete_user']); #20

    //Books
    Route::post('add_book',[BookController::class,'add_book']); #21
    Route::put('update_book/{id}',[BookController::class,'update_book']); #22
    Route::delete('delete_book/{id}',[BookController::class,'delete_book']); #23

    //Suggestions
    Route::post('answer_suggestion/{id}',[SuggestionController::class,'answer_suggestion']); #24

    //News
    Route::post('add_one_news',[NewsController::class,'add_one_news']); #26
    Route::get('view_one_news/{id}',[NewsController::class,'view_one_news']); #27
    Route::put('update_one_news/{id}',[NewsController::class,'update_one_news']); #28
    Route::delete('delete_one_news/{id}',[NewsController::class,'delete_one_news']); #29

    //Authenticated Admin
    Route::get('authenticated_admin',function(){ #30
        return response()->json(['status'=>200,'message'=>'You Are Authenticated','id'=>auth()->user()->id]);
    });
});

#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-

//Middleware if role user
Route::middleware(['auth:sanctum','isUser'])->group(function(){
    //Books
    Route::post('rate_book/{id}',[BookController::class,'rate_book']); #31
    Route::post('search_book/{key}',[BookController::class,'search_book']); #32

    //Suggestions
    Route::post('ask_suggestion',[SuggestionController::class,'ask_suggestion']); #33
    Route::get('show_suggestion/{id}',[SuggestionController::class,'show_suggestion']); #34
    Route::put('update_suggestion/{id}',[SuggestionController::class,'update_suggestion']); #35

    //Borrows
    Route::post('borrow_book/{id}',[BorrowerController::class,'borrow_book']); #36
    Route::post('un_borrow_book/{id}',[BorrowerController::class,'un_borrow_book']); #37
    Route::get('show_borrow_books',[BorrowerController::class,'show_borrow_books']); #38

    //Authenticated User
    Route::get('authenticated_user',function(){ #39
        return response()->json(['status'=>200,'message'=>'You Are Authenticated','id'=>auth()->user()->id]);
    });
});

#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-
