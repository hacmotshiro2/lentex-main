<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\MCourse;
use App\Models\Plan2Attend;

class Session extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'r_len_session';

    protected $fillable = [
        'LearningRoomCd',
        'course_id',
        'sessionStartTime',
        'sessionEndTime',
    ];

    /**
     * コースとのリレーション
     */
    public function course()
    {
        return $this->belongsTo(MCourse::class, 'course_id');
    }
    // リレーションを定義
    public function plan2attends(): HasMany
    {
        return $this->hasMany(Plan2Attend::class, 'session_id', 'id');
    }
    

}
