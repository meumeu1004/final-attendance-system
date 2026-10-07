<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function showSignup()
    {
        $sections = DB::table('section')->get();

        return view('signup', compact('sections'));
    }
}