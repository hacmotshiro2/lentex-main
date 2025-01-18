<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Student;
use App\Models\Session;

class Plan2Attend extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'r_len_plan2attend';

    protected $fillable = [
        'session_id',
        'student_id',
        'description',
    ];

    /**
     * コースとのリレーション
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

}
