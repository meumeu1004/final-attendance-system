<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $adminId = $request->session()->get('user_id');

        // Get sections belonging to this admin
        $sections = DB::table('section')
            ->where('admin_id', $adminId)
            ->orderBy('section_name')
            ->get();

        // Get currently open sessions
        $activeSessions = DB::table('attendance_session')
            ->join(
                'section',
                'attendance_session.section_id',
                '=',
                'section.section_id'
            )
            ->where('attendance_session.admin_id', $adminId)
            ->where('attendance_session.status', 'open')
            ->select(
                'attendance_session.*',
                'section.section_name'
            )
            ->orderBy('attendance_session.date')
            ->orderBy('attendance_session.started_at')
            ->get();

        // Selected filters for Attendance Records
        $selectedSection = $request->input('section_id');
        $selectedDate = $request->input('date');
        $selectedSession = $request->input('session_id');

        // Sessions available for the Attendance Records section
        $recordSessionsQuery = DB::table('attendance_session')
            ->join(
                'section',
                'attendance_session.section_id',
                '=',
                'section.section_id'
            )
            ->where('attendance_session.admin_id', $adminId)
            ->select(
                'attendance_session.*',
                'section.section_name'
            );

        if ($selectedSection) {
            $recordSessionsQuery->where(
                'attendance_session.section_id',
                $selectedSection
            );
        }

        if ($selectedDate) {
            $recordSessionsQuery->where(
                'attendance_session.date',
                $selectedDate
            );
        }

        $recordSessions = $recordSessionsQuery
            ->orderByDesc('attendance_session.date')
            ->orderByDesc('attendance_session.started_at')
            ->get();

        // If no session was manually selected, use the first matching session
        if (!$selectedSession && $recordSessions->count() > 0) {
            $selectedSession = $recordSessions->first()->session_id;
        }

        $selectedSessionData = null;
        $attendanceRecords = collect();
        $presentCount = 0;
        $absentCount = 0;

        if ($selectedSession) {
            $selectedSessionData = DB::table('attendance_session')
                ->join(
                    'section',
                    'attendance_session.section_id',
                    '=',
                    'section.section_id'
                )
                ->where('attendance_session.session_id', $selectedSession)
                ->where('attendance_session.admin_id', $adminId)
                ->select(
                    'attendance_session.*',
                    'section.section_name'
                )
                ->first();

            if ($selectedSessionData) {
                // Get EVERY student in the session's section.
                // LEFT JOIN means students without an attendance
                // record will still appear.
                $attendanceRecords = DB::table('student')
                    ->leftJoin('attendance_record', function ($join) use ($selectedSession) {
                        $join->on(
                            'student.student_id',
                            '=',
                            'attendance_record.student_id'
                        )->where(
                            'attendance_record.session_id',
                            '=',
                            $selectedSession
                        );
                    })
                    ->where(
                        'student.section_id',
                        $selectedSessionData->section_id
                    )
                    ->select(
                        'student.student_id',
                        'student.first_name',
                        'student.last_name',
                        'attendance_record.time_in',
                        'attendance_record.status'
                    )
                    ->orderBy('student.last_name')
                    ->orderBy('student.first_name')
                    ->get();

                $presentCount = $attendanceRecords
                    ->where('status', 'present')
                    ->count();

                $absentCount = $attendanceRecords
                    ->where('status', 'absent')
                    ->count();
            }
        }

        return view('admin.admin-home', compact(
            'sections',
            'activeSessions',
            'recordSessions',
            'selectedSection',
            'selectedDate',
            'selectedSession',
            'selectedSessionData',
            'attendanceRecords',
            'presentCount',
            'absentCount'
        ));
    }

    public function createSession(Request $request)
    {
        $adminId = $request->session()->get('user_id');

        $validated = $request->validate([
            'section_id' => 'required|exists:section,section_id',
            'date' => 'required|date',
            'started_at' => 'required|date',
            'ended_at' => 'required|date|after:started_at',
            'deadline' => 'required|date|after_or_equal:started_at',
        ]);

        // Make sure the section belongs to this admin
        $sectionExists = DB::table('section')
            ->where('section_id', $validated['section_id'])
            ->where('admin_id', $adminId)
            ->exists();

        if (!$sectionExists) {
            return back()
                ->withErrors([
                    'section_id' => 'Invalid section.'
                ])
                ->withInput();
        }

        // Prevent another open session for the same section
        $existingSession = DB::table('attendance_session')
            ->where('section_id', $validated['section_id'])
            ->where('admin_id', $adminId)
            ->where('date', $validated['date'])
            ->where('status', 'open')
            ->exists();

        if ($existingSession) {
            return back()
                ->withErrors([
                    'section_id' => 'This section already has an open attendance session for this date.'
                ])
                ->withInput();
        }

        DB::table('attendance_session')->insert([
            'section_id' => $validated['section_id'],
            'admin_id' => $adminId,
            'started_at' => $validated['started_at'],
            'ended_at' => $validated['ended_at'],
            'deadline' => $validated['deadline'],
            'status' => 'open',
            'date' => $validated['date'],
        ]);

        return redirect()
            ->route('professor.dashboard')
            ->with('session_created', true);
    }

    public function closeSession(Request $request, $sessionId)
    {
        $adminId = $request->session()->get('user_id');

        $session = DB::table('attendance_session')
            ->where('session_id', $sessionId)
            ->where('admin_id', $adminId)
            ->where('status', 'open')
            ->first();

        if (!$session) {
            return redirect()
                ->route('professor.dashboard')
                ->withErrors([
                    'session' => 'The attendance session could not be found.'
                ]);
        }

        // Get all students in this section
        $students = DB::table('student')
            ->where('section_id', $session->section_id)
            ->get();

        foreach ($students as $student) {
            $alreadyRecorded = DB::table('attendance_record')
                ->where('session_id', $session->session_id)
                ->where('student_id', $student->student_id)
                ->exists();

            if (!$alreadyRecorded) {
                DB::table('attendance_record')->insert([
                    'session_id' => $session->session_id,
                    'student_id' => $student->student_id,
                    'time_in' => null,
                    'status' => 'absent',
                ]);
            }
        }

        // Close the session
        DB::table('attendance_session')
            ->where('session_id', $session->session_id)
            ->update([
                'status' => 'closed'
            ]);

        return redirect()
            ->route('professor.dashboard')
            ->with('session_closed', true);
    }
}