<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntexHistory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'r_len_entexhistory';

    //2023/08/08 Paginationを使うために、Elloquent仕様に書き換える

    // private static $select = "
    // SELECT 
    // MAIN.id,
    // MAIN.student_id,
    // mst.messageDispName,
    // MAIN.type,
    // CASE MAIN.type WHEN 1 THEN '入室' WHEN 2 THEN '退室' ELSE '' END as typeName,
    // MAIN.LearningRoomCd,
    // MAIN.entex_datetime,
    // MAIN.created_at,
    // MAIN.updated_at,
    // MAIN.deleted_at

    // FROM r_len_entexhistory MAIN 
    // LEFT OUTER JOIN m_len_students mst
    // ON mst.id = MAIN.student_id 
    // ";

    // // private static $orderby = "
    // //     ORDER BY MAIN.student_id ,MAIN.entex_datetime DESC
    // // ";
    // private static $orderby = "
    //     ORDER BY MAIN.entex_datetime DESC, MAIN.student_id
    // ";

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

    //2023/08/08 Paginationを使うために、Elloquent仕様に書き換える

    // //すべての生徒の入退室履歴を確認する
    // public static function getEntexHistoryAll(){

    //     return DB::select(
    //     self::$select."    
    //     WHERE 
    //     MAIN.deleted_at IS NULL
    //     ".self::$orderby);


    // }
    // //生徒ごとの入退室履歴を確認する
    // public static function getEntexHistory(string $student_id){

    //     $param = [
    //         'student_id'=>$student_id,
    //     ];
    //     return DB::select(
    //     self::$select."    
    //     WHERE MAIN.student_id = :student_id
    //     AND MAIN.deleted_at IS NULL
    //     ".self::$orderby
    //     ,$param);


    // }
    


    //2023/08/08 Paginationを使うために、Elloquent仕様に書き換える

    // 外部キーの設定
    // Studentマスタ
    public function student(){
        return $this->belongsTo('App\Models\Student');
    }

    //TypeNameはDBには持っておらず、typeから判断してつくる
    // 1 → 入室　2 → 退室
    public function getTypeNameAttribute(){

        $typeName = "";

        switch($this->type){

            case 1:
                $typeName = "入室";
                break;
            case 2:
                $typeName = "退室";
                break;
            default:
                break;
        }
        return $typeName;
    }
    //LINE用 例) 11/16 14:40 入室 
    public function getFormattedHistoryAttribute(){
        //例) 11/16 14:40 入室
        return date('m/d H:i', strtotime(($this->entex_datetime)))." ".$this->typeName;
    }

}
