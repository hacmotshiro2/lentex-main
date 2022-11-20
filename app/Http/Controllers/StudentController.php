<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Student;

class StudentController extends Controller
{
    //get student/add
    public function add(Request $request){
        $mode = 'add';
        $item = new Student;
        $items = Student::all();
        
        //クエリ文字列にidがついている場合は、編集モードで開く
        if(isset($request->id)){
            $item = Student::find($request->id);
            $mode = 'edit';
        }
        $args=[
            'mode'=>$mode,
            'item'=>$item,
            'items'=>$items,
        ];
        return view('student.regist',$args);
    }

     //生徒登録画面のPOST
     public function create(Request $request){
        $this->validate($request, Student::$rules);
        $student = new Student;
        $form = $request->all();
        unset($form['_token']);
        $student->fill($form);

        $student->save();

        //登録後の再取得
        $args=[
        ];

        return redirect()->route('student-add',$args);

    }
    //生徒編集画面のPOST
    public function edit(Request $request){
        // print($request->id);
        $this->validate($request, Student::$rules);
        $student = Student::find($request->id);
        $form = $request->all();
        unset($form['_token']);
        $student->fill($form);

        $student->save();

        //登録後の再取得
        $args=[
        ];

        return redirect()->route('student-add',$args);
    }
    //保護者登録画面のPOST
    public function delete(Request $request){

        $student = Student::find($request->id);

        $student->delete();

        //登録後の再取得
        $args=[
        ];

        return redirect()->route('student-add',$args);
    }
}
