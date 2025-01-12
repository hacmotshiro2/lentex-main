<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\MCourse;

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

}
