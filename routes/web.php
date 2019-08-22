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

Route::get('/', 'StaticPagesController@home')->name('home');
Route::get('/help', 'StaticPagesController@help')->name('help');//指定别名,使其可以在blade模板中使用{{ route('help') }}调用
Route::get('/about', 'StaticPagesController@about')->name('about');

Route::get('signup', 'UsersController@create')->name('signup');//有无/都无差别,laravel兼容两种写法

Route::resource('users', 'UsersController');//资源路由
/*
    上面代码等同于
    Route::get('/users', 'UsersController@index')->name('users.index');//显示用户列表页面
	Route::get('/users/create', 'UsersController@create')->name('users.create');//创建用户的页面
	Route::get('/users/{user}', 'UsersController@show')->name('users.show');//显示用户个人信息页面
	Route::post('/users', 'UsersController@store')->name('users.store');//创建用户
	Route::get('/users/{user}/edit', 'UsersController@edit')->name('users.edit');//编辑用户个人资料
	Route::patch('/users/{user}', 'UsersController@update')->name('users.update');//更新用户
	Route::delete('/users/{user}', 'UsersController@destroy')->name('users.destroy');//删除用户
*/

Route::get('login', 'SessionsController@create')->name('login');
Route::post('login', 'SessionsController@store')->name('login');
Route::delete('logout', 'SessionsController@destroy')->name('logout');

//注册验证email
Route::get('signup/confirm/{token}', 'UsersController@confirmEmail')->name('confirm_email');

//重置密码
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');//显示重置密码的邮箱发送页面
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');//邮箱发送重设链接
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');//密码更新页面
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');//执行密码更新操作

Route::resource('statuses', 'StatusesController', ['only' => ['store', 'destroy']]);//处理微博创建和删除的请求

Route::get('/users/{user}/followings', 'UsersController@followings')->name('users.followings');//显示用户粉丝列表
Route::get('/users/{user}/followers', 'UsersController@followers')->name('users.followers');//显示用户关注人列表