<?php

namespace App\Http\Controllers\Manage;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use Validator;
use Auth;
use Redirect;

class ManageController extends Controller
{
    public function index(){

        if( Auth::user()->id !== 1 ){
            Redirect::to('/')->send();
        }

        $users = User::all();
        return view('manage.index',compact('users'));
    }

    public function edit($id){
        $user = User::find($id);
        if( ! $user ){
            abort(404);
        }
        return view('manage.edit_form',compact('user'));
    }

    public function create(){

        if( Auth::user()->id !== 1 ){
            Redirect::to('/')->send();
        }

        return view('manage.add_form');
    }

    public function store(Request $request){

        if( Auth::user()->id !== 1 ){
            Redirect::to('/')->send();
        }

        $validator = Validator::make($request->all(),[
                'name' => 'required|max:255',
                'email' => 'required|max:255|unique:users',
                'password' => 'required|min:6',
            ]);
        if( $validator->fails() ){
            return redirect('manage/users/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $data = $request->only('name','email','password');
        $data['password'] = bcrypt($data['password']);
        $uid = User::create($data);
        if( $uid ){
            return redirect('manage/users');
        }
        
    }

    public function update(Request $request,$id){

        $password = bcrypt($request->input('password'));
        $user = User::find($id);
        
        if( ! $user ){
            abort(404);
        }
        if( $password !== $user->password ){
            return redirect('manage/users/'.$id.'/edit')
                    ->withErrors(['error'=>'密码错误']);
        }
        $data['password'] = bcrypt($request->input('new_password'));
        $rs = $user->update($data);
        if( $rs ){
            return redirect('manage/users');
        }
    }

    public function destroy(Request $request, $id){

        if( Auth::user()->id !== 1 ){
            Redirect::to('/')->send();
        }

        $user = User::find($id);
        if( ! $user ){
            abort(404);
        }
        $user->delete();
        return redirect('manage/users');
    }
}
