@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit Time Log</h1>

        <form action="{{ route('projects.update', $timeLog->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $timeLog->project->name) }}" required>
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control">{{ old('description', $timeLog->project->description) }}</textarea>
                @error('description')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="start_time">Start Time</label>
                <input type="datetime-local" name="start_time" id="start_time" class="form-control" value="{{ old('start_time', $timeLog->start_time) }}" required>
                @error('start_time')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="end_time">End Time</label>
                <input type="datetime-local" name="end_time" id="end_time" class="form-control" value="{{ old('end_time', $timeLog->end_time) }}">
                @error('end_time')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-warning">Update</button>
            <a href="{{ route('projects.index') }}" class="btn btn-secondary">Back to Time Logs</a>
        </form>
    </div>
@endsection
