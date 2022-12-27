<?php

use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\BookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



//Admins
Route::get('view_admins',[AdminController::class,'view_admins']);
Route::post('add_admin',[AdminController::class,'add_admin']);
Route::get('view_admin/{id}',[AdminController::class,'view_admin']);
Route::put('update_admin/{id}',[AdminController::class,'update_admin']);
Route::delete('delete_admin/{id}',[AdminController::class,'delete_admin']);

//Users
Route::get('view_users',[UserController::class,'view_users']);
Route::post('add_user',[UserController::class,'add_user']);
Route::get('view_user/{id}',[UserController::class,'view_user']);
Route::put('update_user/{id}',[UserController::class,'update_user']);
Route::delete('delete_user/{id}',[UserController::class,'delete_user']);

//Books
Route::get('view_books',[BookController::class,'view_books']);
Route::post('add_book',[BookController::class,'add_book']);
Route::get('view_book/{id}',[BookController::class,'view_book']);
Route::put('update_book/{id}',[BookController::class,'update_book']);
Route::delete('delete_book/{id}',[BookController::class,'delete_book']);
Route::get('pdf_book/{name}',[BookController::class,'pdf_book']); //as pdf file

//Default
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
