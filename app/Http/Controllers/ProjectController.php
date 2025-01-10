<?php

namespace App\Http\Controllers;

use App\Models\Projects;
use App\Models\TimeLogs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; // To check the currently authenticated user

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $timeLogs = TimeLogs::with('project')->get(); // Fetch all time logs

        if($request->is('api/*')){
            return response()->json($timeLogs);
        }
        
        return view('timeLogs.index', compact('timeLogs')); // Change to timeLogs view
    }

    public function show($id)
    {
        // Implement the logic to show a project details
    }

    public function create()
    {
        if(Auth::user()->role === 'employee'){
            $projects = Projects::SELECT('name','id')->get();
        }
        return view('timeLogs.create', compact('projects')); // Return to the create time log view (for employees only)
    }

    public function store(Request $request)
    {
        // If the logged-in user is an admin, they can only create the project (not time logs)
        if (Auth::user()->role === 'admin') {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $validatedData = $validator->validated();

            // Create the project without any time log
            $project = Projects::create([
                'name' => $validatedData['name'],
                'description' => $validatedData['description'] ?? null,
            ]);

            return redirect()->route('projects.index')->with('success', 'Project created successfully.');
        }

        // If the logged-in user is an employee, allow them to create a time log for an existing project
        if (Auth::user()->role === 'employee') {
            $validator = Validator::make($request->all(), [
                'start_time' => 'required|date',
                'end_time' => 'nullable|date|after_or_equal:start_time',
                'project_id' => 'required|exists:projects,id', // Ensure the project exists
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $validatedData = $validator->validated();

            // Create a new time log with the associated project
            $timeLog = TimeLogs::create([
                'start_time' => $validatedData['start_time'],
                'end_time' => $validatedData['end_time'],
                'user_id' => Auth::id(), // The logged-in employee
                'project_id' => $validatedData['project_id'],
            ]);

            return redirect()->route('projects.index')->with('success', 'Time log created successfully.');
        }

        // If the user role is not admin or employee, redirect with error
        return redirect()->route('projects.index')->with('error', 'Unauthorized access.');
    }


    public function edit($id)
    {
        $timeLog = TimeLogs::findOrFail($id); // Fetch the specific time log by ID
        return view('timeLogs.edit', compact('timeLog')); // Change to timeLogs edit view
    }

    public function update(Request $request, $id)
    {
        // Ensure only employees can update their own time logs
        if (Auth::user()->role !== 'employee') {
            return redirect()->route('projects.index')->with('error', 'You are not authorized to update time logs.');
        }

        $validator = Validator::make($request->all(), [
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();

        $timeLog = TimeLogs::findOrFail($id); // Find the time log by ID

        // Update the time log
        $timeLog->update([
            'start_time' => $validatedData['start_time'],
            'end_time' => $validatedData['end_time'] ?? null,
        ]);

        return redirect()->route('projects.index')->with('success', 'Time log updated successfully.');
    }

    public function destroy($id)
    {
        // Ensure only admins can delete projects (and related time logs)
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('projects.index')->with('error', 'You are not authorized to delete this project.');
        }

        $timeLog = TimeLogs::findOrFail($id);
        $timeLog->project()->delete(); // Delete the associated project
        $timeLog->delete(); // Delete the time log

        return redirect()->route('projects.index')->with('success', 'Time log and associated project have been deleted.');
    }
}

