<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User;
use Auth;

class UsersController extends Controller
{   
    public function __construct()
    {
        $this->middleware('auth', [            
            'except' => ['show', 'create', 'store','index']
        ]);
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

	//注册页面展示
    public function create()
    {
    	return view('users.create');
    }

    //展示用户信息
    public function show(User $user)
    {
    	return view('users.show',compact('user'));
    }

    //处理提交数据
    public function store(Request $request)
    {
    	$this->validate($request,[
    			'name'=>'required|max:50',
    			'email'=>'required|email|unique:users|max:255',
    			'password'=>'required|confirmed|min:6'
    		]);
    	
    	$user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        Auth::login($user);
        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        
        return redirect()->route('users.show', [$user]);
    }

    //修改用户信息界面
    public function edit(User $user)
    {   
        $this->authorize('update', $user);
        return view('users.edit',compact('user'));
    }

    //修改信息
    public function update(User $user,Request $request)
    {   
        
        $this->validate($request, [
            'name' => 'required|max:50|unique:users,name,'.$user->id,
            'password' => 'nullable|confirmed|min:6'
        ]);

        $this->authorize('update', $user);

        $data = [];
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);

        session()->flash('success', '个人资料更新成功！');

        return redirect()->route('users.show', $user->id);
    }

    //用户信息列表
    public function index()
    {   
        //$this->authorize('index', $user);
        $users = User::paginate(10);
        return view('users.index',compact('users'));
    }

    //删除用户
    public function destroy(User $user)
    {   
        $this->authorize('destroy', $user);

        $user->delete();

        session()->flash('success', '成功删除用户！');
        return redirect()->back();
    }
    
}
