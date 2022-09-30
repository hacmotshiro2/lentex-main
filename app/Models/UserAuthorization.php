<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAuthorization extends Model
{
    use HasFactory;

    protected $table = 'm_userauthorization';

    protected $fillable = [
        'user_id',
        'description',
    ];

    public static $rules = [
        
        //user_id
        'user_id' => ['required','exists:users,id'],
    ];

}
