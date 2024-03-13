<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\SubjectType;

class SubjectTypeController extends Controller
{
      public function viewTypes()
    {
        $subjectTypes = SubjectType::all();

        return view('admin.subject_types.viewtypes', compact('subjectTypes'));
    }

    public function create()
    {
        return view('admin.subject_types.createtypes');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_type' => 'required|unique:subject_type_percentage',
            'lec_percentage' => 'required|numeric|min:0|max:1',
            'lab_percentage' => 'required|numeric|min:0|max:1',
        ]);

        SubjectType::create($request->all());

        return redirect('admin/subject_types/viewtypes')->with('success', 'Subject type added successfully.');
    }

    public function edit($id)
    {
        $subjectType = SubjectType::findOrFail($id);

        return view('admin.subject_types.edittypes', compact('subjectType'));
    }

    public function update(Request $request, $id)
    {
        $subjectType = SubjectType::findOrFail($id);

        $request->validate([
            'subject_type' => 'required|unique:subject_type_percentage,subject_type,' . $id,
            'lec_percentage' => 'required|numeric|min:0|max:1',
            'lab_percentage' => 'required|numeric|min:0|max:1',
        ]);

        $subjectType->update($request->all());

         return redirect('admin/subject_types/viewtypes')->with('success', 'Subject type updated successfully.');
    }

    public function destroy($id)
    {
        $subjectType = SubjectType::findOrFail($id);
        $subjectType->delete();

         return redirect('admin/subject_types/viewtypes')->with('success', 'Subject type deleted successfully.');
    }

    ///////////seceretay side///////////

      public function viewTypes1()
    {
        $subjectTypes = SubjectType::all();

        return view('secretary.subject_types.viewtypes', compact('subjectTypes'));
    }

    public function create1()
    {
        return view('secretary.subject_types.createtypes');
    }

    public function store1(Request $request)
    {
        $request->validate([
            'subject_type' => 'required|unique:subject_type_percentage',
            'lec_percentage' => 'required|numeric|min:0|max:1',
            'lab_percentage' => 'required|numeric|min:0|max:1',
        ]);

        SubjectType::create($request->all());

        return redirect('secretary/subject_types/viewtypes')->with('success', 'Subject type added successfully.');
    }

    public function edit1($id)
    {
        $subjectType = SubjectType::findOrFail($id);

        return view('secretary.subject_types.edittypes', compact('subjectType'));
    }

    public function update1(Request $request, $id)
    {
        $subjectType = SubjectType::findOrFail($id);

        $request->validate([
            'subject_type' => 'required|unique:subject_type_percentage,subject_type,' . $id,
            'lec_percentage' => 'required|numeric|min:0|max:1',
            'lab_percentage' => 'required|numeric|min:0|max:1',
        ]);

        $subjectType->update($request->all());

         return redirect('secretary/subject_types/viewtypes')->with('success', 'Subject type updated successfully.');
    }

    public function destroy1($id)
    {
        $subjectType = SubjectType::findOrFail($id);
        $subjectType->delete();

         return redirect('secretary/subject_types/viewtypes')->with('success', 'Subject type deleted successfully.');
    }
}
