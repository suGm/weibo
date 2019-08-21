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
	Route::delete('/users/{user}', 'UsersController@destory')->name('users.destory');//删除用户
*/

Route::get('login', 'SessionsController@create')->name('login');
Route::post('login', 'SessionsController@store')->name('login');
Route::delete('logout', 'SessionsController@destory')->name('logout');

