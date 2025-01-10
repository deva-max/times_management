@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Time Logs</h1>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Duration</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($timeLogs as $timeLog)
                    <tr>
                        <td>{{ $timeLog->id }}</td>
                        <td>{{ $timeLog->project->name }}</td>
                        <td>{{ $timeLog->project->description }}</td>
                        <td>{{ $timeLog->start_time }}</td>
                        <td>{{ $timeLog->end_time }}</td>
                        <td>{{ $timeLog->duration }}</td>
                        <td>
                            <a href="{{ route('projects.edit', $timeLog->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            @if(Auth::check() && Auth::user()->role === 'admin')
                                <form action="{{ route('projects.destroy', $timeLog->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
