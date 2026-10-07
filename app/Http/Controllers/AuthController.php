<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Process login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Check admin
        $admin = DB::table('admin')
            ->where('email', $credentials['email'])
            ->first();

        if ($admin && Hash::check($credentials['password'], $admin->password_hash)) {

            $request->session()->regenerate();

            $request->session()->put('user_type', 'admin');
            $request->session()->put('user_id', $admin->admin_id);

            return redirect()->route('professor.dashboard');
        }

        // Check student
        $student = Student::where('email', $credentials['email'])->first();

        if ($student && Hash::check($credentials['password'], $student->password_hash)) {

            $request->session()->regenerate();

            $request->session()->put('user_type', 'student');
            $request->session()->put('user_id', $student->student_id);

            return redirect()->route('student.dashboard');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'The email or password is incorrect.',
            ]);
    }


    // Verify Student ID and Email for password reset
    public function verifyPasswordReset(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|string',
            'email' => 'required|email',
        ]);

        $student = Student::where('student_id', $validated['student_id'])
            ->where('email', $validated['email'])
            ->first();

        if (!$student) {
            return back()
                ->withInput()
                ->withErrors([
                    'student_id' => 'The Student ID and email do not match our records.',
                ]);
        }

        // Store the verified student ID temporarily in the session
        $request->session()->put('password_reset_student_id', $student->student_id);

        return redirect()->route('change-password');
    }


    // Update the student's password
    public function updatePassword(Request $request)
    {
        // Make sure the student passed the verification step
        $studentId = $request->session()->get('password_reset_student_id');

        if (!$studentId) {
            return redirect()->route('forgot-password')
                ->withErrors([
                    'student_id' => 'Please verify your Student ID and email first.',
                ]);
        }

        $validated = $request->validate([
            'password' => [
                'required',
                'confirmed',
                'min:7',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[^A-Za-z0-9]/',
            ],
        ]);

        $student = Student::where('student_id', $studentId)->first();

        if (!$student) {
            $request->session()->forget('password_reset_student_id');

            return redirect()->route('forgot-password')
                ->withErrors([
                    'student_id' => 'Student account could not be found.',
                ]);
        }

        // Update the password
        $student->password_hash = Hash::make($validated['password']);
        $student->save();

        // Remove the temporary reset session
        $request->session()->forget('password_reset_student_id');

        // Redirect to login and trigger the success modal
        return redirect()
            ->route('login')
            ->with('password_changed', true);
    }
}