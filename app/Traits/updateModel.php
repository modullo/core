<?php

namespace App\Traits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait updateModel
{

  /**
   * Accepts an eloquent model, and the current request instance, using the request data to update the
   * fields of the model based on the configuration of the updateFields array.
   *
   * @param Model   $model
   * @param Request $request
   */

  protected function updateModelAttributes(Model &$model, $data)
  {
    if (empty($this->updateFields)) {
      throw new \UnexpectedValueException('The update fields were not configured for the endpoint.');
    }
    foreach ($this->updateFields as $requestKey => $modelKey) {
      if (!array_key_exists($requestKey,$data)) {
        # doesn't have it, so we skip the key
        continue;
      }
      $model->{$modelKey} = $data[$requestKey];
    }
  }


  protected function updateUserAttributes(Model &$model, $data)
  {
    if (empty($this->userFields)) {
      throw new \UnexpectedValueException('The update fields were not configured for the endpoint.');
    }
    foreach ($this->userFields as $requestKey => $modelKey) {
      if (!array_key_exists($requestKey,$data)) {
        # doesn't have it, so we skip the key
        continue;
      }
      $model->{$modelKey} = $data[$requestKey];
    }
  }

}