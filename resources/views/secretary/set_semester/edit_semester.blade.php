@extends('layouts.app')

@section('content')


<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">

<div class="container">
    <h1>Edit Semester</h1>

    <form method="post" action="{{ route('semesters.update', $semester->id) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Semester</label>
            <input type="text" class="form-control" name="semester_name" value="{{ $semester->semester_name }}" required>
        </div>
        <div class="form-group">
            <label for="school_year">School Year</label>
            <input type="text" class="form-control" name="school_year" value="{{ $semester->school_year }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
    </div>
</div>
</section>
</div>
@endsection