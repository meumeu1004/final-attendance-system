<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Home - Attendance Tracker</title>
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
                    <p>
                        This system is used only by Professor Reyes to record and monitor
                        the attendance of his students in COMP 143 - Web Development.
                        Students log in on class days to record their attendance and review
                        their own records; the professor keeps the full class record in one place.
                    </p>

                    <div class="actions">
                        <a class="btn" href="{{ route('login') }}">Log in</a>
                        <a class="btn alt" href="{{ route('signup') }}">Sign up</a>
                    </div>

                    <a href="{{ route('password.request') }}">Forgot password?</a>
                </div>
            </main>
        </div>
    </body>
</html>