<?php

namespace App\Http\Resources\Lms;

use Illuminate\Http\Resources\Json\JsonResource;

class NotesResource extends JsonResource {
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array
	 */
	public function toArray($request) {
		return [
			'id' => $this->uuid,
			'note' => $this->note,
			'module' => new ModulesResource($this->whenLoaded('module')),
			'created_at' => (string) $this->created_at->toIso8601String(),
			'updated_at' => (string) $this->updated_at->toIso8601String(),
		];
	}
}
