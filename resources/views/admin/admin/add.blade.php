@extends('layouts.app')
   
@section('content')
   <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Add User</h1>
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
                    <input type="text" class="form-control" name="name" required placeholder="Name">
                  </div>
                  <div class="form-group">
                      <label>Middle Name</label>
                      <input type="text" class="form-control" name="middle_name" placeholder="Middle Name">
                  </div>
                  <div class="form-group">
                      <label>Last Name</label>
                      <input type="text" class="form-control" name="last_name" placeholder="Last Name">
                  </div>
                  <div class="form-group">
                    <label>ID Number</label>
                    <input type="number" class="form-control" name="id_number" required placeholder="ID Number">
                  </div>
                  <div class="form-group">
                    <label>Role</label>
                     <select class="form-control" name="role" required> 
                       <option value="" disabled selected>--- Select Role ---</option>
                       <option value="1">Admin</option>
                       <option value="4">Secretary</option>
                       <option value="2">Instructor</option>
                       <option value="3">Student</option>

                    </select>
                  </div>
               <div class="form-group">
                  <label>Password</label>
                  <input type="password" class="form-control" name="password" required placeholder="Password"
                         pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$"
                         title="Password must contain at least 8 characters, including atleast one letter and one number.">
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