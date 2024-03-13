@extends('layouts.app')

@section('content')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">


    <div class="container">
        <h2>Edit Assessment Description</h2>
        <form action="{{ route('assessment-descriptions.update', $assessmentDescription->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="type">Type:</label>
                <input type="text" name="type" class="form-control" value="{{ $assessmentDescription->type }}" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <input type="text" name="description" class="form-control" value="{{ $assessmentDescription->description }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Description</button>
        </form>
    </div>

</div>
</div>
</section>
</div>

@endsection