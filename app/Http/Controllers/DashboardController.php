<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class DashboardController extends Controller
{
     public function dashboard()
     {
            $data['header_title'] = 'Dashboard';
            if(Auth::user()->role == 1)
            {
              return view('admin.admin.list', $data);
            }
            else if(Auth::user()->role == 2)
            {
              return view('teacher.list.classlist', $data);
            }
            else if(Auth::user()->role == 3)
            {
              return view('student.dashboard', $data);
            }
            else if(Auth::user()->role == 4)
            {
              return view('secretary.teacher_list.instructor_list', $data);
            }
     }
}
