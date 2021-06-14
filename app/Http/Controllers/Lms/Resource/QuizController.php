<?php


namespace App\Http\Controllers\Lms\Resource;


use App\Classes\LMS\Resource\QuizClass;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class QuizController extends Controller
{
    private QuizClass $quizClass;

    public function __construct()
    {
        $this->quizClass = new QuizClass;
    }

    /**
     * @throws ValidationException
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            "title" => "required",
            "total_quiz_mark" => "required|numeric",
            "quiz_timer" => "required|numeric",
            "disable_on_submit" => "required",
            "retake_on_request" => "required",
            "questions" => "required",
        ]);
        $user = $request->user();
        return $this->quizClass->createQuiz($user, $request->all());

    }

    public function updateQuestion(Request $request, string $questionId)
    {
        $this->validate($request, [
            "question_text" => "required|string",
            "answer" => "required|string",
            "question_type" => "required|in:options,case_study",
            "score" => "required|numeric",
            "question_number" => "required|numeric"
        ]);
        return $this->quizClass->updateQuestion($questionId, $request->all());
    }

}