<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineUser extends Model
{
    use HasFactory;

    protected $table = 'm_len_lineusers';

    protected $fillable = [
        'student_id',
        'lineDisplayName',
        'lineUserId',
    ];

    public static $rules = [
        
        //student_id
        'student_id' => ['required','exists:m_len_students,id'],
        //lineDisplayName
        'lineDisplayName'=>'required',
        //lineUserId
        'lineUserId'=>'required',
    ];

}
