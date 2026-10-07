<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Home - Attendance Tracker</title>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    </head>

    <body>
        <header class="topbar">
            <div class="header-left">
                <i class="fa-solid fa-calendar-check"></i>

                <div class="header-text">
                    <strong>Attendance Tracker</strong>
                    <span>COMP 143 - Web Development, Prof. Reyes</span>
                </div>
            </div>

            <button class="icon-btn" aria-label="Log out">
                <i class="fa-solid fa-right-from-bracket"></i>
            </button>
        </header>


        <main class="dash">

            <!-- Take Attendance -->
            <section class="take">
                <div class="panel">
                    <h2>Take attendance</h2>

                    <p class="date">
                        {{ now()->format('m/d/Y') }}
                    </p>

                    @if ($attendanceSession && !$attendanceSubmitted)

                        <div class="attendance-info">
                            <p>
                                <strong>Class time:</strong>
                                {{ \Carbon\Carbon::parse($attendanceSession->started_at)->format('g:i A') }}
                                –
                                {{ \Carbon\Carbon::parse($attendanceSession->ended_at)->format('g:i A') }}
                            </p>

                            <p>
                                <strong>Attendance deadline:</strong>
                                {{ \Carbon\Carbon::parse($attendanceSession->deadline)->format('g:i A') }}
                            </p>
                        </div>

                        <form method="POST" action="{{ route('student.attendance.store') }}">
                            @csrf

                            <button class="btn light" type="submit">
                                Submit Attendance
                            </button>
                        </form>

                    @elseif ($attendanceSubmitted)

                        <p>
                            Your attendance has already been recorded.
                        </p>

                    @else

                        <p>
                            No attendance required for this date.
                        </p>

                    @endif

                </div>
            </section>


            <!-- Attendance Records -->
            <section class="panel">
                <h2>My Attendance Records</h2>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time in</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($attendanceRecords as $record)
                                <tr>
                                    <td>{{ $record->date }}</td>

                                    <td>
                                        {{ $record->time_in }}
                                    </td>

                                    <td>
                                        <span class="tag {{ $record->status }}">
                                            {{ ucfirst($record->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

        </main>


        <!-- Account Information -->
        <div class="modal" id="account" role="dialog" aria-modal="true">
            <div class="box">

                <h2>Account Information</h2>

                <dl>
                    <dt>Name</dt>
                    <dd>
                        {{ $student->first_name }} {{ $student->last_name }}
                    </dd>

                    <dt>Student ID</dt>
                    <dd>
                        {{ $student->student_id }}
                    </dd>

                    <dt>Email</dt>
                    <dd>
                        {{ $student->email }}
                    </dd>
                </dl>

                <a class="btn alt" href="#">Close</a>

            </div>
        </div>


        <!-- Attendance Recorded -->
        <div class="modal" id="recorded" role="dialog" aria-modal="true">
            <div class="box">

                <h2>Attendance recorded</h2>

                <p>
                    Your attendance for today has been saved.
                </p>

                <a class="btn" href="#">
                    Done
                </a>

            </div>
        </div>

    </body>
</html>
