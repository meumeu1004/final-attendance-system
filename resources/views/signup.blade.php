<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Sign up - Attendance Tracker</title>

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

                    <h2>Create a student account</h2>

                    <form method="POST" action="{{ route('signup.store') }}">
                        @csrf

                        <div class="row">

                            <label>
                                First Name
                                <input
                                    type="text"
                                    name="first_name"
                                    autocomplete="given-name"
                                    required
                                >
                            </label>

                            <label>
                                Last Name
                                <input
                                    type="text"
                                    name="last_name"
                                    autocomplete="family-name"
                                    required
                                >
                            </label>

                        </div>


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
                            Section

                            <select name="section_id" required>
                                <option value="" disabled selected>
                                    Select your section
                                </option>

                                @foreach ($sections as $section)
                                    <option value="{{ $section->section_id }}">
                                        {{ $section->section_name }}
                                    </option>
                                @endforeach
                            </select>
                        </label>


                        <label>
                            Email

                            <input
                                type="email"
                                name="email"
                                placeholder="name@example.com"
                                required
                            >
                        </label>


                        <label>
                            Password

                            <span class="pw">

                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    autocomplete="new-password"
                                    pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{7,}"
                                    required
                                >

                                <button
                                    type="button"
                                    class="pw-toggle"
                                    aria-label="Show password"
                                    aria-pressed="false"
                                >
                                                                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/><line class="slash" x1="3" y1="3" x2="21" y2="21"/></svg>
                                </button>

                            </span>
                        </label>


                        <ul class="rules" aria-live="polite">

                            <li data-rule="len">
                                More than 6 characters
                            </li>

                            <li data-rule="up">
                                1 capital letter
                            </li>

                            <li data-rule="low">
                                1 lowercase letter
                            </li>

                            <li data-rule="sp">
                                1 special character
                            </li>

                        </ul>


                        <div
                            class="meter"
                            data-level=""
                            aria-live="polite"
                        >
                            <i></i>
                            <i></i>
                            <i></i>
                            <b></b>
                        </div>


                        <label>
                            Confirm Password

                            <span class="pw">

                                <input
                                    type="password"
                                    id="confirm"
                                    name="password_confirmation"
                                    autocomplete="new-password"
                                    required
                                >

                                <button
                                    type="button"
                                    class="pw-toggle"
                                    aria-label="Show password"
                                    aria-pressed="false"
                                >
                                                                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/><line class="slash" x1="3" y1="3" x2="21" y2="21"/></svg>
                                </button>

                            </span>
                        </label>


                        <p
                            class="match"
                            aria-live="polite"
                        ></p>


                        <label class="check">

                            <input
                                type="checkbox"
                                name="data_privacy_agreed"
                                value="1"
                                required
                            >

                            <span>
                                I agree to the collection and use of my
                                information under the Data Privacy Act of
                                2012 (RA 10173).
                            </span>

                        </label>


                        <button
                            class="btn block"
                            type="submit"
                        >
                            Sign up
                        </button>

                    </form>


                    <span>
                        Already have an account?
                        <a href="{{ route('login') }}">Log in</a>
                    </span>

                </div>

            </main>

        </div>

        <script src="{{ asset('js/auth.js') }}"></script>

    </body>
</html>
