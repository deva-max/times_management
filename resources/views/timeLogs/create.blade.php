@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Create Project</h1>

        <form action="{{ route('projects.store') }}" method="POST">
            @csrf

            <!-- Admin Condition: If the user is an admin, allow project creation only (no time logs) -->
            @if(Auth::user()->role === 'admin')
                <div class="form-group">
                    <label for="name">Project Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            @else
                <!-- Employee Condition: Allow time log creation for employees -->

                <div class="form-group">
                    <label for="start_time">Start Time</label>
                    <input type="datetime-local" name="start_time" id="start_time" class="form-control" value="{{ old('start_time') }}" required>
                    @error('start_time')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="end_time">End Time</label>
                    <input type="datetime-local" name="end_time" id="end_time" class="form-control" value="{{ old('end_time') }}">
                    @error('end_time')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Assuming the employee has to select a project they are assigned to -->
                <div class="form-group">
                    <label for="project_id">Assign to Project</label>
                    <select name="project_id" id="project_id" class="form-control">
                        <option value="">Select a project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                        @endforeach
                    </select>
                    @error('project_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            @endif

            <button type="submit" class="btn btn-success">{{ Auth::user()->role === 'admin' ? 'Create Project' : 'Create Time Log' }}</button>
            <a href="{{ route('projects.index') }}" class="btn btn-secondary">Back to Projects</a>
        </form>
    </div>
@endsection
