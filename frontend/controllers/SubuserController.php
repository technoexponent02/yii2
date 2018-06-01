<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use common\models\Setting;
//use frontend\models\ContactForm;
use frontend\models\MyAccountForm;
use common\models\User;
use yii\helpers\ArrayHelper;
use common\models\FileUpload;
use yii\web\UploadedFile;
use yii\helpers\VarDumper;

use common\models\Property;
use frontend\models\PropertySearch;
use frontend\models\NotificationsSearch;
/**
 * individual controller
 https://www.twilio.com/blog/2016/03/how-to-validate-phone-numbers-in-php-with-the-twilio-lookup-api.html
 */
class SubuserController extends Controller
{
    /**
     * @inheritdoc
     */
    public $user;
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                /*'only' => ['logout', 'signup'],*/
                'rules' => [
                    [
                        'actions' => ['dashboard'],
                        'allow' => true,
                        'roles' => [User::ROLE_ORGANIZATION_USER],
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($event)
    {
        if (!Yii::$app->user->isGuest) 
        {
            $this->user = Yii::$app->user->identity;
        }
        return parent::beforeAction($event);
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
    public function actionDashboard()
    {
        $user = Yii::$app->user->identity;
        
        $myAccountForm =  $this->findAccountFormModel($user->id);
        $rentedProperties = Property::find()->where([ 'parent_id' => $user->parent_id, 'status' => 2, 'rent_status' => 0])->count();
        $availableProperties = Property::find()->where([ 'parent_id' => $user->parent_id, 'status' => 2, 'rent_status' => 1])->count();
        //$addedProperties = Property::find()->where([ 'parent_id' => $user->parent_id, 'status' => 2])->count();
        $addedProperties = Property::find()->where([ 'user_id' => $user->id, 'status' => 2])->count();

        
        ($rentedProperties>0)? $rentedProperties : $rentedProperties = 0;
        ($availableProperties>0)? $availableProperties : $availableProperties = 0;
        // VarDumper::dump($user->getAddedCountry()->one()->getTranslatateData());
        // die();
        $searchModel_notification = new NotificationsSearch();
        $dataProvider_notification = $searchModel_notification->search(Yii::$app->request->queryParams);
        if (Yii::$app->request->isPost) {

            $myAccountForm->load(Yii::$app->request->post());
  
            $myAccountForm->preferred_locale = Yii::$app->language;
            $myAccountForm->name = $myAccountForm->first_name.' '.$myAccountForm->last_name;      
            
                if($myAccountForm->password == $myAccountForm->password_repeat){
                    $user->password = $myAccountForm->password;
                    $user->save(false);
                }
            $myAccountForm->save();
           // return $this->redirect(['subuser/dashboard']);
                
        }
      
            $searchModel = new PropertySearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 5);

             return $this->render('dashboard', [
            'myAccountForm' => $myAccountForm,
            'dataProvider' => $dataProvider,
            'rentedProperties' => $rentedProperties,
            'availableProperties' => $availableProperties,
            'addedProperties' => $addedProperties,
            'dataProvider_notification' => $dataProvider_notification
            ]);
       
            

        //$myAccountForm =  $this->findAccountFormModel($user->id);
        
    }

    /*public function actionDashboard()
    {
        return $this->render('dashboard', [
            
            ]);
    }*/
    protected function findAccountFormModel($id)
    {
        if (($model = MyAccountForm::findOne($id)) !== null) {
            $model->scenario = 'subuser_update';
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    
    
}