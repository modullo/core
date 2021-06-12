<?php

namespace App\Exceptions;

use App\support\Responses\Codes;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
  protected $dontReport = [
    AuthorizationException::class,
    HttpException::class,
    ModelNotFoundException::class,
    ValidationException::class,
  ];

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */

    /**
     * A list of exceptions that denote something was not found.
     *
     * @var array
     */
  protected  $notFoundExceptions = [
    ModelNotFoundException::class,
    NotFoundHttpException::class,
    RecordNotFoundException::class
  ];

  protected  $clientInputExceptions = [
    \UnexpectedValueException::class,
    AuthorizationException::class,
    ApplicationAccessDeniedException::class
  ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception|Throwable
     */
    public function report(Throwable $exception)
    {
//        if (app()->bound('sentry') && $this->shouldReport($exception)) {
//            app('sentry')->captureException($exception);
//        }
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */


  /**
   * Render an exception into an HTTP response.
   *
   * @param \Illuminate\Http\Request $request
   * @param Throwable $e
   * @return \Illuminate\Http\JsonResponse|Response
   * @throws Throwable
   */
    public function render($request, Throwable  $e)
    {

        $status = 500;
        # the status code
        $exceptionClass = get_class($e);
        # we get the class name for the exception
        if (in_array($exceptionClass, $this->notFoundExceptions, true)) {
            $status = 404;
            $message = $exceptionClass === RecordNotFoundException::class ?
                $e->getMessage() : 'route does not exist';
            # our response message
            $response = [
                'status' => $status,
                'code' => Codes::NOT_FOUND,
                'title' => $message,
                'source' => array_merge($request->all(), ['path' => $request->getPathInfo()])
            ];

        } elseif ($e instanceof MethodNotAllowedHttpException) {
            $status = 405;
            $response = [
                'status' => $status,
                'code' => Codes::HTTP_ERROR,
                'title' => 'This method is not allowed for this endpoint.',
                'source' => array_merge($request->all(),
                    ['path' => $request->getPathInfo(), 'method' => $request->getMethod()])
            ];

        } elseif ($e instanceof ValidationException) {

            $status = 400;
            $response = [
                'status' => $status,
                'code' => Codes::VALIDATION_FAILED,
                'title' => 'Some validation errors were encountered while processing your request',
                'source' => validation_errors_to_messages($e)
            ];

        } elseif ($e instanceof ApplicationAccessDeniedException) {
            $status = 403;
            $response = [
                'status' => $status,
                'code' => Codes::HTTP_ERROR,
                'title' => $e->getMessage(),
                'source' => array_merge($request->all(),
                    ['path' => $request->getPathInfo(), 'method' => $request->getMethod()])
            ];

        } elseif (in_array($exceptionClass, $this->clientInputExceptions, true)) {
            $status = 400;
            $response = [
                'status' => $status,
                'code' => Codes::INPUT_ERROR,
                'title' => $e->getMessage(),
                'source' => array_merge($request->all(),
                    ['path' => $request->getPathInfo(), 'method' => $request->getMethod()])
            ];

        } elseif ($e instanceof DeletingFailedException) {
            $response = [
                'status' => $status,
                'code' => Codes::EXCEPTION,
                'title' => $e->getMessage(),
                'source' => array_merge($request->all(),
                    ['path' => $request->getPathInfo(), 'method' => $request->getMethod()])
            ];

        } elseif ($e instanceof UnauthorizedUserException) {
            $status = 403;
            $response = [
                'status' => $status,
                'code' => Codes::HTTP_ERROR,
                'title' => $e->getMessage(),
                'source' => array_merge($request->all(),
                    ['path' => $request->getPathInfo(), 'method' => $request->getMethod()])
            ];

        } elseif ($e instanceof AuthenticationException) {
            $status = 401;
            $response = [
                'status' => $status,
                'code' => Codes::HTTP_ERROR,
                'title' => $e->getMessage(),
                'source' => array_merge($request->all(),
                    ['path' => $request->getPathInfo(), 'method' => $request->getMethod()])
            ];
        }else if($e instanceof ResourceNotFoundException)
        {
            $status = 500;
            $response = [
                'status' => $status,
                'code' => Codes::HTTP_ERROR,
                'title' => $e->getMessage(),
                'source' => array_merge($request->all(),
                    ['path' => $request->getPathInfo(), 'method' => $request->getMethod()])
            ];
        }

        else if($e instanceof CustomValidationFailed)
        {
            $status = 400;
            $response = [
                'status' => $status,
                'code' => Codes::HTTP_ERROR,
                'title' => $e->getMessage(),
                'source' => array_merge($request->all(),
                    ['path' => $request->getPathInfo(), 'method' => $request->getMethod()])
            ];
        }
        else {
            $response = [
                'status' => $status,
                'code' => Codes::EXCEPTION,
                'title' => $e->getMessage(),
            ];
        }
        if(app()->environment() === "testing")
        {
            throw $e;
        }
        return response()->json(['errors' => [$response]], $status);
    }


}
