<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User;
use Auth;


class FollowersController extends Controller
{
    public function __construct()
    {
    	$this->middleware('auth');
    }

    public function store(User $user)
    {
    	//如果用户ID和目标ID一致，跳回首页
    	if(Auth::user()->id == $user->id){
    		return redirect('/');
    	}
    	//检查是否在关注列表
    	if(!Auth::user()->isFollowing($user->id)){
    		Auth::user()->follow($user->id);
    	}

    	return redirect()->route('users.show', $user->id);
    }

    public function destroy(User $user)
    {
    	//如果用户ID和目标ID一致，跳回首页
    	if(Auth::user()->id == $user->id){
    		return redirect('/');
    	}
    	//检查是否在关注列表
    	if(Auth::user()->isFollowing($user->id)){
    		Auth::user()->unfollow($user->id);
    	}

    	return redirect()->route('users.show', $user->id);
    }
}
