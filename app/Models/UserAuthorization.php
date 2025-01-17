<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAuthorization extends Model
{
    use HasFactory;
    use SoftDeletes;

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
