<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use common\models\User;
use yii\helpers\ArrayHelper;
use common\components\SiteHelpers;
/**
 * Site controller
 https://www.twilio.com/blog/2016/03/how-to-validate-phone-numbers-in-php-with-the-twilio-lookup-api.html
 */
class CronjobController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => [],
                'rules' => [
                    [
                        'actions' => ['delete-unverified-user', 'expired-user-update'],
                        'allow' => true,
                        /*'roles' => ['*'],*/
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }


    //Delete all unverified user
    public function actionDeleteUnverifiedUser(){

        $timeLimit = time() - 3600;
        //$timeLimit = time() - 120;

       //Removed for confidentiality

    }

    public function actionExpiredUserUpdate(){
        $currTime = time();
        // echo $currTime.'--> Time <br>';
        $subusers = User::find()->where(['user_type' => '5'])
                ->andWhere(['<>','status', 1])
                ->andWhere(['<', 'expiration_date', $currTime])
                ->all();
        //print_r($subusers);
        foreach($subusers as $user){
            //echo $user->id.'  '.$user->expiration_date.'</br>';
            $parentUser = User::findOne(['id' => $user->parent_id]);
            SiteHelpers::sendNotification($parentUser,'E', $user->id);
        }
       //Removed for confidentiality
    }
}
