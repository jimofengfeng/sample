<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller
{   

    public function __construct()
    {
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }
    
    //登录页面
    public function create()
    {
    	return view('sessions.create');
    }

    //登录验证
    public function store(Request $request)
    {
    	$credentials = $this->validate($request,[
    			'email'=>'required|email|max:255',
    			'password'=>'required|min:6|max:20'
    		]);
    	if(Auth::attempt($credentials, $request->has('remember'))){
    		session()->flash('success', '欢迎回来');
    		return redirect()->intended(route('users.show', [Auth::user()]));
    	}else{
    		session()->flash('danger', '账号或密码错误');
        	return redirect()->back();
    	}
    	return;
    }

    //注销登录
    public function destory()
    {
        Auth::logout();
        session()->flash('success', '您已成功退出');
        return redirect('login');
    }
}
