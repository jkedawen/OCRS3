<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\SubjectType;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    public function index()
  {
    $subjects = Subject::all();
    
    return view('teacher.list.classlist', compact('subjects'));
  }

  public function updateSubjectType(Request $request, Subject $subject)
    {
        
    $subjectTypePercentages = SubjectType::pluck('subject_type')->toArray();

   
    $allSubjectTypes = array_merge(['Lec', 'Lab'], $subjectTypePercentages);

    
    $request->validate([
        'subject_type' => ['required', Rule::in($allSubjectTypes)],
    ]);

    $subject->update([
        'subject_type' => $request->input('subject_type'),
    ]);

        return redirect()->back()->with('success', 'Calculation type updated successfully.');
    }
}
