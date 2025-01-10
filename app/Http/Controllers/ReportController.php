<?php

namespace App\Http\Controllers;

use App\Models\TimeLogs;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->role !== 'admin') {
                abort(403, 'Unauthorized action.');
            }

            return $next($request);
        });
    }


    public function create()
    {
        return view ('reports.index');
    }
    public function generateReport(Request $request)
    {
        $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',
            'report_type' => 'required|in:daily,weekly,monthly', // Report type
        ]);

        // Parse the dates
        $startDate = Carbon::parse($request->start_time);
        $endDate = Carbon::parse($request->end_time);

        // Retrieve the time logs based on date range and report type
        $timeLogs = $this->generateTimeLogsReport($startDate, $endDate, $request->report_type);

        return view('reports.index', compact('timeLogs'));
    }

    public function downloadCSV(Request $request)
    {
        $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',
            'report_type' => 'required|in:daily,weekly,monthly', // Report type
        ]);

        // Parse the dates
        $startDate = Carbon::parse($request->start_time);
        $endDate = Carbon::parse($request->end_time);

        // Retrieve the time logs based on date range and report type
        $timeLogs = $this->generateTimeLogsReport($startDate, $endDate, $request->report_type);

        // Generate the CSV data
        $csvData = "Date/Week/Month, Total Hours, Project Name\n";

        foreach ($timeLogs as $log) {
            $csvData .= "{$log->date}, {$log->total_hours}, {$log->project->name}\n";
        }

        // Create a response with the CSV content
        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="report.csv"');
    }

    /**
     * Common method to generate time logs report based on the given date range and report type
     *
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     * @param  string  $reportType
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function generateTimeLogsReport($startDate, $endDate, $reportType)
    {
        // Start building the time logs query
        $timeLogsQuery = TimeLogs::whereBetween('start_time', [$startDate, $endDate])
                                ->with('project'); // Eager load the project relationship

        // If the user needs to break the report down by daily, weekly, or monthly
        if ($reportType === 'daily') {
            // Breakdown by day
            return $timeLogsQuery->selectRaw('DATE(start_time) as date, SUM(TIMESTAMPDIFF(HOUR, start_time, end_time)) as total_hours, project_id')
                                ->groupBy(DB::raw('DATE(start_time)'), 'project_id')
                                ->get();
        } elseif ($reportType === 'weekly') {
            // Breakdown by week
            return $timeLogsQuery->selectRaw('WEEK(start_time) as week, SUM(TIMESTAMPDIFF(HOUR, start_time, end_time)) as total_hours, project_id')
                                ->groupBy(DB::raw('WEEK(start_time)'), 'project_id')
                                ->get();
        } else { // Default is 'monthly'
            // Breakdown by month
            return $timeLogsQuery->selectRaw('MONTH(start_time) as month, SUM(TIMESTAMPDIFF(HOUR, start_time, end_time)) as total_hours, project_id')
                                ->groupBy(DB::raw('MONTH(start_time)'), 'project_id')
                                ->get();
        }

    }
}