@extends('layouts.app') 

@section('content')
    <div class="content-wrapper">
   
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
           
          </div>
          
        </div>
      </div>
    </section>


    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-6">
            <!-- general form elements -->
            <div class="card card-primary">
              <!-- form start -->
             <form method="post" action="{{ route('admin.confirm-password', ['id' => $userId]) }}">
            {{ csrf_field() }}
   <div class="card-body">
            <div class="form-group">
            <label for="password">Enter your password to proceed:</label>
            <input type="password" name="password" required>
       </div>

        </div>
             <div class="card-footer">
            <button type="submit" class="btn btn-primary">Confirm</button>
        </div>
        </form>
               </div>
         

          </div>
         
        </div>
     
      </div>
    </section>
    
  </div>
@endsection

 