<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Setting;
//use frontend\models\ContactForm;

use backend\models\UserForm;

use common\models\User;
use yii\helpers\ArrayHelper;
/**
 * Site controller
 https://www.twilio.com/blog/2016/03/how-to-validate-phone-numbers-in-php-with-the-twilio-lookup-api.html
 */
class AjaxController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                /*'only' => ['logout', 'signup'],*/
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => [User::ROLE_WORKER, User::ROLE_CUSTOMER, User::ROLE_AGENCY],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
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

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        //echo "aaa";
        return $this->render('index', [
                //'model' => $model,
                //'agencies' => $agencies,
                //'exams_json' => $exams_json
            ]);
    }

}
