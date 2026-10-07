<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Professor Dashboard</title>

    <link rel="stylesheet" href="{{ asset('css/admin-styles.css') }}">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap"
        rel="stylesheet">
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


<main class="page">

    {{-- Success Messages --}}

    @if (session('session_created'))
        <div class="alert success">
            Attendance session created successfully.
        </div>
    @endif

    @if (session('session_closed'))
        <div class="alert success">
            Attendance session closed successfully.
        </div>
    @endif

    {{-- Error Messages --}}

    @if ($errors->any())
        <div class="alert error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- ===================================================== --}}
    {{-- ACTIVE SESSIONS --}}
    {{-- ===================================================== --}}

    <section class="admin-section">

        <div class="section-heading">
            <div>
                <h2 class="section-title">Active Sessions</h2>
                <p class="section-description">
                    Attendance sessions that are currently open.
                </p>
            </div>
        </div>

        @if ($activeSessions->isEmpty())

            <div class="empty-state">
                <i class="fa-regular fa-calendar-xmark"></i>
                <p>No active attendance sessions.</p>
            </div>

        @else

            <div class="session-list">

                @foreach ($activeSessions as $session)

                    <article class="session-card">

                        <div class="session-card-header">

                            <div>
                                <h3>
                                    Session #{{ str_pad($session->session_id, 3, '0', STR_PAD_LEFT) }}
                                </h3>

                                <p class="section-name">
                                    {{ $session->section_name }}
                                </p>
                            </div>

                            <span class="session-status">
                                Open
                            </span>

                        </div>


                        <div class="session-details">

                            <p>
                                <strong>Date:</strong>
                                {{ \Carbon\Carbon::parse($session->date)->format('F j, Y') }}
                            </p>

                            <p>
                                <strong>Start of Class:</strong>
                                {{ \Carbon\Carbon::parse($session->started_at)->format('g:i A') }}
                            </p>

                            <p>
                                <strong>End of Class:</strong>
                                {{ \Carbon\Carbon::parse($session->ended_at)->format('g:i A') }}
                            </p>

                            <p>
                                <strong>Attendance Deadline:</strong>
                                {{ \Carbon\Carbon::parse($session->deadline)->format('F j, Y g:i A') }}
                            </p>

                        </div>


                        <form
                            method="POST"
                            action="{{ route('professor.session.close', $session->session_id) }}"
                            class="close-session-form"
                            onsubmit="return confirm('Close this attendance session? Students who have not submitted will be marked absent.');"
                        >
                            @csrf

                            <button class="btn close-btn" type="submit">
                                Close Attendance
                            </button>
                        </form>

                    </article>

                @endforeach

            </div>

        @endif

    </section>


    {{-- ===================================================== --}}
    {{-- OPEN NEW SESSION --}}
    {{-- ===================================================== --}}

    <section class="admin-section">

        <div class="section-heading">
            <div>
                <h2 class="section-title">Open New Session</h2>
                <p class="section-description">
                    Create a new attendance session for a section.
                </p>
            </div>
        </div>


        <form
            class="card session-form"
            action="{{ route('professor.session.create') }}"
            method="POST"
        >

            @csrf

            <div class="session-form-grid">

                {{-- Section --}}

                <div class="form-row">
                    <label for="section_id">Section</label>

                    <select name="section_id" id="section_id" required>

                        <option value="" disabled
                            {{ old('section_id') ? '' : 'selected' }}>
                            Select section
                        </option>

                        @foreach ($sections as $section)

                            <option
                                value="{{ $section->section_id }}"
                                {{ old('section_id') == $section->section_id ? 'selected' : '' }}
                            >
                                {{ $section->section_name }}
                            </option>

                        @endforeach

                    </select>
                </div>


                {{-- Start of Class --}}

                <div class="form-row">
                    <label for="started_at">Start of Class</label>

                    <input
                        type="time"
                        id="started_at"
                        name="started_at"
                        value="{{ old('started_at') }}"
                        required
                    >
                </div>


                {{-- Attendance Deadline --}}

                <div class="form-row">
                    <label for="deadline">Attendance Deadline</label>

                    <input
                        type="time"
                        id="deadline"
                        name="deadline"
                        value="{{ old('deadline') }}"
                        required
                    >
                </div>


                {{-- Date --}}

                <div class="form-row">
                    <label for="date">Date</label>

                    <input
                        type="date"
                        id="date"
                        name="date"
                        value="{{ old('date') }}"
                        required
                    >
                </div>


                {{-- End of Class --}}

                <div class="form-row">
                    <label for="ended_at">End of Class</label>

                    <input
                        type="time"
                        id="ended_at"
                        name="ended_at"
                        value="{{ old('ended_at') }}"
                        required
                    >
                </div>

            </div>


            <div class="session-form-button">

                <button class="btn" type="submit">
                    Create Session
                </button>

            </div>

        </form>

    </section>


    {{-- ===================================================== --}}
    {{-- ATTENDANCE RECORDS --}}
    {{-- ===================================================== --}}

    <section class="admin-section">

        <div class="section-heading">
            <div>
                <h2 class="section-title">Attendance Records</h2>
                <p class="section-description">
                    View attendance records for active and completed sessions.
                </p>
            </div>
        </div>


        {{-- Filters --}}

        <form
            class="card record-filter-card"
            method="GET"
            action="{{ route('professor.dashboard') }}"
        >

            <div class="record-filters">

                <div class="filter">
                    <label for="record-section">Section</label>

                    <select
                        name="section_id"
                        id="record-section"
                        onchange="this.form.submit()"
                    >

                        <option value="">All Sections</option>

                        @foreach ($sections as $section)

                            <option
                                value="{{ $section->section_id }}"
                                {{ $selectedSection == $section->section_id ? 'selected' : '' }}
                            >
                                {{ $section->section_name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="filter">

                    <label for="record-date">Date</label>

                    <input
                        type="date"
                        id="record-date"
                        name="date"
                        value="{{ $selectedDate }}"
                        onchange="this.form.submit()"
                    >

                </div>


                <div class="filter">

                    <label for="record-session">Session</label>

                    <select
                        name="session_id"
                        id="record-session"
                        onchange="this.form.submit()"
                    >

                        <option value="">Select Session</option>

                        @foreach ($recordSessions as $recordSession)

                            <option
                                value="{{ $recordSession->session_id }}"
                                {{ $selectedSession == $recordSession->session_id ? 'selected' : '' }}
                            >
                                Session #{{ str_pad($recordSession->session_id, 3, '0', STR_PAD_LEFT) }}
                                —
                                {{ $recordSession->section_name }}
                                —
                                {{ \Carbon\Carbon::parse($recordSession->date)->format('M j, Y') }}
                            </option>

                        @endforeach

                    </select>

                </div>

            </div>

        </form>


        @if ($selectedSessionData)

            {{-- Session information --}}

            <div class="record-session-info">

                <div>
                    <strong>
                        Session #{{ str_pad($selectedSessionData->session_id, 3, '0', STR_PAD_LEFT) }}
                    </strong>

                    <span>
                        {{ $selectedSessionData->section_name }}
                    </span>
                </div>

                <div>
                    <strong>Status</strong>

                    <span class="record-session-status {{ $selectedSessionData->status }}">
                        {{ ucfirst($selectedSessionData->status) }}
                    </span>
                </div>

                <div>
                    <strong>Date</strong>

                    <span>
                        {{ \Carbon\Carbon::parse($selectedSessionData->date)->format('F j, Y') }}
                    </span>
                </div>

            </div>


            {{-- Summary --}}

            <div class="attendance-summary">

                <div class="summary-box present-summary">
                    <span>Present</span>
                    <strong>{{ $presentCount }}</strong>
                </div>

                <div class="summary-box absent-summary">
                    <span>Absent</span>
                    <strong>{{ $absentCount }}</strong>
                </div>

            </div>


            {{-- Records table --}}

            <div class="card table-wrap">

                <table>

                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Time In</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($attendanceRecords as $record)

                            <tr>

                                <td>
                                    {{ $record->student_id }}
                                </td>

                                <td>
                                    {{ $record->last_name }},
                                    {{ $record->first_name }}
                                </td>

                                <td>

                                    @if ($record->time_in)
                                        {{ \Carbon\Carbon::parse($record->time_in)->format('g:i A') }}
                                    @else
                                        —
                                    @endif

                                </td>

                                <td>

                                    @if ($record->status === 'present')

                                        <span class="status status-present">
                                            Present
                                        </span>

                                    @elseif ($record->status === 'absent')

                                        <span class="status status-absent">
                                            Absent
                                        </span>

                                    @else

                                        <span class="status status-pending">
                                            Not submitted
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="4">
                                    No students found for this session.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        @else

            <div class="empty-state records-empty">

                <i class="fa-solid fa-clipboard-list"></i>

                <p>
                    Select a session to view attendance records.
                </p>

            </div>

        @endif

    </section>

</main>

</body>
</html>