<?php

use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\BookController;
use Illuminate\Support\Facades\Route;

#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-

//Register User
Route::post('register',[AuthController::class,'register']);
//Login
Route::post('login',[AuthController::class,'login']);
//Forgot Password
Route::post('forgot',[AuthController::class,'forgot']);
//Reset Password
Route::post('reset',[AuthController::class,'reset']);

#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-

//Middleware if role admin or user
Route::middleware(['auth:sanctum'])->group(function(){
    //Logout
    Route::post('logout',[AuthController::class,'logout']);

    //Users
    Route::get('view_user/{id}',[UserController::class,'view_user']);

    //Books
    Route::get('view_books',[BookController::class,'view_books']);
    Route::get('view_book/{id}',[BookController::class,'view_book']);
    Route::get('pdf_book/{name}',[BookController::class,'pdf_book']); //as pdf file
});

#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-

//Middleware if role admin
Route::middleware(['auth:sanctum','isAdmin'])->group(function(){
    //Admins
    Route::get('view_admins',[AdminController::class,'view_admins']);
    Route::post('add_admin',[AdminController::class,'add_admin']);
    Route::get('view_admin/{id}',[AdminController::class,'view_admin']);
    Route::put('update_admin/{id}',[AdminController::class,'update_admin']);
    Route::delete('delete_admin/{id}',[AdminController::class,'delete_admin']);

    //Users
    Route::get('view_users',[UserController::class,'view_users']);
    Route::post('add_user',[UserController::class,'add_user']);
    Route::put('update_user/{id}',[UserController::class,'update_user']);
    Route::delete('delete_user/{id}',[UserController::class,'delete_user']);

    //Books
    Route::post('add_book',[BookController::class,'add_book']);
    Route::put('update_book/{id}',[BookController::class,'update_book']);
    Route::delete('delete_book/{id}',[BookController::class,'delete_book']);

    //Authenticated Admin
    Route::get('authenticated_admin',function(){
        return response()->json(['status'=>200,'message'=>'You Are Authenticated','id'=>auth()->user()->id]);
    });
});

#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-

//Middleware if role user
Route::middleware(['auth:sanctum','isUser'])->group(function(){
    //Books
    Route::post('rate_book/{id}',[BookController::class,'rate_book']);

    //Authenticated User
    Route::get('authenticated_user',function(){
        return response()->json(['status'=>200,'message'=>'You Are Authenticated','id'=>auth()->user()->id]);
    });
});

#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-#-
