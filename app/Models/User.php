<?php

namespace App\Models;


use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Notifications\ResetPassword;

use Auth;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    //监听注册
    public static function boot(){
        parent::boot();

        static::creating(function($user){
            $user->activation_token = str_random(30);
        });
    }

    public function gravatar($size = '100')
    {
        $hash = md5(strtotime(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }
    //关联微博  一对多
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }  
    //微博信息列表
    public function feed()
    {
        /*return $this->statuses()
                    ->orderBy('created_at', 'desc');*/
        $user_ids = Auth::user()->followings->pluck('id')->toArray();
        array_push($user_ids,Auth::user()->id);
        return Status::whereIn('user_id',$user_ids)->with('user')->orderBy('created_at', 'desc');
    }
    //关联粉丝   多对多  获取指定用户的粉丝列表
    public function followers(){
        return $this->belongsToMany(User::class,'followers','user_id','follower_id')->withTimestamps();
    }

    //关联关注列表   多对多 获取指定用户的关注列表
    public function followings(){
        return $this->belongsToMany(User::class,'followers','follower_id','user_id')->withTimestamps();
    }

    //判断是否关注
    public function isFollowing($user_id){
        return $this->followings->contains($user_id);
    }

    //关注某人/某些人
    public function follow($user_ids)
    {
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->sync($user_ids, false);
    }

    //取关某人/某些人
    public function unfollow($user_ids)
    {
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->detach($user_ids);
    }
}
