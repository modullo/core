<?php


namespace App\Http\Controllers\Lms\Resource;


use App\Classes\Lms\Resource\QuizClass;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class QuizController extends Controller
{
    private QuizClass $quizClass;
    public function __construct(){
        $this->quizClass = new QuizClass;
    }

    /**
     * @throws ValidationException
     */
    public function create(Request $request){
        $this->validate($request,[
            "title" => "required",
            "total_quiz_mark" => "required|numeric",
            "quiz_timer" => "required|numeric",
            "disable_on_submit" => "required",
            "retake_on_request" => "required",
            "questions" => "required|array"
        ]);

    }

}