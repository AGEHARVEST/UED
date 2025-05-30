<?php

use App\Http\Controllers\PSController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/listProducts',[PSController::class,'getProducts']);
Route::get('/listPrices',[PSController::class,'getPrices']);


Route::post('/productImg',[PSController::class,'productImg']);
Route::post('/productImgE',[PSController::class,'productImgE']);
Route::post('/stringTest',[PSController::class,'stringTest']);

Route::post('/productStock',[PSController::class,'productStock']);
Route::post('/productPriceList',[PSController::class,'productPriceList']);
Route::post('/productPrice',[PSController::class,'productPrice']);
