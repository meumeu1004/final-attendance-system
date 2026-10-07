<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'student';

    protected $primaryKey = 'student_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'student_id',
        'last_name',
        'first_name',
        'email',
        'password_hash',
        'data_privacy_agreed',
        'section_id',
    ];

    public $timestamps = false;
}