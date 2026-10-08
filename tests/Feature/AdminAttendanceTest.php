<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAttendanceTest extends TestCase
{
    use RefreshDatabase;

    private int $adminId;

    private int $sectionId;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.key' => 'base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=']);

        $this->adminId = DB::table('admin')->insertGetId([
            'name' => 'Test Admin',
            'email' => 'admin@example.test',
            'password_hash' => bcrypt('password'),
        ]);

        $this->sectionId = DB::table('section')->insertGetId([
            'section_name' => 'BSIT 3-1',
            'admin_id' => $this->adminId,
        ]);
    }

    public function test_admin_can_create_a_session_and_values_are_persisted(): void
    {
        $response = $this
            ->withSession(['user_type' => 'admin', 'user_id' => $this->adminId])
            ->post(route('professor.session.create'), [
                'section_id' => $this->sectionId,
                'date' => '2026-10-08',
                'started_at' => '09:00',
                'ended_at' => '10:30',
                'deadline' => '09:15',
            ]);

        $response->assertRedirect(route('professor.dashboard'));
        $response->assertSessionHas('session_created', true);

        $this->assertDatabaseHas('attendance_session', [
            'section_id' => $this->sectionId,
            'admin_id' => $this->adminId,
            'started_at' => '2026-10-08 09:00:00',
            'ended_at' => '2026-10-08 10:30:00',
            'deadline' => '2026-10-08 09:15:00',
            'status' => 'open',
            'date' => '2026-10-08',
        ]);
    }

    public function test_admin_dashboard_renders_the_three_sections_and_navigation(): void
    {
        $this->createSession();

        $response = $this
            ->withSession(['user_type' => 'admin', 'user_id' => $this->adminId])
            ->get(route('professor.dashboard'));

        $response->assertOk();
        $response->assertSee('Active Sessions');
        $response->assertSee('Session #001');
        $response->assertSee('BSIT 3-1');
        $response->assertSee('Close Attendance');
        $response->assertSee('Open New Session');
        $response->assertSee('Attendance Records');
        $response->assertSee('href="#active-sessions"', false);
        $response->assertSee('href="#create-session"', false);
        $response->assertSee('href="#attendance-records"', false);
    }

    public function test_admin_login_opens_the_admin_dashboard(): void
    {
        DB::table('admin')
            ->where('admin_id', $this->adminId)
            ->update(['password_hash' => Hash::make('ValidPass123!')]);

        $response = $this->post(route('login.store'), [
            'email' => 'admin@example.test',
            'password' => 'ValidPass123!',
        ]);

        $response->assertRedirect(route('professor.dashboard'));
        $response->assertSessionHas('user_type', 'admin');
        $response->assertSessionHas('user_id', $this->adminId);

        $this->get(route('professor.dashboard'))->assertOk();
    }

    public function test_guest_and_student_cannot_open_the_admin_dashboard(): void
    {
        $this->get(route('professor.dashboard'))
            ->assertRedirect(route('login'));

        $this->withSession(['user_type' => 'admin'])
            ->get(route('professor.dashboard'))
            ->assertRedirect(route('login'));

        $this->withSession(['user_type' => 'student', 'user_id' => '2023-10001-SR-1'])
            ->get(route('professor.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_logout_and_session_is_invalidated(): void
    {
        $response = $this
            ->withSession(['user_type' => 'admin', 'user_id' => $this->adminId])
            ->post(route('logout'));

        $response->assertRedirect(route('login'));
        $response->assertSessionMissing('user_type');
        $response->assertSessionMissing('user_id');
        $this->get(route('login'))->assertOk();
        $this->get(route('professor.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_cannot_create_a_session_for_another_admins_section(): void
    {
        $otherAdminId = DB::table('admin')->insertGetId([
            'name' => 'Other Admin',
            'email' => 'other@example.test',
            'password_hash' => bcrypt('password'),
        ]);

        $otherSectionId = DB::table('section')->insertGetId([
            'section_name' => 'BSIT 3-2',
            'admin_id' => $otherAdminId,
        ]);

        $response = $this
            ->withSession(['user_type' => 'admin', 'user_id' => $this->adminId])
            ->from(route('professor.dashboard'))
            ->post(route('professor.session.create'), [
                'section_id' => $otherSectionId,
                'date' => '2026-10-08',
                'started_at' => '09:00',
                'ended_at' => '10:30',
                'deadline' => '09:15',
            ]);

        $response->assertRedirect(route('professor.dashboard'));
        $response->assertSessionHasErrors('section_id');
        $this->assertDatabaseCount('attendance_session', 0);
    }

    public function test_closing_a_session_marks_unrecorded_students_absent_and_persists_changes(): void
    {
        $sessionId = DB::table('attendance_session')->insertGetId([
            'section_id' => $this->sectionId,
            'admin_id' => $this->adminId,
            'started_at' => '2026-10-08 09:00:00',
            'ended_at' => '2026-10-08 10:30:00',
            'deadline' => '2026-10-08 09:15:00',
            'status' => 'open',
            'date' => '2026-10-08',
        ]);

        DB::table('student')->insert([
            [
                'student_id' => '2023-10001-SR-1',
                'last_name' => 'Dela Cruz',
                'first_name' => 'Juan',
                'email' => 'juan@example.test',
                'password_hash' => bcrypt('password'),
                'data_privacy_agreed' => true,
                'section_id' => $this->sectionId,
            ],
            [
                'student_id' => '2023-10002-SR-1',
                'last_name' => 'Santos',
                'first_name' => 'Ana',
                'email' => 'ana@example.test',
                'password_hash' => bcrypt('password'),
                'data_privacy_agreed' => true,
                'section_id' => $this->sectionId,
            ],
        ]);

        DB::table('attendance_record')->insert([
            'session_id' => $sessionId,
            'student_id' => '2023-10001-SR-1',
            'time_in' => '2026-10-08 09:05:00',
            'status' => 'present',
        ]);

        $response = $this
            ->withSession(['user_type' => 'admin', 'user_id' => $this->adminId])
            ->post(route('professor.session.close', $sessionId));

        $response->assertRedirect(route('professor.dashboard'));
        $response->assertSessionHas('session_closed', true);
        $this->assertDatabaseHas('attendance_session', [
            'session_id' => $sessionId,
            'status' => 'closed',
        ]);
        $this->assertDatabaseHas('attendance_record', [
            'session_id' => $sessionId,
            'student_id' => '2023-10001-SR-1',
            'status' => 'present',
            'time_in' => '2026-10-08 09:05:00',
        ]);
        $this->assertDatabaseHas('attendance_record', [
            'session_id' => $sessionId,
            'student_id' => '2023-10002-SR-1',
            'status' => 'absent',
            'time_in' => null,
        ]);
        $this->assertDatabaseCount('attendance_record', 2);

        $dashboard = $this
            ->withSession(['user_type' => 'admin', 'user_id' => $this->adminId])
            ->get(route('professor.dashboard'));

        $dashboard->assertOk();
        $dashboard->assertSee('No active attendance sessions.');
        $dashboard->assertSee('Closed');
        $dashboard->assertSee('Absent');
    }

    public function test_student_can_submit_attendance_before_the_deadline(): void
    {
        $studentId = $this->createStudent();
        $sessionId = $this->createSession();
        $this->travelTo(Carbon::parse('2026-10-08 09:10:00'));

        $response = $this
            ->withSession(['user_type' => 'student', 'user_id' => $studentId])
            ->post(route('student.attendance.store'));

        $response->assertRedirect(route('student.dashboard'));
        $response->assertSessionHas('attendance_recorded', true);
        $this->assertDatabaseHas('attendance_record', [
            'session_id' => $sessionId,
            'student_id' => $studentId,
            'status' => 'present',
            'time_in' => '2026-10-08 09:10:00',
        ]);
    }

    public function test_student_cannot_submit_attendance_after_deadline(): void
    {
        $studentId = $this->createStudent();
        $this->createSession();
        $this->travelTo(Carbon::parse('2026-10-08 09:16:00'));

        $response = $this
            ->withSession(['user_type' => 'student', 'user_id' => $studentId])
            ->post(route('student.attendance.store'));

        $response->assertRedirect(route('student.dashboard'));
        $response->assertSessionHasErrors('attendance');
        $this->assertDatabaseCount('attendance_record', 0);
    }

    public function test_student_cannot_submit_before_session_start_or_after_session_closes(): void
    {
        $studentId = $this->createStudent();
        $sessionId = $this->createSession();
        $this->travelTo(Carbon::parse('2026-10-08 08:59:00'));

        $beforeStart = $this
            ->withSession(['user_type' => 'student', 'user_id' => $studentId])
            ->post(route('student.attendance.store'));

        $beforeStart->assertSessionHasErrors('attendance');
        $this->travelBack();

        $this->withSession(['user_type' => 'admin', 'user_id' => $this->adminId])
            ->post(route('professor.session.close', $sessionId))
            ->assertSessionHas('session_closed', true);
        $this->travelTo(Carbon::parse('2026-10-08 09:10:00'));

        $afterClose = $this
            ->withSession(['user_type' => 'student', 'user_id' => $studentId])
            ->post(route('student.attendance.store'));

        $afterClose->assertSessionHasErrors('attendance');
        $this->assertDatabaseHas('attendance_session', [
            'session_id' => $sessionId,
            'status' => 'closed',
        ]);
        $this->assertDatabaseHas('attendance_record', [
            'session_id' => $sessionId,
            'student_id' => $studentId,
            'status' => 'absent',
        ]);
    }

    private function createStudent(): string
    {
        $studentId = '2023-10001-SR-1';

        DB::table('student')->insert([
            'student_id' => $studentId,
            'last_name' => 'Dela Cruz',
            'first_name' => 'Juan',
            'email' => 'juan@example.test',
            'password_hash' => Hash::make('ValidPass123!'),
            'data_privacy_agreed' => true,
            'section_id' => $this->sectionId,
        ]);

        return $studentId;
    }

    private function createSession(): int
    {
        return DB::table('attendance_session')->insertGetId([
            'section_id' => $this->sectionId,
            'admin_id' => $this->adminId,
            'started_at' => '2026-10-08 09:00:00',
            'ended_at' => '2026-10-08 10:30:00',
            'deadline' => '2026-10-08 09:15:00',
            'status' => 'open',
            'date' => '2026-10-08',
        ]);
    }
}
