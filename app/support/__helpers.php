<?php

use App\Events\AccountRegistered;
use App\Exceptions\RecordNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Models\UserSecretQuestions;
use Laravel\Passport\ClientRepository;
use Illuminate\Support\Facades\Hash;
function custom_url(string $base, string $path = null, array $parameters = null, bool $secure = true): string
{
    $uri = new \GuzzleHttp\Psr7\Uri($base);
    # create the URI
    $path = $path ?: '/';
    # for situations where NULL was passed as the path - we assume the base
    if (!empty($path) && !(is_string($path) || is_array($path))) {
        throw new InvalidArgumentException('path should either be a string or an array');
    }
    if (!empty($path)) {
        $path = is_string($path) ? $path : implode('/', $path);
        $uri = $uri->withPath(starts_with($path, '/') ? $path : '/'.$path);
    }
    if (!empty($parameters)) {
        $uri = $uri->withQuery(http_build_query($parameters));
    }
    if ($secure) {
        $uri = $uri->withScheme('https');
    }
    return (string) $uri;
}

function cdn(string $path, bool $secure = true)
{
    $base = config('app.cdn_url', config('app.url'));
    # we get the base URL first
    $secure = app()->environment() === 'production' ? $secure : false;
    # on local, we turn secure mode off
    return custom_url($base, $path, null, $secure);
}

/**
 * @param string|array $path
 * @param array $parameters
 * @param bool  $secure
 *
 * @return string
 */
function web_url($path = null, array $parameters = [], bool $secure = true): string
{
    $base = config('app.url', 'http://localhost');
    # we get the base URL first
    return custom_url($base, $path, $parameters, $secure);
}

/**
 * @param null  $path
 * @param array $parameters
 * @param bool  $secure
 *
 * @return string
 */
function site_url($path = null, array $parameters = [], bool $secure = true): string
{
    $base = config('app.site_url', config('app.url'));
    # we get the base URL first
    return custom_url($base, $path, $parameters, $secure);
}

function validation_errors_to_messages(ValidationException $exception)
{
    $dependentFieldChecks = [
        'required_if',
        'required_unless',
        'required_with',
        'required_with_all',
        'required_without',
        'required_without_all'
    ];
    # checks that have dependent fields
    $messages = [];
    $errors = [];
    foreach ($exception->validator->failed() as $field => $failures) {
        foreach ($failures as $rule => $data) {
            $errors[$field][$rule] = is_array($data) ? implode(', ', $data) : $data;
        }
    }
    foreach ($exception->errors() as $field => $failures) {
        $fieldErrors = $errors[$field] ?? [];
        # get the specific errors for the field -- we'll need this to get additional validation data
         $attributes = [];
        foreach ($failures as $id) {
            $components = explode('.', $id);
            # split up the id
            $checkName = count($components) > 1 ? $components[1] : $components[0];
            # we parse out the check name from the id
            $errorKey = str_replace('_', '', Str::title($checkName));
            # now we see if we got it
            $attributes = [
                'attribute' => $field,
                'values' => $fieldErrors[$errorKey],
                strtolower($checkName) => $fieldErrors[$errorKey]
            ];
            if (in_array($checkName, $dependentFieldChecks, true)) {
                # this field is one of those that has dependencies
                $split = explode(',', $fieldErrors[$errorKey]);
                $additional = ['other' => $split[0]];
                if (count($split) === 2) {
                    $additional['value'] = trim($split[1]);
                } elseif (count($split) > 2) {
                    unset($split[0]);
                    $additional['value'] = implode(',', $split);
                }
                $attributes[] =  $additional;
            }
            # we set the attributes
            if (isset($messages[$field]) && !is_array($messages[$field])) {
                $messages[$field] = (array) $messages[$field];
            }
            if (!empty($messages[$field]) && is_array($messages[$field])) {
                $messages[$field][] = trans($id, $attributes, 'en');
            } else {
                $messages[$field] = trans($id, $attributes, 'en');
            }
        }
    }
    return $messages;
}

if ( ! function_exists('config_path'))
{
    /**
     * Get the configuration path.
     *
     * @param  string $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function sendVerificationEmail(object $user):void {
    $length = 5;
    $otp =  substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil
    ($length/strlen($x)) )),1,$length);
    DB::table('email_otp')->updateOrInsert(['user_id' => $user->id],[
        'user_id' => $user->id,
        'otp' => $otp
    ]);
    event(new AccountRegistered($user));
}

function logSecretQuestionAndAnswer(string $question,string $answer,object $user){
    $question = trim( str_replace(' ', '', strtolower($question)));
    $answer = trim( str_replace(' ', '', strtolower($answer)));
    $theQuestion = Hash::make($question);
    $theanswer = Hash::make($answer);
    return UserSecretQuestions::updateOrCreate([
        'user_id' => $user->id
    ],[
        'question' => $theQuestion,
        'answer' => $theanswer,
    ]);

}

function validateQuestionAndAnswer(string $question,string $answer,object $user):bool{
    $userSecretQuestion = UserSecretQuestions::where('user_id',$user->id)->first();
    if(!$userSecretQuestion){
        throw new LogicException('You cannot perform this transaction till you update your 2FA Question and answer');
    }
     $question = trim( str_replace(' ', '', strtolower($question)));
     $answer = trim( str_replace(' ', '', strtolower($answer)));
     if(!Hash::check($question, $userSecretQuestion->question)){
         throw new LogicException('Secret Question Does Not Match');
     }
     if(!Hash::check($answer, $userSecretQuestion->answer)){
         throw new LogicException('Secret Answer Does Not Match');
     }

     return true;

}

function generateOtp($length = 5,$otpType = 'mixed'){
    switch($otpType){
        case 'number':
            return substr(str_shuffle(str_repeat($x='0123456789', ceil($length/strlen($x)) )),1,$length);
        case 'mixed':
                return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil
                ($length/strlen($x)) )),1,$length);
        default:
        break;
    }

}

    function validate_api_client(\Illuminate\Http\Request $request): bool
    {
      $client = (new ClientRepository())->find($request->input('client_id'));
      # find the OAuth client
      if (empty($client)) {
        throw new RecordNotFoundException('We could not identify your application client.');
      }
      if ($client->secret !== $request->input('client_secret')) {
        throw new \UnexpectedValueException(
          'The provided client_secret is not correct for the provided client.'
        );
      }
      return true;
    }



