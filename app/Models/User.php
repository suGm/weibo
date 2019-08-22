<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';//对应要交互的用户数据库
    /**
     * The attributes that are mass assignable.
     * 对过滤用户提交字段,只有包含在该属性的字段才能被正常更新
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     * 当我们要对用户密码或其他敏感信息在用户实例通过数组或JSON显示时进行隐藏
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    // boot 方法会在用户模型类完成初始化之后进行加载，因此我们对事件的监听需要放在该方法中。
    public static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->activation_token = Str::random(10);
        });
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /*
        Gravatar 为 “全球通用头像”,当你在 Gravatar 的服务器上放置了自己的头像后,可通过将自己的 Gravatar 登录邮箱进行 MD5 转码,并与 Gravatar 的 URL 进行拼接来获取到自己的 Gravatar 头像。

        1.为 gravatar 方法传递的参数 size 指定了默认值 100
        2.通过 $this->attributes['email'] 获取到用户的邮箱
        3.使用 trim 方法剔除邮箱的前后空白内容
        4.用 strtolower 方法将邮箱转换为小写
        5.将小写的邮箱使用 md5 方法进行转码
        6.将转码后的邮箱与链接、尺寸拼接成完整的 URL 并返回
    */
    public function gravatar($size = '100')
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }

    //一个用户可以有多条微博
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    //将该用户发不过去的所有微博从数据库中取出来
    public function feed()
    {
        return $this->statuses()
                    ->orderBy('created_at', 'desc');
    }
}
