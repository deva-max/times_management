@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Time Logs Report</h1>

        <!-- Filter form to select report type -->
        <form action="{{ route('reports.generate') }}" method="POST">
            @csrf
            <div class="form-row">
                <div class="col-md-4">
                    <label for="start_time">Start Date</label>
                    <input type="date" name="start_time" id="start_time" class="form-control" value="{{ request()->start_time ?? '' }}" required>
                </div>
                <div class="col-md-4">
                    <label for="end_time">End Date</label>
                    <input type="date" name="end_time" id="end_time" class="form-control" value="{{ request()->end_time ?? '' }}" required>
                </div>
                <div class="col-md-4">
                    <label for="report_type">Report Type</label>
                    <select name="report_type" id="report_type" class="form-control" required>
                        <option value="daily" {{ request()->report_type == 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="weekly" {{ request()->report_type == 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ request()->report_type == 'monthly' ? 'selected' : '' }}>Monthly</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Generate Report</button>
        </form>

        @if(isset($timeLogs) && $timeLogs->isNotEmpty())
            <!-- Table to display the report data -->
            <table class="table table-bordered mt-4">
                <thead>
                    <tr>
                        <th>Date/Week/Month</th>
                        <th>Total Hours</th>
                        <th>Project</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($timeLogs as $log)
                        <tr>
                            <td>{{ $log->date ?? $log->week ?? $log->month }}</td>
                            <td>{{ $log->total_hours }} Hours</td>
                            <td>{{ $log->project->name ?? 'N/A' }}</td> <!-- Project name -->
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Download CSV Button -->
            <form action="{{ route('reports.downloadCSV') }}" method="POST">
                @csrf
                <input type="hidden" name="start_time" value="{{ request()->start_time }}">
                <input type="hidden" name="end_time" value="{{ request()->end_time }}">
                <input type="hidden" name="report_type" value="{{ request()->report_type }}">
                <button type="submit" class="btn btn-success mt-3">Download CSV</button>
            </form>

        @else
            <p>No time logs available for the selected range.</p>
        @endif
    </div>
@endsection
