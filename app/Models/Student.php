<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $table = 'm_len_students';

    protected $fillable = [
        'verificationName',
        'verificationCode',
        'appDisplayName',
    ];

    public static $rules = [
        
        //verificationName
        'verificationName' => 'required',
        //verificationCode
        'verificationCode'=>'required',
        //appDisplayName
        'appDisplayName'=>'required',
    ];
}
