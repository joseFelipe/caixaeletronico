<?php

use Illuminate\Http\Request;


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResources(['user' => 'API\UserController']);

Route::apiResources(['transaction' => 'API\TransactionController']);

// Route::get('/transactio/id',"API\TransactionController@findTransactionsByAccount");

Route::apiResources(['account' => 'API\AccountController']);