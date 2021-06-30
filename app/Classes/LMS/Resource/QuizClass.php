<?php

namespace App\Classes\Lms\Resource;

use App\Classes\ModulloClass;
use App\Exceptions\CustomValidationFailed;
use App\Exceptions\RecordNotFoundException;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Resources\Lms\QuizQuestionsResource;
use App\Http\Resources\Lms\QuizResource;
use App\Models\Lms\Lessons;
use App\Models\Lms\LmsQuizReport;
use App\Models\Lms\Quiz;
use App\Models\Lms\QuizQuestions;
use App\Models\Lms\Tenants;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use LogicException;
use Ramsey\Uuid\Uuid;

class QuizClass extends ModulloClass {
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
		"options" => "options",

	];
	private QuizQuestions $quizQuestions;
	private Tenants $tenants;
	private Quiz $quiz;
	private Lessons $lessons;
	private LmsQuizReport $lmsQuizReport;

	public function __construct() {
		$this->tenants = new Tenants;
		$this->quiz = new Quiz;
		$this->quizQuestions = new QuizQuestions;
		$this->lmsQuizReport = new LmsQuizReport;
		$this->lessons = new Lessons;
	}

	public function createQuiz(string $tenantId, array $data) {
		$quiz = null;
		DB::transaction(function () use ($tenantId, $data, &$quiz) {
			$tenant = $this->tenants->newQuery()->where('id', $tenantId)->first();
			if (!$tenant) {
				throw new ResourceNotFoundException('unfortunately the tenant could not be found');
			}
			$quiz = $this->quiz->newQuery()->create([
				"tenant_id" => $tenant->id,
				"title" => $data['title'],
				"total_quiz_mark" => $data['total_quiz_mark'],
				"quiz_timer" => $data['quiz_timer'],
				"disable_on_submit" => $data['disable_on_submit'],
				"retake_on_request" => $data['retake_on_request'],
			]);
			if (!$quiz) {
				throw new LogicException('something went wrong while creating a quiz. kindly raise a ticket
                 with admin to have this problem resolved');
			}
			$questions = $this->validateQuizQuestions($quiz->id, $data['questions']);
			if (!count($questions) > 0) {
				throw new LogicException('Quiz questions data are not well formatted, ensure you send an array of questions with the proper values.. check Modullo Docs for proper clarification');
			}

			$this->quizQuestions->newQuery()->insert($questions);
		});
		$resource = new QuizResource($quiz);
		return response()->created('Quiz created successfully', $resource, "quiz");
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
					array_push($quiz_array, array_merge(["uuid" => Str::uuid(), "quiz_id" => $quiz_id, "created_at" => Carbon::now(), "updated_at" => Carbon::now()],
						$question, ["options" => json_encode($question['options'])]));
				} elseif ($question['question_type'] && $question['question_type'] === 'case_study') {
					array_push($quiz_array, array_merge(["uuid" => Str::uuid(), "quiz_id" => $quiz_id, "created_at" => Carbon::now(), "updated_at" => Carbon::now()],
						$question, ["options" => json_encode([])]));
				} else {
					return [];
				}

			} else {
				return [];

			}

		}

		return $defected ? [] : $quiz_array;
	}

	public function updateQuestion(string $questionId, array $data) {
		if ($this->validateSingleQuestion($data)) {
			$question = $this->quizQuestions->newQuery()->where('uuid', $questionId)->first();
			if (!$question) {
				throw new ResourceNotFoundException('this question does not exists in our records');
			}

			$this->updateModelAttributes($question, $data);
			$question->save();
			$resource = new QuizQuestionsResource($question);
			return response()->updated('Question updated successfully', $resource, "question");
		} else {
			throw new LogicException('Quiz questions not properly formatted');
		}

	}

	public function addQuestion(string $quizId, array $data) {
		$quiz = $this->quiz->newQuery()->where('uuid', $quizId)->first();
		if (!$quiz) {
			throw new ResourceNotFoundException('this quiz does not exist in our records ');
		}

		$quizQuestions = null;
		if (isset($data['question_text']) && isset($data['question_text']) && isset($data['question_type'])
			&& isset($data['score']) && isset($data['answer']) && $data['question_number']) {
			if ($data['question_type'] === 'options' && isset($data['options']) && is_array($data['options']) && (count($data['options']) > 0)) {
				//prepare quiz for insert Many
				$quizQuestions = $this->quizQuestions
					->newQuery()
					->create([
						"uuid" => Str::uuid(),
						"quiz_id" => $quiz->id,
						"question_text" => $data['question_text'],
						"score" => $data['score'],
						"answer" => $data['answer'],
						"options" => json_encode($data['options']),
						'question_type' => $data['question_type'],
						'question_number' => $data['question_number'],
					]);
			} elseif ($data['question_type'] === 'case_study') {
				$quizQuestions = $this->quizQuestions
					->newQuery()
					->create([
						"uuid" => Str::uuid(),
						"quiz_id" => $quiz->id,
						"question_text" => $data['question_text'],
						"score" => $data['score'],
						"answer" => $data['answer'],
						"options" => json_encode([]),
						'question_type' => $data['question_type'],
						'question_number' => $data['question_number'],
					]);
			}
		}
		$resource = new QuizQuestionsResource($quizQuestions);
		return response()->created('question created successfully', $resource, 'quiz_questions');
	}

	private function validateSingleQuestion(&$question): bool {
		if (isset($question['question_text']) && isset($question['question_text']) && isset($question['question_type'])
			&& isset($question['score']) && isset($question['answer']) && $question['question_number']) {
			if ($question['question_type'] === 'options' &&
				isset($question['options']) && is_array($question['options']) && (count($question['options']) > 0)) {
				$question['options'] = json_encode($question['options']);
				//prepare quiz for insert Many
				return true;
			} elseif ($question['question_type'] === 'case_study') {
				$question['options'] = json_encode([]);
				return true;
			}
			throw new LogicException('ensure you are sending appropriate data for the particular question');
		}
		return false;
	}

	public function deleteQuestion($questionId) {
		$this->quizQuestions->newQuery()->where('uuid', $questionId)->delete();
		return response()->deleted("Question deleted successfully", "true", "deleted");
	}

	public function updateQuiz(string $quizId, array $data) {
		$quiz = $this->quiz->newQuery()->where('uuid', $quizId)->first();
		if (!$quiz) {
			throw new ResourceNotFoundException('this quiz does not exist in our records ');
		}

		$this->updateModelAttributes($quiz, $data);
		$quiz->save();
		if (isset($data['questions'])) {
			DB::transaction(function () use ($quiz, $data) {
				$this->validateUpdateQuizQuestions($quiz, $data['questions']);
			});
		}
		return response()->updated('Quiz updated successfully', $quiz, "quiz");
	}

	private function validateUpdateQuizQuestions(object $quiz, array $questions): void {
		foreach ($questions as $question) {
			if (isset($question['question_text']) && isset($question['question_text']) && isset($question['question_type'])
				&& isset($question['score']) && isset($question['answer']) && $question['question_number']) {
				if ($question['question_type'] === 'options' && isset($questions['options']) && is_array($question['options']) && (count($question['options']) > 0)) {
					//prepare quiz for insert Many
					if (isset($question['id'])) {
						$quizQuestion = $this->quizQuestions->newQuery()->where('id', $question['id'])
							->where("quiz_id", $quiz->id)
							->first();
						$isValid = $this->validateSingleQuestion($quizQuestion);
						if (!$isValid) {
							throw new LogicException('one of the questions provided is not properly formatted');
						}

						$this->updateModelAttributes($quizQuestion, $question);
						$quizQuestion->save();
					} else {
						$this->quizQuestions
							->newQuery()
							->create([
								"uuid" => Str::uuid(), "quiz_id" => $quiz->id,
								"question_text" => $question['question_text'],
								"score" => $question['score'],
								"answer" => $question['answer'],
								"options" => json_encode($question['options']),
								'question_type' => $question['question_type'],
								'question_number' => $question['question_number'],
							]);
					}
				} elseif ($question['question_type'] === 'case_study') {
					if (isset($question['id'])) {
						$quizQuestion = $this->quizQuestions->newQuery()->where('id', $question['id'])
							->where("quiz_id", $quiz->id)
							->first();
						$isValid = $this->validateSingleQuestion($quizQuestion);
						if (!$isValid) {
							throw new LogicException('one of the questions provided is not properly formatted');
						}

						$this->updateModelAttributes($quizQuestion, $question);
						$quizQuestion->save();
					} else {
						$this->quizQuestions
							->newQuery()
							->create([
								"uuid" => Str::uuid(), "quiz_id" => $quiz->id,
								"question_text" => $question['question_text'],
								"score" => $question['score'],
								"answer" => $question['answer'],
								"options" => json_encode([]),
								'question_type' => $question['question_type'],
								'question_number' => $question['question_number'],
							]);
					}
				} else {
					throw new CustomValidationFailed("Question are not well formatted");
				}

			} else {
				throw new CustomValidationFailed("A particular question is not well formatted.. please ensure all required fields for the question are provided");
			}

		}

	}

	public function getAllQuiz(string $tenantId, int $limit) {
		$tenant = $this->tenants->newQuery()->where('id', $tenantId)->first();
		if (!$tenant) {
			throw new ResourceNotFoundException('unfortunately the tenant could not be found');
		}
		$quizzes = $this->quiz->newQuery()->where('tenant_id', $tenant->id)->oldest('created_at')->paginate($limit);
		$resource = QuizResource::collection($quizzes);
		return response()->fetch("All tenant quiz fetched", $resource, "quizzes");
	}

	public function getSingleQuiz(string $quizId) {
		$quiz = $this->quiz->newQuery()->where('uuid', $quizId)->with('questions')->first();
		if (!$quiz) {
			throw new RecordNotFoundException('Quiz not found');
		}

		$resource = new QuizResource($quiz);
		return response()->fetch("quiz fetched successfully", $resource, "quiz");
	}

  /**
   * @throws Exception
   */
  public function SubmitQuiz(string $learnerId, string $lessonId, string $score, $submission, string $quizId) {
		$lesson = $this->lessons->newQuery()->where('uuid', $lessonId)->first();
		if (!$lesson) {
			throw new ResourceNotFoundException('could not find the lesson');
		}
		$quiz = $this->quiz->newQuery()->where('uuid', $quizId)->first();
		if (!$quiz) {
			throw new ResourceNotFoundException('the quiz could not be found');
		}
		$hasSubmittedQuiz = $this->lmsQuizReport->newQuery()->where('learner_id', $learnerId)->where('quiz_id', $quiz->id)->first();
		if ($hasSubmittedQuiz && $hasSubmittedQuiz->disable_on_submit) {
			throw new LogicException('You have submitted this quiz already, the quiz is not allowed for resubmission');
		}
		try {
			DB::transaction(function () use ($learnerId, $lesson, $score, $submission, $quiz) {
				$this->lmsQuizReport->newQuery()
					->sharedLock()
					->updateOrCreate(
						['learner_id' => $learnerId, 'quiz_id' => $quiz->id],
						[
							"learner_id" => $learnerId,
							"quiz_id" => $quiz->id,
							"score" => $score,
							"lesson_id" => $lesson->id,
							"submission" => $submission ? json_encode($submission) : json_encode([])
						]);
			});

			return response()->created('quiz submitted successfully', true, 'quiz');

		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

}