<?php


namespace App\Classes\Lms\Resource;


use App\Classes\ModulloClass;
use App\Exceptions\CustomValidationFailed;
use App\Exceptions\RecordNotFoundException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\UnableToCreateResourceException;
use App\Http\Resources\Lms\QuizQuestionsResource;
use App\Http\Resources\Lms\QuizResource;
use App\Models\Lms\Quiz;
use App\Models\Lms\QuizQuestions;
use App\Models\Lms\Tenants;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use LogicException;
use Ramsey\Uuid\Uuid;

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

    protected array $updateFields = [
        "title" => "title",
        "total_quiz_mark" => "total_quiz_mark",
        "quiz_timer" => "quiz_timer",
        "disable_on_submit" => "disable_on_submit",
        "retake_on_request" => "retake_on_request",
        "question_text" => "question_text",
        "answer" => "answer",
        "question_type" => "question_type",
        "score" => "score",
        "question_number" => "question_number",
        "options" => "options"

    ];


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
                isset($question['answer']) & isset($question['question_number'])) {

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

    public function updateQuestion(string $questionId,array $data)
    {
        if($this->validateSingleQuestion($data))
        {
            $question = $this->quizQuestions->newQuery()->where('uuid',$questionId)->first();
            if (!$question) throw new ResourceNotFoundException('this question does not exists in our records');
            $this->updateModelAttributes($question,$data);
            $question->save();
            $resource = new QuizQuestionsResource($question);
            return  response()->updated('Question updated successfully',$resource,"question");
        }else{
            throw new LogicException('Quiz questions not properly formatted');
        }

    }

    public function deleteQuestion($questionId)
    {
        $this->quizQuestions->newQuery()->where('uuid',$questionId)->delete();
        return response()->deleted("Question deleted successfully","true","deleted");
    }

    private function validateUpdateQuizQuestions($quiz,$questions): bool
    {
        //validate and prepare quiz questions
        $quiz_array = [];
        $defected = false;
        foreach($questions as $question)
        {
            if(isset($question['question_text'])   && isset($question['score']) && isset($question['answer']) && isset($question['options']))
            {

                if(is_array($question['options'] ) && (count($question['options']) >0)){
                    //prepare quiz for insert Many
                    if(isset($question['id']))
                    {
                        $this->quizQuestionRepo->where('id',$question['id'])
                            ->where("quiz_id" , $quiz->id)
                            ->update([
                                "question_text" => $question['question_text'],
                                "score" => $question['score'],
                                "answer" => $question['answer'],
                                "options" => json_encode($question['options'])] );

                    }else{
                        $this->quizQuestionRepo
                            ->create([ "id" => Uuid::uuid1(),
                                "tenant_id" => $tenantId,
                                "question_text" => $question['question_text'],
                                'quiz_id' => $quiz->id,
                                "score" => $question['score'],
                                "answer" => $question['answer'],
                                "options" => json_encode($question['options'])] );
                    }
                }else{

                    throw new CustomValidationFailed("Question are not well formatted");
                }

            }else{
                throw new CustomValidationFailed("Question are not well formatted");
            }

        }

        return true;



    }

    private function validateSingleQuestion(&$question): bool
    {
        if(isset($question['question_text']) && isset($question['question_text'])  && isset($question['question_type'])
            && isset($question['score']) && isset($question['answer']) && $question['question_number'])
        {
            if($question['question_type'] === 'options' &&
                isset($question['options']) && is_array($question['options'] ) && (count($question['options']) >0)){
                 $question['options'] = json_encode($question['options']);
                //prepare quiz for insert Many
                return true;
            }
            elseif ($question['question_type'] === 'case_study'){
                $question['options'] = json_encode([]);
                return true;
            }
            throw new LogicException('ensure you are sending appropriate data for the particular question');
        }
        return false;
    }

    public function updateQuiz($tenantId,$quizId,$title,$reward,$total_quiz_mark,$quiz_timer,$disable_on_submit,$retake_on_request,$questions)
    {
        $quiz =  $this->quizRepo->find($quizId);
        $updated =  $this->quizRepo->whereId($quizId)->update([
            "tenant_id" => $tenantId,
            "title" => $title,
            "reward" => $reward,
            "total_quiz_mark" => $total_quiz_mark,
            "quiz_timer" => $quiz_timer,
            "disable_on_submit" => $disable_on_submit,
            "retake_on_request" => $retake_on_request
        ]);

        if($updated && $quiz)
        {
            DB::transaction(function () use($tenantId,$quiz,$questions) {
                $this->validateUpdateQuizQuestions($tenantId,$quiz,$questions);
            });
            return  $this->updated('Quiz updated successfully',$updated,"update");
        }else{
            throw new UnableToCreateResourceException('Unable to update quiz');
        }

    }

    public function getAllQuiz($tenantId)
    {
        $quizzes = $this->quizRepo->whereTenantId($tenantId)->get();
        return $this->fetch("All tenant quiz fetched",$quizzes,"quizzes");
    }


    public function getSingleQuiz($tenantId,$quizId)
    {
        $quiz =  $this->quizRepo->whereTenantId($tenantId)->whereId($quizId)->with('questions')->first();

        if($quiz)
        {
            return $this->get(" tenant quiz fetched",$quiz,"quiz");
        }else{
            throw new RecordNotFoundException('Quiz not found');
        }
    }

}