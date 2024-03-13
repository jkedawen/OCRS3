@extends('layouts.app')

@section('content')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
 
    <h1>Subjects Taught by {{ $instructor->name }} {{ $instructor->middle_name }} {{ $instructor->last_name }}</h1>
<table class="table mt-3">

     <thead>
                <tr>
                    <th>Subject Name</th>
                    <th>Subject Code</th>
                    <th>Action</th>
                   
                </tr>
            </thead>
    <tbody>
        @foreach($subjects as $subject)
          <tr>
             <td>{{ $subject->subject->description }}</td>
             <td>{{ $subject->subject->subject_code }}</td>
             <td>  <a href="{{ route('secretary.teacher_list.enrolled_students', ['subject' => $subject->subject->id]) }}"class="btn btn-info btn-sm">View Enrolled Students</a></td>
               </tr>
        @endforeach
    </tbody>

   </table>
</div>
</div>
</section>
</div>
@endsection

 