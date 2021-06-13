<?php


namespace App\Classes\Lms\Resource;


use App\Classes\ModulloClass;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Resources\Lms\QuizResource;
use App\Models\Lms\Quiz;
use App\Models\Lms\QuizQuestions;
use App\Models\Lms\Tenants;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use LogicException;

class QuizClass extends ModulloClass
{
    private QuizQuestions $quizQuestions;
    private Tenants $tenants;
    private Quiz $quiz;

    public function __construct()
    {
        $this->tenants = new Tenants;
        $this->quiz = new Quiz;
        $this->quizQuestions = new QuizQuestions;
    }


    public function createQuiz(object $user, array $data)
    {
        $quiz = null;
        DB::transaction(function () use ($user, $data, &$quiz) {
            $tenant = $this->tenants->newQuery()->where('lms_user_id', $user->id)->first();
            if (!$tenant) {
                throw new ResourceNotFoundException('unfortunately the tenant could not be found');
            }
            $quiz = $this->quiz->newQuery()->create([
                "tenant_id" => $tenant->id,
                "title" => $data['title'],
                "total_quiz_mark" => $data['total_quiz_mark'],
                "quiz_timer" => $data['quiz_timer'],
                "disable_on_submit" => $data['disable_on_submit'],
                "retake_on_request" => $data['retake_on_request']
            ]);
            if (!$quiz) {
                throw new LogicException('something went wrong while creating a quiz. kindly raise a ticket
                 with admin to have this problem resolved');
            }
            $questions = $this->validateQuizQuestions($quiz->id, $data['questions']);
            if (!count($questions) > 0) throw  new LogicException('Quiz questions data are not well formatted, ensure you send an array of questions with the proper values.. check Modullo Docs for proper clarification');
            $this->quizQuestions->newQuery()->insert($questions);
        });
        $resource = new QuizResource($quiz);
        return response()->created('Quiz created successfully',$resource,"quiz");
    }

    private function validateQuizQuestions(string $quiz_id, array $questions): array
    {
        //validate and prepare quiz questions
        $quiz_array = [];
        $defected = false;
        foreach ($questions as $question) {
            if (isset($question['question_text']) && isset($question['score']) &&
                isset($question['answer'])) {

                if (isset($question['options']) && is_array($question['options']) && (count($question['options']) > 0) && $question['question_type'] === 'options') {
                    //prepare quiz for insert Many
                    array_push($quiz_array, array_merge([ "uuid" => Str::uuid(),"quiz_id" => $quiz_id, "created_at" => Carbon::now(), "updated_at" => Carbon::now()],
                        $question, ["options" => json_encode($question['options'])]));
                }
                elseif ($question['question_type'] && $question['question_type'] === 'case_study'){
                    array_push($quiz_array, array_merge([ "uuid" => Str::uuid(),"quiz_id" => $quiz_id,"created_at" => Carbon::now(), "updated_at" => Carbon::now()],
                        $question,["options" => json_encode([])]));
                }
                else {
                    return [];
                }

            } else {
                return [];

            }


        }

        return $defected ? [] : $quiz_array;


    }

}