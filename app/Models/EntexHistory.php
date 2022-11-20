<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntexHistory extends Model
{
    use HasFactory;
    protected $table = 'r_len_entexhistory';

    protected $fillable = [
        'student_id',
        'type',
        'LearningRoomCd',
        'entex_datetime',
    ];

    public static $rules = [
        
        //student_id
        'student_id' => ['required','exists:m_len_students,id'],
        //type
        'type'=>'required',
        //LearningRoomCd
        
        //entex_datetime
        'entex_datetime'=>'required',
    ];
}
