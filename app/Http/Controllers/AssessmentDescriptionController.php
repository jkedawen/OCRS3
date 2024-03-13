<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssessmentDescription;

class AssessmentDescriptionController extends Controller
{
     public function viewDesc()
    {
        $descriptions = AssessmentDescription::all();
        return view('admin.assessment_description.view_desc', compact('descriptions'));
    }

    public function create()
    {
        return view('admin.assessment_description.create_desc');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'description' => 'required|string',
        ]);

        AssessmentDescription::create($request->all());

        return redirect('admin/assessment_description/view_desc')
            ->with('success', 'Assessment description created successfully');
    }

    public function edit(AssessmentDescription $assessmentDescription)
    {
        return view('admin.assessment_description.edit_desc', compact('assessmentDescription'));
    }

    public function update(Request $request, AssessmentDescription $assessmentDescription)
    {
        $request->validate([
            'type' => 'required|string',
            'description' => 'required|string',
        ]);

        $assessmentDescription->update($request->all());

        return redirect('admin/assessment_description/view_desc')
            ->with('success', 'Assessment description updated successfully');
    }

    public function destroy(AssessmentDescription $assessmentDescription)
    {
        $assessmentDescription->delete();

        return redirect('admin/assessment_description/view_desc')
            ->with('success', 'Assessment description deleted successfully');
    }

   public function fetch(Request $request)
{
   
    $type = $request->input('type');

    $descriptions = AssessmentDescription::where('type', $type)->get();

    return response()->json(['descriptions' => $descriptions]);
}
}
