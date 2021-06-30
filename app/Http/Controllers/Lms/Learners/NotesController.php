<?php

namespace App\Http\Controllers\Lms\Learners;

use App\Classes\LMS\NotesClass;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Controllers\Controller;
use App\Models\Lms\Learners;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class NotesController extends Controller {

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */

	private NotesClass $notesClass;
	private Learners $learners;

	public function __construct() {
		$this->notesClass = new NotesClass;
		$this->learners  = new Learners;
	}

	public function index(Request $request, string $moduleId){
    $user = $request->user();
    $learner = $this->learners->newQuery()->where('lms_user_id',$user->id)->first();
    if (!$learner) throw new ResourceNotFoundException('unable to find the learner');
    $limit = $request->query('limit',100);
    return $this->notesClass->fetchAllNotes($learner->id,$limit,$moduleId);
  }

  public function all(Request $request){
    $user = $request->user();
    $learner = $this->learners->newQuery()->where('lms_user_id',$user->id)->first();
    if (!$learner) throw new ResourceNotFoundException('unable to find the learner');
    $limit = $request->query('limit',100);
    return $this->notesClass->fetchAllNotes($learner->id,$limit,null);
  }

  /**
   * @throws ValidationException
   */
  public function save(Request $request, string $moduleId){
	  $this->validate($request,[
	    'note' => 'required'
    ]);

	  $user = $request->user();
	  $learner = $this->learners->newQuery()->where('lms_user_id',$user->id)->first();
	  if (!$learner) throw new ResourceNotFoundException('unable to find the learner');
	  return $this->notesClass->createOrUpdateNote($learner->id,$request->note,$moduleId);
  }

  public function single(string $noteId){
    return $this->notesClass->fetchSingleNote($noteId);
  }



	//
}
