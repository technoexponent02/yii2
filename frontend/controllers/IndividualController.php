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
use common\models\Property;
//use frontend\models\ContactForm;
use frontend\models\MyAccountForm;
use common\models\User;
use yii\helpers\ArrayHelper;
use common\models\FileUpload;
use yii\web\UploadedFile;
use yii\helpers\VarDumper;
use common\components\SiteHelpers;
use frontend\models\NotificationsSearch;
/*use common\models\Notifications;
use yii\data\ActiveDataProvider;
use yii\base\Model;*/

/**
 * individual controller
 https://www.twilio.com/blog/2016/03/how-to-validate-phone-numbers-in-php-with-the-twilio-lookup-api.html
 */
class IndividualController extends Controller
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
                        'roles' => [User::ROLE_INDIVIDUAL],
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
        $progress_bar = SiteHelpers::getBadgeProgress($user);
        
        $myAccountForm =  $this->findAccountFormModel($user->id);
        $rentedProperties = Property::find()->where([ 'user_id' => $user->id, 'status' => 2, 'rent_status' => 0])->count();
        $availableProperties = Property::find()->where([ 'user_id' => $user->id, 'status' => 2, 'rent_status' => 1])->count();
        
        ($rentedProperties>0)? $rentedProperties : $rentedProperties = 0;
        ($availableProperties>0)? $availableProperties : $availableProperties = 0;
        // VarDumper::dump($user->getAddedCountry()->one()->getTranslatateData());
        // die();
        $user_image=$myAccountForm->user_image;
        $searchModel_notification = new NotificationsSearch();
        $dataProvider_notification = $searchModel_notification->search(Yii::$app->request->queryParams);
        if (Yii::$app->request->isPost && $myAccountForm->load(Yii::$app->request->post()) && $myAccountForm->validate()) {
            if (UploadedFile::getInstance($myAccountForm, 'user_image'))
            {
                if ($myAccountForm->user_image!="" && 
                    file_exists(Yii::getAlias('@backend').'/web/upload/user_image/'.$myAccountForm->user_image))
                {
                    unlink(Yii::getAlias('@backend').'/web/upload/user_image/'.$myAccountForm->user_image);
                }
            }
            
            if (UploadedFile::getInstance($myAccountForm, 'user_image'))
            {
                $file_model = new FileUpload();
                $file_model->singleFile = UploadedFile::getInstance($myAccountForm, 'user_image');
                if($pro_image = $file_model->uploadSingle('web/upload/user_image'))
                    {
                        $myAccountForm->user_image = $pro_image;
                    } 
            }
            else
            {
                $myAccountForm->user_image = $user_image;
            }    
            $myAccountForm->preferred_locale = Yii::$app->language;
            $myAccountForm->name = $myAccountForm->first_name.' '.$myAccountForm->last_name; 
            if($myAccountForm->phone != $user->phone){
                
                $session = Yii::$app->session;
                $verification_code = mt_rand(100000, 999999) . $user->id;
                $user->verification_code = $verification_code;
                $user->save(false);
                $response = SiteHelpers::phoneMobilyVerificationSms($verification_code , $myAccountForm->phone);
                $session['user'] = [
                    'phone' => $myAccountForm->phone,
                    'id' => $user->id,
                ];
                $myAccountForm->phone = $user->phone;
                $myAccountForm->save();

                $url = Yii::$app->urlManager->createUrl(['post/profile-verification','token' => $user->auth_key]);
                return $url;
            }      
            $myAccountForm->save();
            //return $this->redirect(['individual/dashboard']);
        }
       
             return $this->render('dashboard', [
            'myAccountForm' => $myAccountForm,
            'rentedProperties' => $rentedProperties,
            'availableProperties' => $availableProperties,
            'progressBar' => $progress_bar,
            'dataProvider_notification' => $dataProvider_notification
            ]);
         
        //$myAccountForm =  $this->findAccountFormModel($user->id);
        
    }
    protected function findAccountFormModel($id)
    {
        if (($model = MyAccountForm::findOne($id)) !== null) {
            $model->scenario = 'individual_update';
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    
    
}