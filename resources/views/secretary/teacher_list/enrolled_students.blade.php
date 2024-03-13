@extends('layouts.app')

@section('content')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
    <h1>Enrolled Students for {{ $subject->description }} ({{ $subject->subject_code }})</h1>

 <table class="table mt-3">
    <thead>
        <tr>
            <th>Name</th>
            <th>ID Number</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @php
            $sortedStudents = $enrolledStudents->groupBy('student.gender');
        @endphp

        @foreach ($sortedStudents as $gender => $students)
            <tr>
                <td colspan="3" class="gender-header">{{ $gender }}</td>
            </tr>

            @foreach ($students as $enrolledStudent)
                <tr>
                    <td>{{ $enrolledStudent->student->last_name }}, {{ $enrolledStudent->student->name }} {{ $enrolledStudent->student->middle_name }}</td>
                    <td>{{ $enrolledStudent->student->id_number }}</td>
                    <td>
                        <a href="{{ route('view.student.points', ['studentId' => $enrolledStudent->student->id, 'subjectId' => $subject->id]) }}" class="btn btn-primary">View Scores</a>
                    </td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>

</div>
</div>
</section>
</div>
@endsection

