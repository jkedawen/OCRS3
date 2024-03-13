  @extends('layouts.app')
   
  @section('content')
<script src="{{ asset('resources/js/import.js') }}"></script>

    <!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <body>
        <h6></h6>
        <div class="container">
            <div class="card bg-light mt-3">
                <div class="card-header">
                    Import Classlist
                </div>
                <div class="card-body">
                    <form action="{{ route('teacher.list.imported-data') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="file" >
                        <button type="submit" class="btn btn-primary">Import</button>
                    </form>

                  

                  
                   @if(isset($subjectExists) && isset($importedClasslistExists))
                        <p>Subject: {{ $subjectExists }}</p>
                        <p>Imported Classlist: {{ $importedClasslistExists }}</p>
                    @endif
                   
                </div>
            </div>
        </div>
    </body>
</div>

<div id="imported-data-container"></div>
    <!-- /.content-header -->

    <!-- Main content -->
   
  <!-- /.content-wrapper -->

  @endsection

  