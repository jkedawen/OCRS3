@extends('layouts.app')

@section('content')
   

     <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
         <h1>Edit Subject Type</h1>
          </div>
          
        </div>
      </div><!-- /.container-fluid -->
    </section>
     <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-6">
            <!-- general form elements -->
            <div class="card card-primary">
<div class="card-body">
    <form action="{{ route('subject_types.update', $subjectType->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="subject_type">Class Type:</label>
            <input type="text" name="subject_type" class="form-control" value="{{ $subjectType->subject_type }}" required>
        </div>
        <div class="form-group">
            <label for="lec_percentage">Lec Percentage:</label>
            <input type="number" name="lec_percentage" step="0.01" class="form-control" value="{{ $subjectType->lec_percentage }}" required>
        </div>
        <div class="form-group">
            <label for="lab_percentage">Lab Percentage:</label>
            <input type="number" name="lab_percentage" step="0.01" class="form-control" value="{{ $subjectType->lab_percentage }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>

    
</div>
          </div>
          <!--/.col (left) -->
          <!-- right column -->
         
          <!--/.col (right) -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>

@endsection