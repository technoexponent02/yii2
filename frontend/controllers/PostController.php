<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use common\models\Setting;
//use frontend\models\ContactForm;

use common\models\User;
use yii\helpers\ArrayHelper;

use common\models\Property;
use common\models\PropertyCategory;
use common\models\PropertyCondition;
use common\models\PropertyFeatures;
use common\models\PropertyFeatureMatcher;
use common\models\PropertyImages;
use common\models\PropertyRentMethod;
use common\models\PropertyType;
use common\models\RequestSubuser;
use frontend\models\PropertySearch;
use frontend\models\UserSearch;
use common\models\Reports;
use common\models\PropertyReports;
use common\models\Searches;
use frontend\models\ProfileVerification;

use common\models\FileUpload;

use frontend\models\AddPostForm;
use yii\helpers\VarDumper;

use frontend\models\PropertyFilterSearch;
use common\components\SiteHelpers;
use frontend\models\PropertyFilterSearchMap;

use common\components\SubUserRequest;
use common\models\Notifications;

/**
 * individual controller
 https://www.twilio.com/blog/2016/03/how-to-validate-phone-numbers-in-php-with-the-twilio-lookup-api.html
 */
class PostController extends Controller
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
                        'actions' => [//Removed for confidentiality],
                        'allow' => true,
                        'roles' =>[User::ROLE_INDIVIDUAL, User::ROLE_ORGANIZATION_USER, User::ROLE_ORGANIZATION],
                    ],
                    [
                        'actions' => [//Removed for confidentiality],
                        'allow' => true,
                        'roles' =>[User::ROLE_INDIVIDUAL, User::ROLE_ORGANIZATION_USER],
                    ],
                    [
                        'actions' => [//Removed for confidentiality],
                        'allow' => true,
                        'roles' =>[ User::ROLE_ORGANIZATION ],
                    ],
                    [
                        'actions' => [//Removed for confidentiality],
                        'allow' => true,
                        'roles' =>[User::ROLE_ORGANIZATION, User::ROLE_ORGANIZATION_USER],
                    ],
                    [
                        'actions' => [//Removed for confidentiality],
                        'allow' => true,
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
        return $this->render('dashboard', [
                //'model' => $model,
            ]);
    }

   



    public function actionPropertyTypeList($id)
    {
        $propertyType_list = PropertyType::getCategoryDropdownList($id);

        $html = '';
        foreach ($propertyType_list as $list) {
            $html.= '<option value="'.$list['id'].'">'.$list['category'].'</option>';
        }

        return($html);
    }

    public function actionRentMethodList($id)
    {
        $rentMethod_list = PropertyRentMethod::getCategoryDropdownList();

        $html = '';
   //Removed for confidentiality
        return($html);
    }


    public function actionManageAccount(){

        $requestSubuser = RequestSubuser::findOne(['user_id' => Yii::$app->user->id, 'status' => 1]);
        $dataProvider = User::find()->where(['parent_id' => YII::$app->user->id])->orderBy('id', SORT_DESC)->all();

        return $this->render('manageAccount', [
            'dataProvider' => $dataProvider,
            'requestSubuser' => $requestSubuser,
            ]);
    }

    public function actionSubuserRequest($val)
    {
        $requestSubuser = RequestSubuser::findOne(['user_id' => Yii::$app->user->id, 'status' => 1]);
        if(!$requestSubuser){
            $requestSubuser =  new RequestSubuser();
        }
        $requestSubuser->user_id = Yii::$app->user->id;
        $requestSubuser->requested_user = $val;
        $requestSubuser->status = 1;


        if($requestSubuser->save()){
            $count_subuser = User::find()->where(['parent_id' => $requestSubuser->user_id, 'status' => 10])->count();

            SiteHelpers::createSubuser($requestSubuser->user_id, $requestSubuser->requested_user);
            $sub_user_request = new SubUserRequest();
            $sub_user_request->username = Yii::$app->user->identity->name;
            $sub_user_request->package = $requestSubuser->requested_user;
            $sub_user_request->user_id = $requestSubuser->user_id;
            $sub_user_request->no_of_users = $count_subuser;

            SiteHelpers::sendSiteEmails('S',Yii::$app->params['requestSubuserEmail'],$sub_user_request);
            return 1;
        }
        return 0;

    }

    public function actionRentalStatus($id){

        $model = AddPostForm::findOne(['id' => $id]);
        $status = $model->rent_status;
        ($status == 0)? $model->rent_status = 1 : $model->rent_status = 0;
        $model->save(false);
        return 1;
    }

    public function actionRemoveImage($id){

        $propertyImage = PropertyImages::findOne(['id' => $id]);

        if ($propertyImage->property_image!="" &&
            file_exists(Yii::getAlias('@backend').'/web/upload/property_images/'.$propertyImage->property_image))
        {
            unlink(Yii::getAlias('@backend').'/web/upload/property_images/'.$propertyImage->property_image);
        }
        $property = $propertyImage->getProperty()->one();

        if($propertyImage->delete()){
            $property->status = 0;
            $property->save(false);
            return 1;
        }
        return 0;
    }


    public function actionAllPost()
    {
        $searchModel = new PropertySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $property_categories = PropertyCategory::getCategoryDropdownList();
        return $this->render('allPost', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'property_categories' => $property_categories
            ]);
    }

    public function actionAllPostSubuser()
    {
        $searchModel = new PropertySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $property_categories = PropertyCategory::getCategoryDropdownList();
        return $this->render('allPostSub', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'property_categories' => $property_categories
            ]);
    }

    public function actionViewPost($id, $access = null)
    {
        $view_allow = "A";
        $model = AddPostForm::findOne(['id' => $id]);
        if ($access != null )
        {
           if (!in_array(base64_decode($access), [1, 2, 7]))
           {
              $view_allow = "NA";
           }

        }
        else
        {
           if (in_array($model->status, [0, 1, 3]))
           {
               $view_allow = "NA";
           }
        }
        if ($view_allow == "NA")
        {
           //$this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
           return $this->redirect(Yii::$app->urlManager->baseUrl);
           exit;
        }
        //$model->scenario = 'save_post';
        $property_categories = PropertyCategory::getCategoryDropdownList();
        $propertyCondition = PropertyCondition::getCategoryDropdownList();
        $propertyFeatures = PropertyFeatures::getCategoryDropdownList();
        $rentMethod = PropertyRentMethod::getCategoryDropdownList();
        $propertyType = PropertyType::getCategoryDropdownList($model->property_category);
        $features = PropertyFeatures::getCategoryDropdownList();
        $feature_raw_data = $model->propertyFeatureMatchers;
        $property_images = $model->getPropertyImages()->orderBy(['id'=>SORT_DESC])->all();
        $reports = Reports::getCategoryDropdownList();

        $feature_data = [];
        foreach($feature_raw_data as $val){

            $model->features[] = $val->feature_id;
        }

         \Yii::$app->view->registerMetaTag([
            'og:url' => Yii::$app->request->getAbsoluteUrl(),
            'og:type' =>'Property',
            'og:title' =>'Waarf',
            'og:description' =>$model->additional_information,
            'og:image' => Yii::$app->backendUrlManager->createAbsoluteUrl(['upload/property_images/'.$property_images[0]->property_image]),
        ]);

        return $this->render('viewPost',[
            'model' => $model,
            'features' => $features,
            'property_images' => $property_images,
            'reports' => $reports,
        ]);
    }

    public function actionManageAccountAjax($id){
        $model = User::findOne(['id' => $id]);
        $input = Yii::$app->request->Post();

        switch($input['type']){
            case 'S':
                if($input['pass'])
                    $model->password =$input['pass'];
                    $model->name = $input['name'];
                    $name = explode(" ", $input['name']);
                    $model->first_name = $name[0];
                    $model->last_name = $name[1];
                break;
            case 'B':
                    $model->status = User::STATUS_BLOCKED;
                break;
            case 'A':
                    $model->status = User::STATUS_ACTIVE;
                break;
            case 'D':
                    $model->status = User::STATUS_DELETED;
                break;
            default :
                break;
        }
        if($model->save()){
            return 1;
        }
        else
            return 0;
    }


    public function actionHistory(){

        $searchModel = new PropertySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('history', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            ]);
    }

    public function actionReportPost($post_id, $report_id){

        $model = new PropertyReports();
        $property = AddPostForm::findOne(['id' => $post_id]);
        $model->report_id = $report_id;
        $model->post_id = $post_id;
        $model->parent_id = $property->parent_id;
        $model->user_id = $property->user_id;
        $model->approved_by = $property->approved_by;
        $model->published_on = $property->updated_at;
        if($model->save()){
            return 1;
        }
        else
            return 0;

    }

    public function actionSearchResult()
    {
        //VarDumper::dump(Yii::$app->request->queryParams);

        $searchModel = new PropertyFilterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $search_log = Yii::$app->request->queryParams;


        $model = new Searches();
        if(isset($search_log['PropertyFilterSearch']['keyword']))
            $model->search = $search_log['PropertyFilterSearch']['keyword'];
        if(isset($search_log['PropertyFilterSearch']['no_of_bathroom']))
            $model->baths = $search_log['PropertyFilterSearch']['no_of_bathroom'];
        if(isset($search_log['PropertyFilterSearch']['no_of_room']))
            $model->rooms = $search_log['PropertyFilterSearch']['no_of_room'];
        if(isset($search_log['PropertyFilterSearch']['price']))
            $model->price = $search_log['PropertyFilterSearch']['price'];
        if(isset($search_log['PropertyFilterSearch']['property_type']))
            $model->type = $search_log['PropertyFilterSearch']['property_type'];
        $model->results = $dataProvider->getTotalCount();
        $model->save(false);

        //VarDumper::dump($dataProvider->getModels());

        $property_categories = PropertyCategory::getCategoryDropdownList();
        $propertyType = PropertyType::getCategoryDropdownList($searchModel->property_category);
        //$propertyType = PropertyType::getCategoryDropdownList();
        $city = Property::find()->select('city')
                        ->where(['status' => 2, 'rent_status' => 1])
                        ->groupBy('city')
                        ->asArray()
                        ->all();
        $neighbourhood = Property::find()->select('neighbourhood')
                        ->where(['status' => 2, 'rent_status' => 1])
                        ->groupBy('neighbourhood')
                        ->asArray()
                        ->all();

    //Removed for confidentiality
        //print_r($dataProvider->getKeys());
        $search_data = $dataProvider->getKeys() ? implode(",", $dataProvider->getKeys()) : 0;


   //Removed for confidentiality

            $connection = Yii::$app->getDb();
            $command = $connection->createCommand($sql);
            $slideFilters = $command->queryOne();


        $max_price = ($slideFilters['max_price'])? $slideFilters['max_price'] : 0;
        $min_price =  ($slideFilters['min_price'])? $slideFilters['min_price'] : 0;
        $max_size = ($slideFilters['max_size'])? $slideFilters['max_size'] : 0;
        $min_size =  ($slideFilters['min_size'])? $slideFilters['min_size'] : 0;

        $floors = [];
        $bathrooms = [];
        for ($i=1; $i<=11; $i++)
        {
            $rooms[$i] = $i;
            $bathrooms[$i] = $i;
            if($i == 11){
                $rooms[$i] = '10+';
                $bathrooms[$i] = '10+';
            }
        }

       /* echo $dataProvider->getTotalCount();
        die();*/

        return $this->render('searchResult', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'property_categories' => $property_categories,
            'property_type' => $propertyType,
            'city' => $city,
            'neighbourhood' => $neighbourhood,
            'rooms' => $rooms,
            'bathrooms' => $bathrooms,
            'max_price' => $max_price,
            'min_price' => $min_price,
            'max_size' => $max_size,
            'min_size' => $min_size,
            ]);
    }


    public function actionProfileVerification($token = NULL)
    {
        $otp_verified = 0;
        $email='';
        if($token == NULL)
        {
            return $this->redirect(['/']);
        }
        $user_by_auth_key = User::findIdentityByAccessToken($token);
        $model = new ProfileVerification($token);


        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->validate())
        {

            $user = User::findIdentityByVerificationCode2($model->verification_code);

            if(!$user)
            {
                return $this->redirect(['/']);

            }
            else
            {
                $session = Yii::$app->session;
                $user->verification_code = NULL;
                $user->otp_request = 1;
                $user->phone = $session['user']['phone'];
                $session->remove('user');
                $user->save(false);

                return $this->redirect(['site/thank-you']);
                //return $this->redirect(Yii::$app->request->referrer);

            }
        }

            //print_r($model->getErrors());
            //exit;
            return $this->render('profileVerification', [
                'model' => $model,
                'token' => $token,
                'user_by_auth_key' => $user_by_auth_key,
                'otp_verified' => $otp_verified,
                'email' => $email,

            ]);
    }

    public function actionResendOtp()
    {
        $input_data = Yii::$app->request->post();
        $session = Yii::$app->session;
        $user = User::findIdentityByAccessToken($input_data['token']);
        if($user->otp_request > 3){
            $user->otp_request = 1;
            $user->verification_code = null;
            $user->save(false);
            $session->remove('user');
            if($user->user_type == 3){
                $dashboardUrl = Yii::$app->urlManager->createUrl(['individual/dashboard']);
            }
            else
            {
                $dashboardUrl = Yii::$app->urlManager->createUrl(['organization/dashboard']);
            }
            return $dashboardUrl;
        }
        else{
            $user->otp_request = $user->otp_request + 1;
            $verification_code = mt_rand(100000, 999999) . $user->id;
            $user->verification_code = $verification_code;

            $user->save(false);

            $response = SiteHelpers::phoneMobilyVerificationSms($verification_code , $session['user']['phone']);
            return 1;
        }

    }

  

    public function actionUpdateNotification($id){
        $model = Notifications::findOne(['id' => $id]);
        if($model->status == 0)
        {
            $model->status = 1;
            $model->save(false);
        }
    }

    public function actionTd($id)
    {
        if($id)
        SiteHelpers::deleteUser($id);
    }

    public function actionTestNoti()
    {
        /*$user = User::findOne(32);
        SiteHelpers::sendNotification($user, 'W', 34);*/
        SiteHelpers::phoneMobilyVerificationSms('7891');

    }

    public function actionResetFilters()
    {
        $sql = 'SELECT max(rent_price) as max_price, max(lot_size) as max_size, min(rent_price) as min_price, min(lot_size) as min_size  FROM `property` LEFT JOIN `user` ON `property`.`user_id` = `user`.`id` WHERE ((`property`.`status`=2) AND (`rent_status`=1)) AND (`user`.`status`=10)';

        $connection = Yii::$app->getDb();
        $command = $connection->createCommand($sql);
        $slideFilters = $command->queryOne();

        $response['max_price'] = ($slideFilters['max_price'])? $slideFilters['max_price'] : 0;
        $response['min_price'] =  ($slideFilters['min_price'])? $slideFilters['min_price'] : 0;
        $response['max_size'] = ($slideFilters['max_size'])? $slideFilters['max_size'] : 0;
        $response['min_size'] =  ($slideFilters['min_size'])? $slideFilters['min_size'] : 0;

        return $response;

    }

    public function actionAddPost($step=1,$post=0)
    {
        //validation for authenticity
        if($post!=0 && $this->authenticateProperty($post) == 0 ){
            return $this->goBack();
        }
        if(Yii::$app->request->Post('delete') && $this->authenticateProperty($post) == 1)
        {
            $model = AddPostForm::findOne(['id' => $post]);
            $propertyImages = $model->getPropertyImages()->orderBy(['id'=>SORT_DESC])->all();
                foreach ($propertyImages as $propertyImage) {
                if($propertyImage->property_image!="" &&
                      file_exists(Yii::getAlias('@backend').'/web/upload/property_images/'.$propertyImage->property_image))
                  {
                      unlink(Yii::getAlias('@backend').'/web/upload/property_images/'.$propertyImage->property_image);
                  }
                }
            $model->delete();

            $user = Yii::$app->user->identity;
            switch ($user->getRole())
            {
                case User::ROLE_INDIVIDUAL:
                    return $this->redirect(['individual/dashboard']);
                    break;
                case User::ROLE_ORGANIZATION_USER:
                    return $this->redirect(['subuser/dashboard']);
                    break;
            }
        }
        if(Yii::$app->request->Post('previous') && $this->authenticateProperty($post) == 1)
        {
            return $this->redirect(['post/add-post?step='.($step-1).'&post='.$post]);
        }
        if(Yii::$app->request->Post('save') && $this->authenticateProperty($post) == 1)
        {
            $model = AddPostForm::findOne([$post]);
            if($step == 1 || $step == 4 || $step == 2){
                $model->load(Yii::$app->request->post());
                $model->one_time_payment_price = $model->one_time_payment ? $model->one_time_payment_price : null;
                $model->save();
            }
            if($step == 2)
            {
                $features_data = $model->features;
                $removeFeature = PropertyFeatureMatcher::deleteAll(['property_id' => $post]);
                foreach ($features_data as $val) {
                    $addFeature = new PropertyFeatureMatcher();
                    $addFeature->property_id = $model->id;
                    $addFeature->feature_id = $val;
                    $addFeature->save(false);
                }
            }

            $user = Yii::$app->user->identity;
            switch ($user->getRole())
            {
                case User::ROLE_INDIVIDUAL:
                    return $this->redirect(['post/all-post']);
                    break;
                case User::ROLE_ORGANIZATION_USER:
                    return $this->redirect(['post/all-post-subuser']);
                    break;
            }

        }

        switch($step)
        {
            case 1:
                $model = new AddPostForm();
                $propertyCondition = PropertyCondition::getCategoryDropdownList();
                $propertyType = [];

                if($model->load(Yii::$app->request->post()))
                {
                    if($model->validate()){

                        $model->status = 0;
                        $model->user_id = Yii::$app->user->id;
                        $model->modified_by = Yii::$app->user->id;
                        $model->user_type = Yii::$app->user->identity->user_type;
                        $model->parent_id = Yii::$app->user->identity->parent_id;
                        $model->locale = Yii::$app->language;
                        if($model->save()){
                            return $this->redirect(['post/add-post?step=2&post='.$model->id]);
                        }
                    }
                    else
                        return ActiveForm::validate($model);
                }
                return $this->render('add_post_step_1',[
                    'model' => $model,
                    'property_type' => $propertyType,
                    'property_condition' => $propertyCondition,
                ]);
                break;

            case 2:
                    $model = AddPostForm::findOne(['id' => $post]);

                    $model->scenario = 'add_post_step_2';
                    if($model->load(Yii::$app->request->post()))
                    {
                        $features_data = $model->features;
                        $removeFeature = PropertyFeatureMatcher::deleteAll(['property_id' => $post]);
                        foreach ($features_data as $val) {
                            $addFeature = new PropertyFeatureMatcher();
                            $addFeature->property_id = $model->id;
                            $addFeature->feature_id = $val;
                            $addFeature->save(false);
                        }
                        if($model->validate()){
                            $model->one_time_payment_price = $model->one_time_payment ? $model->one_time_payment_price : null;
                            $model->status = 0;
                            $model->locale = Yii::$app->language;
                            if($model->save()){
                                return $this->redirect(['post/add-post?step=3&post='.$model->id]);
                            }
                        }
                        else{
                            Yii::$app->response->format = Response::FORMAT_JSON;
                            return ActiveForm::validate($model);
                        }
                    }

                    $features = PropertyFeatures::getCategoryDropdownList();
                    $rentMethod = PropertyRentMethod::getCategoryDropdownList();
                    $currentYear = date("Y");
                    $built_year = [];
                    $rooms = [];
                    $floors = [];
                    $bathrooms = [];
                    $units = [];


                    for ($i=$currentYear; $i>=1960 ; $i--)
                        $built_year[$i] = $i;

                    for ($i=0; $i<=51 ; $i++){
                        $units[$i] = $i;
                        $floors[$i] = $i;
                        if($i == 51){
                            $floors[$i] = '50+';
                            $units[$i] = '50+';
                        }
                    }

                    for ($i=1; $i<=11; $i++){
                        $rooms[$i] = $i;
                        $bathrooms[$i] = $i;
                        if($i == 11){
                            $rooms[$i] = '10+';
                            $bathrooms[$i] = '10+';
                        }
                    }

                    for ($i=$currentYear; $i>=1960 ; $i--)
                        $built_year[$currentYear] = $currentYear;

                    $feature_data = [];
                    $feature_raw_data = $model->propertyFeatureMatchers;
                    foreach($feature_raw_data as $val){
                        $model->features[] = $val->feature_id;
                    }

                    return $this->render('add_post_step_2',[
                        'model' => $model,
                        'features' => $features,
                        'feature_data' => $feature_data,
                        'built_year' => $built_year,
                        'rooms' => $rooms,
                        'floors' => $floors,
                        'bathrooms' => $bathrooms,
                        'units' => $units,
                        'rent_method' => $rentMethod,
                    ]);
                break;

            case 3:
                $json = file_get_contents('php://input');
                //print_r($json);
                $obj = json_decode($json, true);
                // print_r($obj['final_list_images[]']);
                // die;
                $model = AddPostForm::findOne(['id' => $post]);
                $property_images = $model->getPropertyImages()->orderBy(['is_cover'=>SORT_DESC])->all();

                //if (isset($_POST['final_list_images']))
                if (isset($obj['final_list_images[]']))
                {
                    $is_cover_set = 'NA';
                    // print_r($obj['final_list_images[]']);
                    // print_r($obj['is_cover[]']);
                    // die;
                    // VarDumper::dump($_FILES);
                    // VarDumper::dump($_POST); die;
                    $propertyImages = $model->getPropertyImages()->orderBy(['is_cover'=>SORT_DESC])->all();
                    foreach ($propertyImages as $propertyImage) {
                    if($propertyImage->property_image!="" &&
                          file_exists(Yii::getAlias('@backend').'/web/upload/property_images/'.$propertyImage->property_image))
                      {
                          unlink(Yii::getAlias('@backend').'/web/upload/property_images/'.$propertyImage->property_image);
                      }
                      $propertyImage->delete();
                    }
                    // foreach ($_POST['final_list_images'] as $key => $value) {
                    if (is_array(($obj['final_list_images[]'])))
                    {
                        foreach ($obj['final_list_images[]'] as $key => $value) {
                            $filename =  uniqid() . '.png';
                            $file = "backend/web/upload/property_images/" .$filename ;

                            $data = $value;
                            list($type, $data) = explode(';', $data);
                            list(, $data)      = explode(',', $data);
                            $data = base64_decode($data);

                            file_put_contents($file, $data);

                            $propertyImage = new PropertyImages();
                            $propertyImage->user_id = Yii::$app->user->id;
                            $propertyImage->property_id = $model->id;
                            $propertyImage->property_image = $filename;
                            $propertyImage->is_cover = $obj['is_cover[]'][$key];
                            $propertyImage->save(false);

                            if ($obj['is_cover[]'][$key] == 1)
                            {
                                $is_cover_set = 'A';
                            }

                        }
                    }
                    else
                    {
                            $filename =  uniqid() . '.png';
                            $file = "backend/web/upload/property_images/" .$filename ;

                            $data = $obj['final_list_images[]'];
                            list($type, $data) = explode(';', $data);
                            list(, $data)      = explode(',', $data);
                            $data = base64_decode($data);

                            file_put_contents($file, $data);

                            $propertyImage = new PropertyImages();
                            $propertyImage->user_id = Yii::$app->user->id;
                            $propertyImage->property_id = $model->id;
                            $propertyImage->property_image = $filename;
                            $propertyImage->is_cover = $obj['is_cover[]'];
                            $propertyImage->save(false);
                    }

                    if($is_cover_set == 'NA')
                    {
                        $set_first_as_cover = PropertyImages::find()->where(['property_id' => $model->id])->one();
                        $set_first_as_cover->is_cover = 1;
                        $set_first_as_cover->save();
                    }

                    if($obj['save_as_draft']==1)
                        return 1;
                    else
                        return 0;

                }

                return $this->render('add_post_step_3',[
                    'model' => $model,
                     'property_images' => $property_images
                    ]);
                break;
            case 4:
                $model = AddPostForm::findOne(['id' => $post]);
                $model->scenario = 'add_post_step_4';

                if($model->load(Yii::$app->request->post()))
                {
                    if($model->validate()){
                        $model->status = 0;
                        $model->locale = Yii::$app->language;
                        if($model->save()){
                            return $this->redirect(['post/add-post?step=5&post='.$model->id]);
                        }
                    }
                    else{
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        return ActiveForm::validate($model);
                    }
                }


                return $this->render('add_post_step_4',[
                   'model' => $model,
                ]);
                break;
            case 5:
                $model = AddPostForm::findOne(['id' => $post]);
                if(Yii::$app->request->isPost)
                {
                    if(Yii::$app->request->Post('publish')){
                        $model->status = 1;
                        if($this->validateProperty($post)!=5){
                            return $this->redirect(['post/add-post?step='.$this->validateProperty($post).'&post='.$model->id]);
                        }
                        if($model->save())
                        {
                            if($model->user_type == 3)
                                return $this->redirect(['post/all-post']);
                            else if($model->user_type == 5){
                                $model->status = 2;
                                $model->approved_by = $model->parent_id;
                                $model->save();
                                return $this->redirect(['post/all-post-subuser']);
                            }
                        }

                     }
                    else{
                        return $this->redirect(['post/add-post?step=2&post='.$model->id]);
                    }
                }

                $property_categories = PropertyCategory::getCategoryDropdownList();
                $propertyCondition = PropertyCondition::getCategoryDropdownList();
                $propertyFeatures = PropertyFeatures::getCategoryDropdownList();
                $rentMethod = PropertyRentMethod::getCategoryDropdownList();
                $propertyType = PropertyType::getCategoryDropdownList($model->property_category);
                $features = PropertyFeatures::getCategoryDropdownList();
                $feature_raw_data = $model->propertyFeatureMatchers;
                $property_images = $model->getPropertyImages()->orderBy(['id'=>SORT_DESC])->all();
                $reports = Reports::getCategoryDropdownList();

                $feature_data = [];
                foreach($feature_raw_data as $val){

                    $model->features[] = $val->feature_id;
                }
                return $this->render('add_post_step_5',[
                    'model' => $model,
                    'features' => $features,
                    'property_images' => $property_images,
                    'reports' => $reports,
                ]);
                break;
            default:
                return $this->render('add_post_step_1',[]);
                break;
        }

    }

    protected function validateProperty($post_id)
    {
        $model_2 = AddPostForm::findOne(['id' => $post_id]);
        $model_4 = AddPostForm::findOne(['id' => $post_id]);

        $model_2->scenario = 'add_post_step_2';
        $model_4->scenario = 'add_post_step_4';

        $images = $model_2->getPropertyImages()->all();
        $no_of_img = count($images);
        $flag = 0;
        if($model_2->property_category == 1){
            $flag = $no_of_img>=2 ? 1 : 0;
        }
        else
            $flag = $no_of_img>=4 ? 1 : 0;

        if(!$model_2->validate()){
            return 2;
            }
        else if($flag == 0){
            return 3;
        }
        else if(!$model_4->validate()){
            return 4;
            }
        else
            return 5;
    }

    protected function authenticateProperty($post_id)
    {
        $user = Yii::$app->user->identity;
        $property_model = AddPostForm::findOne($post_id);
        if(!$property_model)
            return 0;
        switch ($user->getRole())
        {
            case User::ROLE_INDIVIDUAL:
                if($property_model->user_id == $user->id)
                    return 1;
                else
                    return 0;
                break;
            case User::ROLE_ORGANIZATION_USER:
                if($property_model->parent_id == $user->parent_id)
                    return 1;
                else
                    return 0;
                break;
            default:
                return 0;
                break;
        }
    }

    public function reArrayFiles(&$file_post) {

        $file_ary = array();
        $file_count = count($file_post['name']);
        $file_keys = array_keys($file_post);

        for ($i=0; $i<$file_count; $i++) {
            foreach ($file_keys as $key) {
                $file_ary[$i][$key] = $file_post[$key][$i];
            }
        }

        return $file_ary;
    }

    public function actionCoverUpdate()
    {
        /*$user = User::findOne(1);
        $user->password = 'admin123';
        $user->save();*/
        $all_id = [];
        $model = PropertyImages::find()->select('id')->groupBy('property_id')->all();
        foreach($model as $arr){

            $all_id[] = $arr->id;
        }
        echo $all_id; 
        //print_r($all_id);
        //PropertyImages::updateAll(['is_cover' => 1], 'id IN :all_id',[':all_id' => $all_id]);
    }





//everything  above here

}