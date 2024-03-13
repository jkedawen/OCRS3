@extends('layouts.app')

@section('content')
@push('scripts')
<script>
    $(document).ready(function () {
        
        $('#semester_id').change(function () {
            $('#currentSemesterForm').submit();
        });
    });
</script>
@endpush

<div class="content-wrapper">
   
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           <a href="{{ url('admin/set_semester/view_semesters')}}" class="btn btn-primary">Modify Semesters</a>
          </div>
          
        </div>
      </div>
    </section>
  
  <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-6">
            <!-- general form elements -->
            <div class="card card-primary">
   @include('messages')
    <form method="post" action="{{ route('semesters.setupCurrent') }}">
        @csrf

         <div class="card-body">
            <div class="form-group">
            <label for="semester_id">Current Semester:</label>
            <select class="form-control" id="semester_id" name="semester_id" required>
               @foreach ($semesters as $semester)
            <option value="{{ $semester->id }}" {{ $semester->is_current ? 'selected' : '' }}>
                {{ $semester->semester_name }}, {{ $semester->school_year }}
            </option>
           @endforeach
            </select>
        </div>
         </div>

        </div>
          <div class="card-footer">
        <button type="submit" class="btn btn-primary">Set as Current</button>
    </div>
    </form>

    </div>
         

          </div>
         
        </div>
     
      </div>
    </section>
    
  </div>
@endsection

