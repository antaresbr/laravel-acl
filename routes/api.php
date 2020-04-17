<?php

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/alive', function (Request $request) {
    return response()->json([
        'env' => app()->environmentFile(),
        'version' => config('version.app', '1.0'),
        'serverDateTime' => Carbon::now()->toString(),
    ]);
});

Route::post('/login', 'AclLoginController@login');

Route::middleware('auth:acl')->get('/logged-user', 'AclUserController@getLoggedUser');
