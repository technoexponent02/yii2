<?php
namespace api\controllers;

use yii\rest\Controller;
use Yii;
/**
 * Default controller
 */
class DefaultController extends Controller
{
    public function actionError()
    {
    	if (($exception = Yii::$app->getErrorHandler()->exception) === null) {
    		return '';
    	}
    	
    	if ($exception instanceof HttpException) {
    		$code = $exception->statusCode;
    	} else {
    		$code = $exception->getCode();
    	}
    	if ($exception instanceof Exception) {
    		$name = $exception->getName();
    	} else {
    		$name = Yii::t('yii', 'Error');
    	}
    	if ($code) {
    		$name .= " (#$code)";
    	}
    	
    	if ($exception instanceof UserException) {
    		$message = $exception->getMessage();
    	} else {
    		$message = Yii::t('yii', 'An internal server error occurred.');
    	}
    	
    	return [
    		'name' => $name,
    		'message' => $message,
    		'code' => $code,
    		'status' => $exception->statusCode,
    		'type' => get_class($exception),
    	];
    }

    public function actionIndex()
    {
    	return "API";
    }
}
