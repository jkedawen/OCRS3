@extends('layouts.app')

@section('content')
   

    <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
<div class="container">
    <h1>Assessments for {{ $subject->description }} </h1>
      @include('messages')
        
    <table class="table mt-3">
        <thead>
            <tr>
                <th>Grading Period</th>
                <th>Type</th>
                <th>Description</th>
                <th>Max Points</th>
                <th>Activity Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assessments as $assessment)
                <tr>
                    <td>{{ $assessment->grading_period }}</td>
                    <td>{{ $assessment->type }}</td>
                    <td>{{ $assessment->description }}</td>
                    <td>{{ $assessment->max_points }}</td>
                    <td>{{ $assessment->activity_date }}</td>
                    <td>
                      <a href="{{ route('instructor.editSingleAssessment', ['assessmentId' => $assessment->id]) }}"  class="btn btn-primary">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>

       
</div>
</div>
</section>

<div class="container mt-3">
    <a href="{{ route('teacher.list.studentlist', ['subject' => $subject->id]) }}" class="btn btn-primary">Back</a>
</div>

</div>
@endsection

