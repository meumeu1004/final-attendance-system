<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Forgot password - Attendance Tracker</title>

        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    </head>

    <body>

        <div class="split">

            <aside class="aside">
                <p class="school">
                    Polytechnic University of the Philippines<br>
                    Santa Rosa Campus
                </p>

                <p class="course">
                    COMP 143 - Web Development<br>
                    Prof. Reyes
                </p>

                <h1>ATTENDANCE TRACKER</h1>
            </aside>

            <main class="main">
                <div class="card">

                    <h2>Forgot password</h2>

                    <p class="sub">
                        Enter the Student ID and Email you signed up with.
                    </p>

                    <form method="POST" action="{{ route('password.verify') }}">
                        @csrf

                        <label>
                            Student ID
                            <input
                                type="text"
                                name="student_id"
                                placeholder="0000-00000-SR-0"
                                pattern="\d{4}-\d{5}-SR-\d"
                                title="Format: 0000-00000-SR-0"
                                required
                            >
                        </label>

                        <label>
                            Email
                            <input
                                type="email"
                                name="email"
                                required
                            >
                        </label>

                        <button class="btn block" type="submit">
                            Continue
                        </button>
                    </form>

                    <a href="{{ route('login') }}">
                        Back to log in
                    </a>

                </div>
            </main>

        </div>

        <script src="{{ asset('js/auth.js') }}"></script>

    </body>
</html>
