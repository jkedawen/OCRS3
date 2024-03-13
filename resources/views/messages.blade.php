@if(!empty(session('error'))) 
<div class="alert alert-danger " role="alert"> 
    {{ session('error') }} 
     <button type="button" class="close" data-dismiss="alert">×</button>   
</div> 
@endif
@if (!empty(session('success')))
<div class="alert alert-success alert-block">
     {{ session('success') }} 
     <button type="button" class="close" data-dismiss="alert">×</button>    
</div>
@endif
@if(session('warning'))
    <div class="alert alert-warning">
        {{ session('warning') }}
         <button type="button" class="close" data-dismiss="alert">×</button>   
    </div>
@endif