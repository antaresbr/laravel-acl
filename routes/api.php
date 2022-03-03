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
        'package' => ai_acl_infos(),
        'env' => app()->environmentFile(),
        'serverDateTime' => Carbon::now()->toString(),
    ]);
});

Route::post('/login', 'AclLoginController@login');

Route::middleware('auth:acl')->group(function () {
    Route::post('/authorize', 'AclAuthorizeController@authorize');
    Route::post('/get-menu-tree', 'AclMenuController@getMenuTree');
    Route::post('/get-menu-rights', 'AclMenuController@getMenuRights');
    Route::get('/get-session', 'AclSessionController@getSessionFromRequest');
    Route::get('/logged-user', 'AclUserController@getLoggedUser');
    Route::get('/logout', 'AclLoginController@logout');
});
