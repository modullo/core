<?php

namespace App\Classes\Lms;

use App\Classes\ModulloClass;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Resources\Lms\NotesResource;
use App\Models\Lms\Learners;
use App\Models\Lms\lmsNotes;
use App\Models\Lms\Modules;
use Illuminate\Support\Facades\DB;

class NotesClass extends ModulloClass {

	private LmsNotes $lmsNotes;
	private Modules $modules;

	public function __construct() {

		$this->modules = new Modules;
		$this->lmsNotes = new lmsNotes;
	}

	public function createOrUpdateNote(string $learnerId, string $note, string $moduleId) {
		$module = $this->modules->newQuery()->where('uuid', $moduleId)->first();
		if (!$module) {
			throw new ResourceNotFoundException('unable to find the module');
		}
		$courseId = $module->course_id;
		$tenantId = $module->tenant_id;
		$learnerNote = null;
		Db::transaction(function () use ($module, $learnerId, $courseId, $tenantId, &$learnerNote, $note) {
			$learnerNote = $this->lmsNotes->newQuery()->sharedLock()->updateOrCreate([
				'learner_id' => $learnerId,
				'module_id' => $module->id,
			], [
				'learner_id' => $learnerId,
				'module_id' => $module->id,
				'course_id' => $courseId,
				'tenant_id' => $tenantId,
				'note' => $note,
			]);
		});
		$resource = new NotesResource($learnerNote);
		return response()->created('notes saved successfully', $resource, 'note');
	}

	public function fetchSingleNote(string $noteId) {
		$learningNote = $this->lmsNotes->newQuery()->where('uuid', $noteId)->first();
		if (!$learningNote) {
			throw new ResourceNotFoundException('note could not be found');
		}
		$resource = new NotesResource($learningNote);
		return response()->fetch('notes fetched successfully', $resource, 'note');

	}

	public function fetchAllNotes(string $learnerId, string $limit,  ?string $moduleId = null) {
		$builder = $this->lmsNotes->newQuery();
		if($moduleId){
			$module = $this->modules->newQuery()->where('uuid', $moduleId)->first();
			if (!$module) {
				throw new ResourceNotFoundException('unable to find the module');
			}
			$builder = $builder->where('module_id',$module->id)->with('modules');
		}
		$builder = $builder->where('learner_id',$learnerId)->oldest('created_at')->paginate($limit);
		$resource = NotesResource::collection($builder);
		return response()->fetch('notes fetched successfully', $resource, 'notes');
	}

}