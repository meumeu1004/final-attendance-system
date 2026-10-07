<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Change password - Attendance Tracker</title>
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
                    <h2>Change password</h2>

                    <form data-next="#changed">
                        <label>
                            New Password
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
                                    <svg
                                        viewBox="0 0 24 24"
                                        width="20"
                                        height="20"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        aria-hidden="true"
                                    >
                                        <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                        <line class="slash" x1="3" y1="3" x2="21" y2="21"/>
                                    </svg>
                                </button>
                            </span>
                        </label>

                        <ul class="rules" aria-live="polite">
                            <li data-rule="len">More than 6 characters</li>
                            <li data-rule="up">1 capital letter</li>
                            <li data-rule="low">1 lowercase letter</li>
                            <li data-rule="sp">1 special character</li>
                        </ul>

                        <div class="meter" data-level="" aria-live="polite">
                            <i></i>
                            <i></i>
                            <i></i>
                            <b></b>
                        </div>

                        <label>
                            Repeat Password
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
                                    <svg
                                        viewBox="0 0 24 24"
                                        width="20"
                                        height="20"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        aria-hidden="true"
                                    >
                                        <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                        <line class="slash" x1="3" y1="3" x2="21" y2="21"/>
                                    </svg>
                                </button>
                            </span>
                        </label>

                        <p class="match" aria-live="polite"></p>

                        <button class="btn block" type="submit">
                            Change Password
                        </button>
                    </form>
                </div>
            </main>
        </div>

        <div class="modal" id="changed" role="dialog" aria-modal="true">
            <div class="box">
                <h2>Password Changed</h2>
                <p>Log in to access your account.</p>
                <a class="btn" href="{{ route('login') }}">Go to log in</a>
            </div>
        </div>

        <script src="{{ asset('js/auth.js') }}"></script>
    </body>
</html>