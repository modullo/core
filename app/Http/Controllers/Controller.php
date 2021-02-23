<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
  /**
   * The response container
   *
   * @var array
   */
  protected $data = [];

  /**
   * An associative array of request field names, and the model keys that should be used when trying to
   * update the columns in a model.
   *
   * @var array
   */
  protected $updateFields = [];

  /**
   * Returns the company for the currently authenticated user, returns NULL otherwise.
   *
   * @param Request|null $request
   * @param bool         $throwExceptionOnFail
   * @param bool         $enforcePlanAccess
   *
   * @return null|User
   * @throws AuthorizationException
   * @throws RecordNotFoundException
   */
  protected function user(Request $request = null, bool $throwExceptionOnFail = true, bool $enforcePlanAccess = true)
  {
    if ($request === null) {
      $request = app('request');
    }
    $user = $request->user();
    # get the user
    if (empty($user)) {
      if ($throwExceptionOnFail) {
        throw new RecordNotFoundException('Sorry, we cannot retrieve your credentials because you are not authenticated');
      }
      return null;
    }


    return $user;
  }



  /**
   * Returns a properly formatted JSON response for a ValidationException
   *
   * @param ValidationException $e
   * @param int                 $status
   *
   * @return JsonResponse
   */
  protected function validationErrorToResponse(ValidationException $e, int $status = 400): JsonResponse
  {
    $response = [
      'status' => $status,
      'code' => ResponseStatus::VALIDATION_FAILED,
      'title' => 'Some validation errors were encountered while processing your request',
      'source' => validation_errors_to_messages($e)
    ];
    # convert the error
    return response()->json(['errors' => [$response]], $status);
  }

  /**
   * Returns a properly formatted JSON response for a Throwable
   *
   * @param \Throwable $e
   * @param int        $status
   *
   * @return JsonResponse
   */
  protected function throwableToResponse(\Throwable $e, int $status = 500): JsonResponse
  {
    $response = [
      'status' => $status,
      'code' => ResponseStatus::EXCEPTION,
      'title' => $e->getMessage(),
    ];
    return response()->json(['errors' => [$response]], $status);
  }
}
