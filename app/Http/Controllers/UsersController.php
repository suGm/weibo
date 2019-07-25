<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{	
	//用户注册页面
    public function create()
    {
    	return view('users.create');
    }

    //路由器会自动解析控制器方法的Eloquent模型类型声明。该方法在传参时声明了类型--Eloquent模型User,对应变量名$user会匹配路由片段中的{user},这样,laravel会自动注入和请求URL中传入的ID对应的用户模型实例。
    //此功能称为“隐形路由模型绑定”,是“约定由于配置”设计规范的体现,需要同时满足以下两种情况才会实现:
    //1、路由声明必须使用Eloquent模型单数小写格式来作为路由片段参数,User对应{user}
    //Route::get('/users/{user}', 'UsersController@show')->name('users.show');
    //在使用资源路由Route::resource('users', 'UsersController'); 时,默认已经包含了上面的声明。
    //2、控制器方法传参中必须包含对应的Eloquent模型类型声明,并且是有序的
    //如下
    //如果满足上面两个条件,当请求 http://weibo.test/users/1 并且满足以上两个条件时,Laravel 将会自动查找 ID 为 1 的用户并赋值到变量 $user 中,如果数据库中找不到对应的模型实例,会自动生成 HTTP 404 响应。
    public function show(User $user)
    {
    	return view('users.show', compact('user'));
    }
}
