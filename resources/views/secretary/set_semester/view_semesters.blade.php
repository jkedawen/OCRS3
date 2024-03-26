@extends('layouts.app')

@section('content')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
<div class="container">
    <h1>Semesters</h1>
      @include('messages')
        <div class="col-sm-6" style="text-align: right;">
    <a href="{{ route('semesters.create1') }}" class="btn btn-primary">Add Semester</a>
</div>
    <table class="table mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Semester</th>
                <th>School Year</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($semesters as $semester)
                <tr>
                    <td>{{ $semester->id }}</td>
                    <td>{{ $semester->semester_name }}</td>
                    <td>{{ $semester->school_year }}</td>
                    <td>
                        <a href="{{ route('semesters.edit1', $semester->id) }}" class="btn btn-primary">Edit</a>
                        <form action="{{ route('semesters.destroy1', $semester->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>
</div>
</section>
</div>
@endsection
