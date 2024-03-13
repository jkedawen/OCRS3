@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">

    <div class="container">
    <h1>Create Semester</h1>

    <form method="post" action="{{ route('semesters.store') }}">
        @csrf
        <div class="form-group">
            <label for="name">Semester</label>
          
             <select class="form-control" name="semester_name" required>
                    <option value="" disabled selected>--- Select Semester ---</option>
                    <option value="First Semester">First Semester</option>
                    <option value="Second Semester">Second Semester</option>
                    <option value="Short Term">Short Term</option>
                </select>
        </div>
        <div class="form-group">
            <label for="school_year">School Year</label>
            <input type="text" class="form-control" name="school_year" placeholder="example: 2023 - 2024" required>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
    </div>

</div>
</div>
</section>
</div>
@endsection