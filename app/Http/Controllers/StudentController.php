<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    // Show the signup page
    public function showSignup()
    {
        $sections = DB::table('section')->get();

        return view('signup', compact('sections'));
    }


    // Process the signup form
    public function signup(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',

            'student_id' => 'required|string|max:20|unique:student,student_id',

            'section_id' => 'required|exists:section,section_id',

            'email' => 'required|email|max:100|unique:student,email',

            'password' => [
                'required',
                'confirmed',
                'min:7',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[^A-Za-z0-9]/',
            ],

            'data_privacy_agreed' => 'required|accepted',
        ]);


        // Create the student account
        Student::create([
            'student_id' => $validated['student_id'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],

            // Hash the password before saving it
            'password_hash' => Hash::make($validated['password']),

            'data_privacy_agreed' => true,
            'section_id' => $validated['section_id'],
        ]);


        // Go to login and trigger the Account Created modal
        return redirect()
            ->route('login')
            ->with('account_created', true);
    }

    public function dashboard(Request $request){
        $studentId = $request->session()->get('user_id');

        $student = Student::where('student_id', $studentId)->firstOrFail();

        // Find today's open attendance session for the student's section
        $attendanceSession = DB::table('attendance_session')
            ->where('section_id', $student->section_id)
            ->where('date', now()->toDateString())
            ->where('status', 'open')
            ->where('started_at', '<=', now())
            ->where('deadline', '>=', now())
            ->first();

        // Check if the student already submitted attendance
        $attendanceSubmitted = false;

        if ($attendanceSession) {
            $attendanceSubmitted = DB::table('attendance_record')
                ->where('session_id', $attendanceSession->session_id)
                ->where('student_id', $student->student_id)
                ->exists();
        }

        // Get the student's attendance history
        $attendanceRecords = DB::table('attendance_record')
            ->join(
                'attendance_session',
                'attendance_record.session_id',
                '=',
                'attendance_session.session_id'
            )
            ->where('attendance_record.student_id', $student->student_id)
            ->orderByDesc('attendance_session.date')
            ->select(
                'attendance_session.date',
                'attendance_record.time_in',
                'attendance_record.status'
            )
            ->get();

        return view('student.student-home', compact(
            'student',
            'attendanceSession',
            'attendanceSubmitted',
            'attendanceRecords'
        ));
    }

    public function storeAttendance(Request $request){
        $studentId = $request->session()->get('user_id');

        $student = Student::where('student_id', $studentId)->firstOrFail();

        $attendanceSession = DB::table('attendance_session')
            ->where('section_id', $student->section_id)
            ->where('date', now()->toDateString())
            ->where('status', 'open')
            ->where('started_at', '<=', now())
            ->where('deadline', '>=', now())
            ->first();

        if (!$attendanceSession) {
            return redirect()
                ->route('student.dashboard')
                ->withErrors([
                    'attendance' => 'Attendance is not currently available.'
                ]);
        }

        $alreadySubmitted = DB::table('attendance_record')
            ->where('session_id', $attendanceSession->session_id)
            ->where('student_id', $student->student_id)
            ->exists();

        if ($alreadySubmitted) {
            return redirect()
                ->route('student.dashboard')
                ->withErrors([
                    'attendance' => 'Your attendance has already been recorded.'
                ]);
        }

        DB::table('attendance_record')->insert([
            'session_id' => $attendanceSession->session_id,
            'student_id' => $student->student_id,
            'time_in' => now(),
            'status' => 'present',
        ]);

        return redirect()
            ->route('student.dashboard')
            ->with('attendance_recorded', true);
    }
}
