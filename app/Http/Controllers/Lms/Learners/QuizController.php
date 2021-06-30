<?php

namespace App\Http\Controllers\Lms\Learners;
use App\Classes\LMS\Resource\QuizClass;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Controllers\Controller;
use App\Models\Lms\Learners;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class QuizController extends Controller {
	private QuizClass $quizClass;
	private Learners $learners;

	public function __construct() {
		$this->quizClass = new QuizClass;
		$this->learners = new Learners;
	}

  /**
   * @throws ValidationException
   * @throws \Exception
   */
  public function submitQuiz(Request $request, string $lessonId, string $quizId) {

		$this->validate($request, [
			'score' => 'required|numeric',
			'submission' => 'required',
		]);
		$user = $request->user();
		$learner = $this->learners->newQuery()->where('lms_user_id', $user->id)->first();
		if (!$learner) {
			throw new ResourceNotFoundException('could not find the learner');
		}
		return $this->quizClass->SubmitQuiz($learner->id, $lessonId, $request->score, $request->submission, $quizId);

	}

}