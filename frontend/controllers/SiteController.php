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
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\SignupFormOrg;
use common\models\Setting;
use frontend\models\RegisterVerification;
use frontend\models\ContactForm;
use frontend\models\PropertySearch;
use frontend\models\PropertyFilterSearch;

use common\models\User;
use common\models\Searches;
use common\models\Property;
use common\models\PropertyTypeTranslation;
use yii\helpers\ArrayHelper;


use common\components\SiteHelpers;
use yii\helpers\VarDumper;
use common\models\Seodata;
use common\models\PropertyType;


/**
 * Site controller
 https://www.twilio.com/blog/2016/03/how-to-validate-phone-numbers-in-php-with-the-twilio-lookup-api.html
 */
class SiteController extends Controller
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
                        'actions' => ['index', 'register', 'register-verification', 'login', 'error', 'reset-password', 'request-password-reset', 'resend-otp', 'change-language', 'email-verification', 'need-help', 'search-result','search-log', 'terms-and-conditions', 'contact-us', 'about', 'auto-complete-search', 'draw-map','test-sms','resend-registration-email', 'blocked-dashboard', 'demo-index'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['reset-password', 'thank-you' , 'locked-dashboard'],
                        'allow' => true,
                        'roles' => [User::ROLE_INDIVIDUAL, User::ROLE_ORGANIZATION, User::ROLE_ORGANIZATION_USER],
                    ],
                ],
            ],
            // 'verbs' => [
            //     'class' => VerbFilter::className(),
            //     'actions' => [
            //         'logout' => ['post'],
                    
            //         /*'login' => ['post'],*/
            //     ],
            // ],
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
    /*public function actionIndex()
    {
        $searchModel = new PropertyFilterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 3);

        $seodata = Seodata::findOne(4);

        \Yii::$app->view->registerMetaTag([
            'name' => 'keywords',
            'content' => $seodata->translatateData->page_keywords,
        ]);
        \Yii::$app->view->registerMetaTag([
            'name' => 'description',
            'content' => $seodata->translatateData->page_description,
        ]);

        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'seodata' => $seodata,
            ]);
    }*/

    public function actionIndex()
    {
        $searchModel = new PropertyFilterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 3);

        $seodata = Seodata::findOne(4);

        \Yii::$app->view->registerMetaTag([
            'name' => 'keywords',
            'content' => $seodata->translatateData->page_keywords,
        ]);
        \Yii::$app->view->registerMetaTag([
            'name' => 'description',
            'content' => $seodata->translatateData->page_description,
        ]);
        $propertyType = PropertyType::getCategoryDropdownList();
        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'seodata' => $seodata,
                'property_type' => $propertyType,
                'searchModel' => $searchModel,
            ]);
    }

    public function actionRegister()
    {
        $active_tab = 2;
        $model_individual = new SignupForm();
        $model_organization = new SignupFormOrg();

        $seodata = Seodata::findOne(5);

        \Yii::$app->view->registerMetaTag([
            'name' => 'keywords',
            'content' => $seodata->translatateData->page_keywords,
        ]);
        \Yii::$app->view->registerMetaTag([
            'name' => 'description',
            'content' => $seodata->translatateData->page_description,
        ]);

        if(Yii::$app->request->isAjax && $model_individual->load(Yii::$app->request->post())) 
        {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model_individual);
        }
        if($model_individual->load(Yii::$app->request->post()) && $model_individual->validate())
        {
            $input_data = Yii::$app->request->post();
            $model_individual->name = $model_individual->first_name . ' ' . $model_individual->last_name;
            $model_individual->user_type = 3;
            $model_individual->status = User::STATUS_INCOMPLETE;
            $model_individual->save();
            $model_individual->assignRole(User::ROLE_INDIVIDUAL);
            $verification_code = mt_rand(100, 999) . $model_individual->id;
            $model_individual->verification_code = $verification_code;
            $model_individual->username = User::ROLE_INDIVIDUAL . '-' . $verification_code;
            $model_individual->save(false);
            $user = User::findOne($model_individual->id);
            //Yii::$app->user->login($model);
            /*Yii::$app->getUser()->login($user);*/
            $user->sign_up_ip = $_SERVER['REMOTE_ADDR'];
            // checking twilio user id exists in verification_code column
            // if ($user->verification_code == "" && $user->verification_code == NULL)
            // {
            //       $response = SiteHelpers::authyRegisterUserApi($user->email, $user->phone, $country_code = 966);  
            //       if (empty($response['errors']))
            //       {
            //            $user->verification_code = $response['user_id'];
            //       }
            // }

            $response = SiteHelpers::phoneMobilyVerificationSms($verification_code , $user->phone);  
                  if (empty($response['errors']))
                  {
                       $user->save(false);
                  }
                             
            // call to request verification code 
            // if ($user->verification_code != "")
            // {
            //     $sms = SiteHelpers::authySendVerificationSms($authy_user_id = $user->verification_code);
            // }

            return $this->redirect(['site/register-verification?token='.$user->auth_key]);
        }

        if(Yii::$app->request->isAjax && $model_organization->load(Yii::$app->request->post())) 
        {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model_organization);
        }
        if($model_organization->load(Yii::$app->request->post()) && $model_organization->validate())
        {
            $input_data = Yii::$app->request->post();
            $model_organization->user_type = 4;
            $model_organization->status = User::STATUS_INCOMPLETE;
            $model_organization->password = $model_organization->password_org;
            $name = explode(" ", $model_organization->name);
            $name[0] ? $model_organization->first_name = $name[0] : '';
            $name[1] ? $model_organization->last_name = $name[1] : '';
            $model_organization->save();
            $model_organization->assignRole(User::ROLE_ORGANIZATION);
            $verification_code = mt_rand(100, 999) . $model_organization->id;
            $model_organization->verification_code = $verification_code;
            $model_organization->username = User::ROLE_ORGANIZATION . '-' . $verification_code;
            $model_organization->save(false);
            $user = User::findOne($model_organization->id);
            //Yii::$app->user->login($model);
            /*Yii::$app->getUser()->login($user);*/
            $user->sign_up_ip = $_SERVER['REMOTE_ADDR'];
            // checking twilio user id exists in verification_code column
            
            $response = SiteHelpers::phoneMobilyVerificationSms($verification_code , $user->phone);
            if (empty($response['errors']))
            {
               $user->save(false);
            }

            /*$setting = Setting::findOne(1);
            Yii::$app->mailer->compose(
                ['html' => 'verificationCode-html', 'text' => 'verificationCode-text'],
                ['user' => $user, 'verification_code' => $verification_code]
            )
            ->setFrom([$setting->contact_email => $setting->site_title . ' robot'])
            ->setCc([$setting->contact_email, 'ankit@technoexponent.com'])
            ->setTo($user->email)
            ->setSubject('Verification code for ' . $setting->site_title)
            ->send();*/

            return $this->redirect(['site/register-verification?token='.$user->auth_key]);
        }

        return $this->render('register', [
                'model_individual' => $model_individual,
                'model_organization' => $model_organization,
                'active_tab' => $active_tab,
            ]);
    }

    public function actionRegisterVerification($token = NULL)
    {
        $otp_verified = 0;
        $email = '';
        $model = null;
        if($token == NULL)
        {
            return $this->redirect(['/']);
        }
        $user_by_auth_key = User::findIdentityByAccessTokenForVerification($token);

        if($user_by_auth_key){
            $model = new RegisterVerification($token);
            if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->validate())      
            {
                $user = User::findIdentityByVerificationCode($model->verification_code);
                if(!$user)
                {
                    return $this->redirect(['/']);
                }
                else
                {
                    $user->verification_code = NULL;
                    $user->otp_request = 1;
                    $user->status = User::STATUS_ACTIVE;
                    Yii::$app->getUser()->login($user);
                    $user->login_ip = $_SERVER['REMOTE_ADDR'];
                    $user->last_login = time();
                    $user->is_online = 1;
                    $user->save(false);

                    switch ($user->getRole()) 
                    {
                        case User::ROLE_INDIVIDUAL:

                            return $this->redirect(['thank-you']);
                            //return $this->redirect(['individual/dashboard']);
                            break;
                        case User::ROLE_ORGANIZATION:
                            $user->pending_verification = 1;
                            $user->save(false);
                            $mail_verification_code = encrypt($user->email);                       
                            SiteHelpers::sendSiteEmails('R',$user->email, $user);

                            $email = $user->email;
                            $otp_verified = 1;
                            break;
                        
                        default:
                            break;
                    }
                }
            }
        }
        else
        {
            $user_by_auth_key = User::findIdentityByAccessToken($token);
            if(!$user_by_auth_key)
                return $this->redirect(['/']);
            else if($user_by_auth_key->pending_verification != 1)
                return $this->redirect(['thank-you']);
            $email = $user_by_auth_key->email;
            $otp_verified = 1;
        }
       
            //print_r($model->getErrors());
            //exit;
            return $this->render('register-verification', [
                'model' => $model,
                'token' => $token,
                'user_by_auth_key' => $user_by_auth_key,
                'otp_verified' => $otp_verified,
                'email' => $email,

            ]);
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        $model->scenario = 'frontend_login';
		if(Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) 
        {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post()) && $model->login()) 
		{
			$user = Yii::$app->user->identity;
			$user->login_ip = SiteHelpers::getClientIP();
            // $user_agent_info = get_browser();
            $user_agent_info = SiteHelpers::getBrowser();
            
            //print_r($user_agent_info); die;
            // $user->user_device = $user_agent_info->platform;
            // $user->user_browser = $user_agent_info->browser;
            $user->user_device = $user_agent_info['platform'];
            $user->user_browser = $user_agent_info['name']." ".$user_agent_info['version'] ;


            //$user->current_location = SiteHelpers::ipDetails($user->login_ip);
			$user->is_online = 1;
			$user->last_login = time();
			$user->save();

            if($user->status == User::STATUS_BANNED || $user->status == User::STATUS_BLOCKED){
                //VarDumper::dump(Yii::$app->user->identity);
                //VarDumper::dump($user->getRole());
                //die();
                //return $this->redirect(['blocked-dashboard']);
                $model = new ContactForm();
                return $this->render('blocked_dashboard', [
                    'model' => $model,
                ]);
            }

            switch ($user->getRole()) 
            {
                case User::ROLE_INDIVIDUAL:
                    return $this->redirect(['individual/dashboard']);
                    break;
                case User::ROLE_ORGANIZATION:
                    if($user->pending_verification == 0)
                        return $this->redirect(['organization/dashboard']);
                    else
                        return $this->redirect(['register-verification?token='.$user->auth_key]);
                    break;
                case User::ROLE_ORGANIZATION_USER:
                    return $this->redirect(['subuser/dashboard']);
                    break;
                default:
                    # code...
                    break;
            }
            
        }
        else
        {            
            return $this->redirect(['index']);
            /*Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);*/
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
		$user = Yii::$app->user->identity;
		$user->is_online = 0;
		$user->save();
        Yii::$app->user->logout();
        return $this->redirect(['/']);
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    /*public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }*/

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        //$this->layout = 'loginLayout';
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
            return $this->redirect(['/']);
        }
        return $this->render('@frontend/views/site/requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) 
        {
            return $this->redirect(['/']);
        }

        return $this->render('@frontend/views/site/resetPassword', [
            'model' => $model,
        ]);
    }

     public function actionResendOtp()
     {
        $input_data = Yii::$app->request->post();
        $user = User::findIdentityByAccessTokenForVerification($input_data['token']);
        if($user->otp_request > 2){
            $user->delete();
            $register_url = Yii::$app->urlManager->createUrl(['site/register']);
            return $register_url;
        }
        else{
            $user->otp_request = $user->otp_request + 1;
            $verification_code = mt_rand(100, 999) . $user->id;
            $user->verification_code = $verification_code;
            
            $user->save(false);

            $response = SiteHelpers::phoneMobilyVerificationSms($verification_code , $user->phone);
            return 1;
        }

    }
    public function actionChangeLanguage($locale = 'en')
    {
        //echo Yii::$app->request->referrer; die;
        $session = Yii::$app->session;
        $languageNew = Yii::$app->request->get('language');
        $languages = Yii::$app->params['languages'];
        if($languageNew)
        {
            if(isset($languages[$languageNew]))
            {
                Yii::$app->language = $languageNew;
                $session->set('language', $languageNew);
            }
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

   public function actionEmailVerification($key)
   {
        $email = decrypt($key);
        $user = User::findOne(['email' => $email]);

        if($user){
            if($user->pending_verification == 1){
                $user->pending_verification = 2;
                $user->save(false);
                $newSubuser = new User();
                $newSubuser->password = '123456';
                $newSubuser->parent_id = $user->id;
                $newSubuser->status = User::STATUS_ACTIVE;
                $newSubuser->name = 'Free Subuser';
                $newSubuser->save(false);
                $newSubuser->assignRole(User::ROLE_ORGANIZATION_USER);                
                $newSubuser->user_type = 5;
                $newSubuser->pending_verification = 2;
                $newSubuser->username = User::ROLE_ORGANIZATION_USER . '-' . $newSubuser->id;
                $newSubuser->sign_up_ip = $_SERVER['REMOTE_ADDR'];
                $newSubuser->save(false);
            }
            Yii::$app->user->login($user, false ? 3600 * 24 * 30 : 0);
            return $this->redirect(['thank-you']);
        }

   }

   public function actionNeedHelp()
   {
        $seodata = Seodata::findOne(2);

        \Yii::$app->view->registerMetaTag([
            'name' => 'keywords',
            'content' => $seodata->translatateData->page_keywords,
        ]);
        \Yii::$app->view->registerMetaTag([
            'name' => 'description',
            'content' => $seodata->translatateData->page_description,
        ]);
        $model = new ContactForm();
        if(Yii::$app->language == 'en')
            return $this->render('need_help_en',[
                'model' => $model,
                'seodata' => $seodata,
            ]);
        else
            return $this->render('need_help_ar',[
                'model' => $model,
                'seodata' => $seodata,
            ]);
   }

   /* public function actionNeedHelp()
   {
        $model = new ContactForm();
        $check = null;
        if(Yii::$app->request->isPost){
            $model->load(Yii::$app->request->Post());
                if($model->save())
                    $check = 1;
                else
                    $check = 2;
        }
        return $this->render('need_help',[
            'model' => $model,
            'check' => $check
        ]);
   }*/

   public function actionThankYou()
   {
        return $this->render('thank_you');
   }

   public function actionLockedDashboard()
   {
        $model = new ContactForm();
        return $this->render('locked_dashboard', [
             'model' => $model,
        ]);
   }

    public function actionBlockedDashboard()
   {
        /*VarDumper::dump(Yii::$app->user->identity);
                 die();*/
        $model = new ContactForm();
        return $this->render('blocked_dashboard', [
             'model' => $model,
        ]);
   }

   public function actionTermsAndConditions()
   {
        $seodata = Seodata::findOne(3);

        \Yii::$app->view->registerMetaTag([
            'name' => 'keywords',
            'content' => $seodata->translatateData->page_keywords,
        ]);
        \Yii::$app->view->registerMetaTag([
            'name' => 'description',
            'content' => $seodata->translatateData->page_description,
        ]);

            return $this->render('terms_and_conditions',[
                'seodata' => $seodata
            ]);
   }

   public function actionSearchLog($search = null)
   {
        $model = new Searches();
        $model->search = $search;
        $model->save();

        $getparams = array_merge(['post/search-result'], ['PropertyFilterSearch[keyword]' => $search ]);

        $url = Yii::$app->urlManager->createUrl($getparams);
        return $this->redirect($url);
   }

    public function actionContactUs()
    {
        $model = new ContactForm();
        if(Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) 
        {
            if($model->save()){
                SiteHelpers::sendSiteEmails('C', Yii::$app->params['supportEmail'], $model);
                return 1;
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        else
        {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }

    public function actionAbout()
    {
        $seodata = Seodata::findOne(1);

        \Yii::$app->view->registerMetaTag([
            'name' => 'keywords',
            'content' => $seodata->translatateData->page_keywords,
        ]);
        \Yii::$app->view->registerMetaTag([
            'name' => 'description',
            'content' => $seodata->translatateData->page_description,
        ]);
        if(Yii::$app->language == 'en'){
            return $this->render('about_en',[
                'seodata' => $seodata
            ]);
        }
        else
            return $this->render('about_ar',[
                'seodata' => $seodata
            ]);
    }
    
    public function actionAutoCompleteSearch()
    {

       if(Yii::$app->request->isPost){
        $keyword = Yii::$app->request->Post('search');
        $html = '';
        if(trim($keyword) != null)
        {
           $autocompleteList = $this->makeAutoCompleteList($keyword); 
           if($autocompleteList){
            foreach ($autocompleteList as $value) {
                $html.='<li>'.$value["listname"].'</li>';
            }
        }
        }
        
        // VarDumper::dump($autocompleteList);
        // die;
        //$val = Property::find()->where(['LIKE', 'city', $keyword])->groupBy('city')->all();
        
        
        return $html;
       }
    }
    public function makeAutoCompleteList($search_keyword)
    {
        $autocompleteList = array();
        // make property_type list

        // make city list
        $city_list = $this->makeCityPropertiesList($search_keyword);
        //make neighbourhood list
        //$neighbourhood_list = $this->makeNeighbourhoodPropertiesList($search_keyword);
        //$propertyType_list = $this->makePropertiesTypeList($search_keyword);
        //$autocompleteList =array_merge($city_list, $neighbourhood_list, $propertyType_list);
        //return $autocompleteList;
        return $city_list;
    }
    public function makeCityPropertiesList($search_keyword)
    {
        $properties = Property::find()->select('city as listname')
                        ->where(['LIKE', 'city', $search_keyword.'%', false])
                        ->andWhere(['status' => 2, 'rent_status' => 1])
                        ->groupBy('city')
                        ->asArray()
                        ->all();
        return $properties;
    }
    public function makeNeighbourhoodPropertiesList($search_keyword)
    {
        $properties = Property::find()->select('neighbourhood as listname')
                        ->where(['LIKE', 'neighbourhood', $search_keyword.'%', false])
                        ->groupBy('neighbourhood')
                        ->asArray()
                        ->all();
        return $properties;
    }
    public function makePropertiesTypeList($search_keyword)
    {
        $properties = PropertyTypeTranslation::find()->select('name as listname')
                        ->where(['LIKE', 'name', $search_keyword.'%', false])
                        ->andWhere(['=', 'locale', Yii::$app->language])
                        ->groupBy('name')
                        ->asArray()
                        ->all();
        /*$properties = PropertyType::translatateData()->find()->select('name as listname')
                        ->all();*/
        return $properties;
    }

    /*public function actionDrawMap(){

        return $this->render('draw_map');
    }*/

    //mail dummy
    public function actionTestMail($link = 0, $email = null){

        $mail_verification_code = '1siinaHn1eLZwtfSoM%2FQ2w%3D%3D';
        if($email == null){
            $email = 'ankit@technoexponent.com';
        }
        if($link == 0){
            $link = Yii::$app->urlManager->createUrl(['site/email-verification' ,'key'=>$mail_verification_code]);
        }
        else
            $link = Yii::$app->urlManager->createAbsoluteUrl(['site/email-verification' ,'key'=>$mail_verification_code]);

        $setting = Setting::findOne(1);
        Yii::$app->mailer->compose(
            ['html' => 'verificationCode-html', 'text' => 'verificationCode-text'],
            ['verification_code' => Yii::$app->urlManager->createAbsoluteUrl(['site/email-verification' ,'key'=>$mail_verification_code])]
            )
        ->setFrom([$setting->contact_email => $setting->site_title . ' robot'])
        ->setTo($email)
        ->setSubject('Test mail for link'.$setting->site_title)
        ->send();

        //echo 'done';
    }

    //everything above here
    //
    public function actionTestSms($register_sms_text = 'testing', $phone = '9038880155')
    {
            // echo $register_sms_text;
            // echo $phone;
            // die;
            // http://dev4.technoexponent.net/frlan/frontend/web/site/test-sms?register_sms_text=testing&phone=+919038880154
            require_once(Yii::getAlias('@common')."/lib/Twilio/autoload.php");
            $sid = 'AC0bd5abecf8ccf12001ecb64eec21bed8';
            $token = 'ac060fea6a99724b5d65d7734a47a1ed';

            $client = new \Twilio\Rest\Client($sid, $token);

            //$register_sms_text = Yii::$app->view->renderFile('@common/sms/register_sms.php');

            try {
                    $sms = $client->messages->create(
                                    '+966'.$phone,
                                    array(
                                    'from' => '+16697211595',
                                    'body' => $register_sms_text
                                    )
                    );
                VarDumper::dump($sms);
            } catch ( \Twilio\Exceptions\RestException $e ) {
                VarDumper::dump($e->getMessage());
            }
    }

    public function actionResendRegistrationEmail($token = null){
        $user = User::findIdentityByAccessToken($token);
        if($user){
            SiteHelpers::sendSiteEmails('R',$user->email, $user);
            return 1;
        }
        else
            return 0;
    }


}