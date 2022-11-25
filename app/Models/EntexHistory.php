<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EntexHistory extends Model
{
    use HasFactory;
    protected $table = 'r_len_entexhistory';

    private static $select = "
    SELECT 
    MAIN.id,
    MAIN.student_id,
    mst.messageDispName,
    MAIN.type,
    CASE MAIN.type WHEN 1 THEN '入室' WHEN 2 THEN '退室' ELSE '' END as typeName,
    MAIN.LearningRoomCd,
    MAIN.entex_datetime,
    MAIN.created_at,
    MAIN.updated_at,
    MAIN.deleted_at

    FROM r_len_entexhistory MAIN 
    LEFT OUTER JOIN m_len_students mst
    ON mst.id = MAIN.student_id 
    ";

    private static $orderby = "
        ORDER BY MAIN.student_id ,MAIN.entex_datetime DESC
    ";

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
    //すべての生徒の入退室履歴を確認する
    public static function getEntexHistoryAll(){

        return DB::select(
        self::$select."    
        WHERE 
        MAIN.deleted_at IS NULL
        ".self::$orderby);


    }
    //生徒ごとの入退室履歴を確認する
    public static function getEntexHistory(string $student_id){

        $param = [
            'student_id'=>$student_id,
        ];
        return DB::select(
        self::$select."    
        WHERE MAIN.student_id = :student_id
        AND MAIN.deleted_at IS NULL
        ".self::$orderby
        ,$param);


    }
    
}
