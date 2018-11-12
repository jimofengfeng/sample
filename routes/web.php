<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//首页 帮助页面 关于我们
Route::get('/', 'StaticPagesController@home')->name('home');
Route::get('/help', 'StaticPagesController@help')->name('help');
Route::get('/about', 'StaticPagesController@about')->name('about');


//注册页面
Route::get('/signup', 'UsersController@create')->name('signup');

//用户信息 CURD  增删改查
Route::resource('users','UsersController');

//登陆 退出
Route::get('login','SessionsController@create')->name('login');
Route::post('login','SessionsController@store')->name('login');
Route::delete('logout','SessionsController@destory')->name('logout');

//注册激活
Route::get('signup/confirm/{token}', 'UsersController@confirmEmail')->name('confirm_email');

//找回密码相关
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

//微博相关
Route::resource('statuses','StatusesController',['only'=>['store','destroy']]);

//关注列表和粉丝列表
Route::get('/users/{user}/followings', 'UsersController@followings')->name('users.followings');//关注
Route::get('/users/{user}/followers', 'UsersController@followers')->name('users.followers');//粉丝

//关注
Route::post('/users/followers/{user}', 'FollowersController@store')->name('followers.store');
//取关
Route::delete('/users/followers/{user}', 'FollowersController@destroy')->name('followers.destroy');