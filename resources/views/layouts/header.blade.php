 <!-- Navbar -->
  <div id="sidebar-wrapper">
    <!-- Left navbar links -->
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="info">
          <a href="#" class="d-block">{{ Auth::user()->name }}</a>
        </div>
      </div>

    

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
         
        @if(Auth::user()->role == 1)
        
          <li class="nav-item">
            <a href="{{ url('admin/admin/list')}}" class="nav-link @if (Request::segment(3) == 'list') active @endif">
              <p>
                User List
              </p>
            </a>
          </li>
           <li class="nav-item">
            <a href="{{ url('admin/subject_types/viewtypes')}}" class="nav-link @if (Request::segment(3) == 'viewtypes') active @endif">
              <p>
                Class Types
              </p>
            </a>
          </li>
           <li class="nav-item">
            <a href="{{ url('admin/assessment_description/view_desc')}}" class="nav-link @if (Request::segment(3) == 'view_desc') active @endif">
              <p>
                Assessment Descriptions
              </p>
            </a>
          </li>

           <li class="nav-item">
            <a href="{{ url('admin/set_semester/set_current')}}" class="nav-link @if (Request::segment(3) == 'set_current') active @endif">
              <p>
                Semester
              </p>
            </a>
          </li>

          <li class="nav-item">
            <a href="{{ url('admin/subject_list/view_subjects')}}" class="nav-link @if (Request::segment(3) == 'view_subjects') active @endif">
              <p>
               Subject List
              </p>
            </a>
          </li>
        @elseif(Auth::user()->role == 2)
      
          <li class="nav-item">
          <a href="{{ url('teacher/list/classlist')}}"  class="nav-link @if (Request::segment(3) == 'classlist') active @endif">
              <p>
                Subject List
              </p>
            </a>
          </li>
          <li class="nav-item">
         <a href="{{ url('teacher/list/importexcel')}}" class="nav-link @if (Request::segment(4) == 'importexcel') active @endif">
              <p>
                Import 
              </p>
            </a>
          </li>
       
        @elseif(Auth::user()->role == 3)
          <li class="nav-item">
         <a  href="{{ url('student/subjectlist', ['studentId' => Auth::user()->id]) }}" class="nav-link @if (Request::segment(3) == 'subjectlist') active @endif">
              <p>
                Subjects
              </p>
            </a>
          </li>
      
         @elseif(Auth::user()->role == 4)
         
          <li class="nav-item">
            <a href="{{ url('secretary/teacher_list/instructor_list')}}" class="nav-link @if (Request::segment(3) == 'instructor_list') active @endif">
              <p>
               Instructors
              </p>
            </a>
          </li>

           <li class="nav-item">
            <a href="{{ url('secretary/subject_types/viewtypes')}}" class="nav-link @if (Request::segment(3) == 'viewtypes') active @endif">
              <p>
                Class Types
              </p>
            </a>
          </li>

           <li class="nav-item">
            <a href="{{ url('secretary/set_semester/set_current')}}" class="nav-link @if (Request::segment(3) == 'set_current') active @endif">
              <p>
                Semester
              </p>
            </a>
          </li>
        @endif
          <li class="nav-item">
            <a href="{{ url('logout')}}" class="nav-link">
              <p>
                Logout
              </p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  </div>