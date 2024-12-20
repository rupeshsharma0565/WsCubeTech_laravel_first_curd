<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::get('/first',function(){
//            echo "first api call";
//            return "joo";
// });
// Route::post('/first',function(){
//     return "This is post api call";
// });

Route::post('/user/store',[UserController::class,'store']);
Route::get('/user/get/{flag}',[UserController::class,'index']);
Route::get('/user/{id}',[UserController::class,'show']);
Route::delete('/user/{id}',[UserController::class,'destroy']);
Route::put('/user/update/{id}',[UserController::class,'update']);
Route::patch('/change-password/{id}',[UserController::class,'changePassword']);