<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
//hashing passwords
use Hash;
//for authentication
use Auth;

class AuthController extends Controller
{
    public function login()
    {    
        //use Hash for pass
      //dd(Hash::make(12345));


        if(!empty(Auth::check()))
        {
            if(Auth::user()->role == 1)
            {
              return redirect('admin/admin/list');
            }
            else if(Auth::user()->role == 2)
            {
              return redirect('teacher/list/classlist');
            }
            else if(Auth::user()->role == 3)
            {
                
              return redirect('student/dashboard');
            }
            else if(Auth::user()->role == 4)
            {
              return redirect('secretary/teacher_list/instructor_list');
            }
        }

        return view('auth.login');
    }

    public function AuthLogin(Request $request)
    {
      //dd($request->all());

        if(Auth::attempt(['id_number' => $request->id_number, 'password' => $request->password], true))
        {
            if(Auth::user()->role == 1)
            {
              return redirect('admin/admin/list');
            }
            else if(Auth::user()->role == 2)
            {
              return redirect('teacher/list/classlist');
            }
            else if(Auth::user()->role == 3)
            {
              return redirect('student/dashboard');
            }
             else if(Auth::user()->role == 4)
            {
              return redirect('secretary/teacher_list/instructor_list');
            }

        }
        else
        {
            return redirect()->back()->with('error', 'Enter correct ID number and password');
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect(url(''));
    }
}
