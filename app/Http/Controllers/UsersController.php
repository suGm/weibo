<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Mail;

class UsersController extends Controller
{	
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store', 'index', 'confirmEmail']
        ]);

        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

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
        $statuses = $user->statuses()
                           ->orderBy('created_at', 'desc')
                           ->paginate(10);
    	return view('users.show', compact('user', 'statuses'));
    }

    /*
        该方法用于注册
        需要对用户输入数据进行验证,验证成功后再将数据存入数据库
        在laravel中,提供了多种数据验证方法
        这里使用了validator方法来验证数据
        validator方法接受两个参数,第一个参数为用户的输入数据,第二个参数为该输入数据的验证规则
        require(存在性验证,验证该字段是否为空)|
        min(填写字段的最小值)|
        max(填写字段的最大值)|
        email(对用户邮箱进行验证)|
        unique(唯一性验证:users对表users进行该字段唯一性验证)|
        confirmed(密码匹配验证)|
    */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        // Auth::login($user);
        // session()->flash('success', '欢迎您');
        $this->sendEmailConfirmationTo($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上,请注意查收。');
        // return redirect()->route('users.show', [$user]);
        return redirect('/');
    }

    //该方法用于展示身份编辑
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    //该方法用于更新个人信息
    public function update(User $user, Request $request)
    {
        $this->authorize('update', $user);
        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);

        $data = [];
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);

        session()->flash('success', '个人资料更新成功!');
        return redirect()->route('users.show', $user);
    }

    public function destroy(User $user)
    {
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success', '成功删除用户!');
        return back();
    }

    //显示用户关注列表
    public function followings(User $user)
    {
        $users = $user->followings()->paginate(30);
        $title = $user->name . '关注的人';
        return view('users.show_follow', compact('users', 'title'));
    }

    //显示用户粉丝列表
    public function followers(User $user)
    {
        $users = $user->followers()->paginate(30);
        $title = $user->name . '的粉丝';
        return view('users.show_follow', compact('users', 'title'));
    }

    //完成用户激活操作
    public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜你,激活成功!');
        return redirect()->route('users.show', [$user]);
    }

    //发送邮件
    protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $to = $user->email;
        $subject = '感谢注册 Weibo 应用!请确认你的邮箱。';

        Mail::send($view, $data, function($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
    }

}
