<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Log in - Attendance Tracker</title>

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

                    <h2>Log in</h2>

                    <p class="sub"></p>


                    {{-- Login Error --}}
                    @if ($errors->any())
                        <p class="form-error">
                            {{ $errors->first() }}
                        </p>
                    @endif


                    <form method="POST" action="{{ route('login.store') }}">
                        @csrf

                        <label>
                            Email

                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                autocomplete="username"
                                required
                            >
                        </label>


                        <label>
                            Password

                            <span class="pw">

                                <input
                                    type="password"
                                    name="password"
                                    autocomplete="current-password"
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
                                        <line
                                            class="slash"
                                            x1="3"
                                            y1="3"
                                            x2="21"
                                            y2="21"
                                        />
                                    </svg>
                                </button>

                            </span>
                        </label>


                        <button
                            class="btn block"
                            type="submit"
                        >
                            Log in
                        </button>

                    </form>


                    <div class="link-row">

                        <a href="{{ route('forgot-password') }}">
                            Forgot Password?
                        </a>

                        <span>
                            No account?
                            <a href="{{ route('signup') }}">Sign up</a>
                        </span>

                    </div>


                    <a href="{{ route('home') }}">
                        Back to Home
                    </a>

                </div>

            </main>

        </div>


        {{-- Account Created Modal --}}
        @if (session('account_created'))

            <div
                class="modal"
                id="created"
                role="dialog"
                aria-modal="true"
            >
                <div class="box">

                    <h2>Account Created</h2>

                    <p>
                        Log in to access your account.
                    </p>

                    <button
                        type="button"
                        class="btn"
                        onclick="document.getElementById('created').style.display='none'"
                    >
                        Continue
                    </button>

                </div>
            </div>

        @endif


        {{-- Password Changed Modal --}}
        @if (session('password_changed'))

            <div
                class="modal"
                id="changed"
                role="dialog"
                aria-modal="true"
            >
                <div class="box">

                    <h2>Password Changed</h2>

                    <p>
                        Log in to access your account.
                    </p>

                    <button
                        type="button"
                        class="btn"
                        onclick="document.getElementById('changed').style.display='none'"
                    >
                        Continue
                    </button>

                </div>
            </div>

        @endif


        <script src="{{ asset('js/auth.js') }}"></script>

    </body>
</html>