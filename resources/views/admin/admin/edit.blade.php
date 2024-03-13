@extends('layouts.app')
   
@section('content')
   <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Edit User</h1>
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
              <!-- form start -->
              <form method="post" action="">
                {{ csrf_field() }}
                <div class="card-body">
                  <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" name="name" value="{{ $getData->name }}" required placeholder="Name">
                  </div>
                   <div class="form-group">
                    <label>Middle Name</label>
                    <input type="text" class="form-control" name="middle_name" value="{{ $getData->middle_name }}" placeholder="Middle Name">
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" class="form-control" name="last_name" value="{{ $getData->last_name }}" placeholder="Last Name">
                </div>
                  <div class="form-group">
                    <label>ID Number</label>
                    <input type="number" class="form-control" name="id_number" value="{{ $getData->id_number }}" required placeholder="ID Number">
                  </div>
                  <div class="form-group">
                      <label>Role</label>
                      <select class="form-control" name="role">
                          <option value="1" {{ $getData->role == 1 ? 'selected' : '' }}>Admin</option>
                          <option value="4" {{ $getData->role == 4 ? 'selected' : '' }}>Secretary</option>
                          <option value="2" {{ $getData->role == 2 ? 'selected' : '' }}>Instructor</option>
                          <option value="3" {{ $getData->role == 3 ? 'selected' : '' }}>Student</option>
                      </select>
                  </div>
                  <div class="form-group">
                    <label>Password</label>
                    <input type="text" class="form-control" name="password" placeholder="Password">

                  </div>
                  
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </form>
            </div>
            <!-- /.card -->

            <!-- general form elements -->
           
            <!-- /.card -->

            <!-- Input addon -->
    
             
            <!-- /.card -->
            <!-- Horizontal Form -->
            
            <!-- /.card -->

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