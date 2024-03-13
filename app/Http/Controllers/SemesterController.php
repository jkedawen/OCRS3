<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Semester;
use Illuminate\Support\Facades\DB;

class SemesterController extends Controller
{
   public function viewSemester()
    {
        $semesters = Semester::all();
        return view('admin.set_semester.view_semesters', compact('semesters'));
    }

     public function create()
    {
        return view('admin.set_semester.create_semester');
    }

    public function store(Request $request)
    {
        $request->validate([
            'semester_name' => 'required',
            'school_year' => 'required',
        ]);

        Semester::create($request->all());

          return redirect('admin/set_semester/view_semesters')->with('success', 'Semester created successfully');
    }

    public function edit($id)
    {
        $semester = Semester::findOrFail($id);
        return view('admin.set_semester.edit_semester', compact('semester'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'semester_name' => 'required',
            'school_year' => 'required',
        ]);

        $semester = Semester::findOrFail($id);
        $semester->update($request->all());

        return redirect('admin/set_semester/view_semesters')->with('success', 'Semester updated successfully');
    }

    public function destroy($id)
    {
        $semester = Semester::findOrFail($id);
        $semester->delete();

        return redirect('admin/set_semester/view_semesters')->with('success', 'Semester deleted successfully');
    }


    public function setupCurrentSemesterView()
{
    $semesters = Semester::all();

    return view('admin.set_semester.set_current', compact('semesters'));
}

public function setupCurrentSemester(Request $request)
{
    
   
    DB::table('semesters')->update(['is_current' => false]);

    Semester::where('id', $request->semester_id)->update(['is_current' => true]);


    return redirect('admin/set_semester/set_current')->with('success', 'Current semester updated successfully');
}


///////secretary side///

public function viewSemester1()
    {
        $semesters = Semester::all();
        return view('secretary.set_semester.view_semesters', compact('semesters'));
    }

     public function create1()
    {
        return view('secretary.set_semester.create_semester');
    }

    public function store1(Request $request)
    {
        $request->validate([
            'semester_name' => 'required',
            'school_year' => 'required',
        ]);

        Semester::create($request->all());

          return redirect('secretary/set_semester/view_semesters')->with('success', 'Semester created successfully');
    }

    public function edit1($id)
    {
        $semester = Semester::findOrFail($id);
        return view('secretary.set_semester.edit_semester', compact('semester'));
    }

    public function update1(Request $request, $id)
    {
        $request->validate([
            'semester_name' => 'required',
            'school_year' => 'required',
        ]);

        $semester = Semester::findOrFail($id);
        $semester->update($request->all());

        return redirect('secretary/set_semester/view_semesters')->with('success', 'Semester updated successfully');
    }

    public function destroy1($id)
    {
        $semester = Semester::findOrFail($id);
        $semester->delete();

        return redirect('secretary/set_semester/view_semesters')->with('success', 'Semester deleted successfully');
    }


    public function setupCurrentSemesterView1()
{
    $semesters = Semester::all();

    return view('secretary.set_semester.set_current', compact('semesters'));
}

public function setupCurrentSemester1(Request $request)
{
    
   
    DB::table('semesters')->update(['is_current' => false]);

    Semester::where('id', $request->semester_id)->update(['is_current' => true]);


    return redirect('secretary/set_semester/set_current')->with('success', 'Current semester updated successfully');
}
}
