<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'm_len_students';

    protected $fillable = [
        'verificationName',
        'verificationCode',
        'appDispName',
        'messageDispName',
        'lsuppoStudentCd',
    ];

    public static $rules = [
        
        //verificationName
        'verificationName' => 'required',
        //verificationCode
        'verificationCode'=>'required',
        //appDispName
        'appDispName'=>'required',
        //messageDispName
        'messageDispName'=>'required',
        //lsuppoStudentCd
        // 'lsuppoStudentCd'=>'required',
    ];
}
