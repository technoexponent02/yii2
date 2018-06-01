<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use common\models\UserDetails;
use backend\models\UserForm;
use backend\models\UserSearch;
use common\models\Setting;
use common\models\UserTransaction;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\PasswordResetRequestForm;
use yii\helpers\Json;
use yii\web\JsExpression;

use common\models\PropertyReports;

use backend\models\IndividualMyAccountForm;
use backend\models\OrganisationMyAccountForm;

use common\models\FileUpload;
use yii\web\UploadedFile;

use yii\helpers\VarDumper;
use common\components\SiteHelpers;
use common\models\RequestSubuser;

/**
 * UserController implements the CRUD actions for UserForm model.
 */
class UserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'update', 'delete', 
                        'organisation-update','report-delete', 'organisation-report-delete',
                        'organisation-subuser-delete','organisation-subuser-review', 'organisation-delete'],
                        'allow' => true,
                        'roles' => ['banUsers'],
                    ],
                    [
                        'actions' => ['change-password', 'change-password-process'],
                        'allow' => true,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all UserForm models.
     * @return mixed
     */
    public function actionIndex()
    {
         
        /*if(!in_array($type, [2, 3, 4, 5, 6]) || !in_array($typeName, [2, 3, 4, 5, 6]))*/

        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        //VarDumper::dump($dataProvider->getKeys());
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserForm model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $role = $model->getRole($id);

        if ($role == User::ROLE_ORGANIZATION_USER)
            {
                $model = $this->findModel($model->parent_id);
            }
        // $user = Yii::$app->user->identity;
        // $user_id = $id;
        
        
        $view_page = 'view';
        
        if ($role == User::ROLE_ORGANIZATION || $role == User::ROLE_ORGANIZATION_USER)
        {
            $view_page = 'organisation_view';
        }
        
        return $this->render($view_page, [
            'model' => $model,
            
        ]);
    }

    /**
     * Creates a new UserForm model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    // public function actionCreate()
    // {
        
    // }

    /**
     * Updates an existing UserForm model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $individualMyAccountForm =  $this->findIndividualMyAccountFormModel($id);
        


        $user_image = $individualMyAccountForm->user_image;

        if (Yii::$app->request->isPost) {

            if (UploadedFile::getInstance($individualMyAccountForm, 'user_image'))
            {
                if ($individualMyAccountForm->user_image!="" && 
                    file_exists(Yii::getAlias('@backend').'/web/upload/user_image/'.$individualMyAccountForm->user_image))
                {
                    unlink(Yii::getAlias('@backend').'/web/upload/user_image/'.$individualMyAccountForm->user_image);
                }
            }

            $individualMyAccountForm->load(Yii::$app->request->post());

            //VarDumper::dump(Yii::$app->request->post()); die;

            if (UploadedFile::getInstance($individualMyAccountForm, 'user_image'))
            {
                $file_model = new FileUpload();
                $file_model->singleFile = UploadedFile::getInstance($individualMyAccountForm, 'user_image');
                if($pro_image = $file_model->uploadSingle('web/upload/user_image'))
                    {
                        $individualMyAccountForm->user_image = $pro_image;
                    } 
            }
            else
            {
                $individualMyAccountForm->user_image = $user_image;
            }    
            //VarDumper::dump($individualMyAccountForm);
            if ($individualMyAccountForm->password_val != null)
            {
                $individualMyAccountForm->password = $individualMyAccountForm->password_val; 
            }

            if ($individualMyAccountForm->is_badge === "on")
            {
                $individualMyAccountForm->is_badge = 10;
            }
            else
            {
                $individualMyAccountForm->is_badge = 0;
            }
            if ($individualMyAccountForm->status != User::STATUS_BANNED)
                {
                    $individualMyAccountForm->reason =null;
                }

            // echo "P-".$individualMyAccountForm->password_val."-".$individualMyAccountForm->password;
            $individualMyAccountForm->name = $individualMyAccountForm->first_name." ".$individualMyAccountForm->last_name;
            $individualMyAccountForm->save();
            //VarDumper::dump($individualMyAccountForm); die;
            // return $this->render('update', [
            //     'individualMyAccountForm' => $individualMyAccountForm,
            // ]);
            
            if ($individualMyAccountForm->status == User::STATUS_BANNED)
                {
                    // echo 1; die;
                    SiteHelpers::sendSiteEmails('B', $individualMyAccountForm->email, $individualMyAccountForm);
                    /*$setting = Setting::findOne(1);
                    Yii::$app->mailer->compose(
                        ['html' => 'blockedMail-html', 'text' => 'blockedMail-text'],
                        ['user' => $individualMyAccountForm]
                    )
                    ->setFrom([$setting->contact_email => $setting->site_title . ' robot'])
                    ->setTo($individualMyAccountForm->email)
                    ->setSubject('Sorry you have been blocked ' . $setting->site_title)
                    ->send();*/
                }

            


            $params = array_merge(["user/update"], ["id" => $individualMyAccountForm->id]);
                        
            return $this->redirect(Yii::$app->urlManager->createUrl($params));
        }
        else {
             return $this->render('update', [
                'individualMyAccountForm' => $individualMyAccountForm,
            ]);
         }
    }
    public function actionOrganisationUpdate($id)
    {

        $organisationMyAccountForm =  $this->findOrganisationMyAccountFormModel($id);
        
        $request_sub_user = RequestSubuser::find()->where(['user_id' => $id, 'status' => 1])->one();


        $user_image = $organisationMyAccountForm->user_image;

        if (Yii::$app->request->isPost) {

            if (UploadedFile::getInstance($organisationMyAccountForm, 'user_image'))
            {
                if ($organisationMyAccountForm->user_image!="" && 
                    file_exists(Yii::getAlias('@backend').'/web/upload/user_image/'.$organisationMyAccountForm->user_image))
                {
                    unlink(Yii::getAlias('@backend').'/web/upload/user_image/'.$organisationMyAccountForm->user_image);
                }
            }

            $organisationMyAccountForm->load(Yii::$app->request->post());

            // VarDumper::dump(Yii::$app->request->post()); die;

            if (UploadedFile::getInstance($organisationMyAccountForm, 'user_image'))
            {
                $file_model = new FileUpload();
                $file_model->singleFile = UploadedFile::getInstance($organisationMyAccountForm, 'user_image');
                if($pro_image = $file_model->uploadSingle('web/upload/user_image'))
                    {
                        $organisationMyAccountForm->user_image = $pro_image;
                    } 
            }
            else
            {
                $organisationMyAccountForm->user_image = $user_image;
            }    
            // VarDumper::dump($organisationMyAccountForm->is_badge);
            // die();
            if ($organisationMyAccountForm->password_val != null)
            {
                $organisationMyAccountForm->password = $organisationMyAccountForm->password_val; 
            }

            if ($organisationMyAccountForm->is_badge === "on")
            {
                $organisationMyAccountForm->is_badge = 10;
            }
            else
            {
                $organisationMyAccountForm->is_badge = 0;
            }
            
            if ($organisationMyAccountForm->status != User::STATUS_BANNED)
                {
                    $organisationMyAccountForm->reason =null;
                }
            if ($organisationMyAccountForm->status == User::STATUS_UNACTIVATED)
                {
                    $organisationMyAccountForm->pending_verification =2;
                    $organisationMyAccountForm->status =10;
                }
                else
                {
                    $organisationMyAccountForm->pending_verification =0;
                    $models = User::find()->where(" parent_id = $id AND pending_verification = 2")->all();
                    if ($models)
                    {
                        foreach ($models as $model) {
                            $model->pending_verification = 0;
                            $model->save();
                        }
                    }

                }

            // echo "P-".$individualMyAccountForm->password_val."-".$individualMyAccountForm->password;
            
            //VarDumper::dump(Yii::$app->request->post()); die;
            // VarDumper::dump(Yii::$app->request->post()); die;
            // if (!$organisationMyAccountForm->save())
            // {
            //     VarDumper::dump($organisationMyAccountForm->getErrors()); die;
            // }
            $organisationMyAccountForm->save();
            // for sub users
            $posted_data = Yii::$app->request->post();
            // VarDumper::dump($posted_data); die;
            if (count($posted_data['OrganisationMyAccountForm']['sub_user_id']) > 0)
            {
                foreach ($posted_data['OrganisationMyAccountForm']['sub_user_id'] as $key => $sub_user_id) {
                    if ($sub_user_id != 0)
                    {
                        $sub_user = User::findOne($sub_user_id);
                        if ($posted_data['OrganisationMyAccountForm']['sub_user_name'][$key] !=null)
                        {
                            $sub_user->name = $posted_data['OrganisationMyAccountForm']['sub_user_name'][$key];  
                        }
                        if ($posted_data['OrganisationMyAccountForm']['sub_user_status'][$key] !=null)
                        {
                            $sub_user->status = $posted_data['OrganisationMyAccountForm']['sub_user_status'][$key];
                        }
                        if ($posted_data['OrganisationMyAccountForm']['sub_user_password'][$key] !=null)
                        {
                            $sub_user->password = $posted_data['OrganisationMyAccountForm']['sub_user_password'][$key];
                        }
                        if ($sub_user->is_requested == 1 && $sub_user->status == User::STATUS_ACTIVE)
                            {
                                $sub_user->expiration_date = time() + (365*24*60*60);
                                $sub_user->is_requested = 2;
                            }
                        $sub_user->save();
                    }
                    else
                    {
                       $newSubuser = new User();
                       $newSubuser->password = $posted_data['OrganisationMyAccountForm']['sub_user_password'][$key];
                       $newSubuser->parent_id = $organisationMyAccountForm->id;
                       $newSubuser->status = $posted_data['OrganisationMyAccountForm']['sub_user_status'][$key];
                       $newSubuser->name = $posted_data['OrganisationMyAccountForm']['sub_user_name'][$key];
                       $newSubuser->save(false);
                       $newSubuser->assignRole(User::ROLE_ORGANIZATION_USER);                
                       $newSubuser->user_type = 5;
                       $newSubuser->pending_verification = 0;
                       $newSubuser->username = User::ROLE_ORGANIZATION_USER . '-' . $newSubuser->id;
                       $newSubuser->sign_up_ip = SiteHelpers::getClientIP();
                       $newSubuser->expiration_date == time() + (365*24*60*60);
                       $newSubuser->save(false);
                    }

                       
                }
            }
           
            if ($organisationMyAccountForm->status == User::STATUS_BANNED)
                {
                    // echo 1; die;
                    SiteHelpers::sendSiteEmails('B', $organisationMyAccountForm->email, $organisationMyAccountForm);
                    /*$setting = Setting::findOne(1);
                    Yii::$app->mailer->compose(
                        ['html' => 'blockedMail-html', 'text' => 'blockedMail-text'],
                        ['user' => $organisationMyAccountForm]
                    )
                    ->setFrom([$setting->contact_email => $setting->site_title . ' robot'])
                    ->setTo($organisationMyAccountForm->email)
                    ->setSubject('Sorry you have been blocked ' . $setting->site_title)
                    ->send();*/
                }

            if ($organisationMyAccountForm->status == User::STATUS_BANNED)
                {
                    $id = $organisationMyAccountForm->id;
                    $models = User::find()->where(" parent_id = $id")->all();
                    if ($models)
                    {
                        foreach ($models as $model) {
                            $model->status = User::STATUS_BANNED;
                            $model->save();
                        }
                    }
                }

            $params = array_merge(["user/organisation-update"], ["id" => $organisationMyAccountForm->id]);
                        
            return $this->redirect(Yii::$app->urlManager->createUrl($params));
        }
        else {
             return $this->render('organisation_update', [
                'organisationMyAccountForm' => $organisationMyAccountForm,
                'request_sub_user' => $request_sub_user
            ]);
         }
    }
    protected function findIndividualMyAccountFormModel($id)
    {
        if (($model = IndividualMyAccountForm::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findOrganisationMyAccountFormModel($id)
    {
        if (($model = OrganisationMyAccountForm::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Deletes an existing UserForm model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        SiteHelpers::deleteUser($id);
        // $this->findModel($id)->delete();
        // Yii::$app->authManager->revokeAll($id);
        /*$user = User::findOne($id);
        $user->status = User::STATUS_DELETED;
        $user->save();*/
        return $this->redirect(['index']);
    }
    public function actionOrganisationDelete($id)
    {
        SiteHelpers::deleteUser($id);
        // $user = User::findOne($id);
        // $user->status = User::STATUS_DELETED;
        // $user->save();
        // $models = User::find()->where(" parent_id = $id")->all();
        // if ($models)
        // {
        //     foreach ($models as $model) {
        //         $user->status = User::STATUS_DELETED;
        //         $user->save();
        //     }
        // }
        
        return $this->redirect(['index']);
    }
    public function actionOrganisationSubuserDelete($id)
    {
        $user = User::findOne($id);
        $parent_id = $user->parent_id;
        /*$user->status = User::STATUS_DELETED;
        $user->save();*/
        SiteHelpers::deleteUser($id);
        $params = array_merge(["user/organisation-update"], ["id" => $parent_id]);
                        
        return $this->redirect(Yii::$app->urlManager->createUrl($params));
    }

    public function actionOrganisationSubuserReview($id)
    {
        $request_sub_user = RequestSubuser::findOne($id);
        $request_sub_user->status = 0;
        $request_sub_user->save();
        $params = array_merge(["user/organisation-update"], ["id" => $request_sub_user->user_id]);
                        
        return $this->redirect(Yii::$app->urlManager->createUrl($params));
    }
    public function actionReportDelete($id)
    {
        $property_report = PropertyReports::findOne($id);
        $user_id = $property_report->user_id;
        $property_report->delete();

        $params = array_merge(["user/update"], ["id" => $user_id]);
                        
        return $this->redirect(Yii::$app->urlManager->createUrl($params));

        //return $this->redirect(['user/update']);
    }

    public function actionOrganisationReportDelete($id)
    {
        $property_report = PropertyReports::findOne($id);
        $user_id = $property_report->parent_id;
        $property_report->delete();

        $params = array_merge(["user/organisation-update"], ["id" => $user_id]);
                        
        return $this->redirect(Yii::$app->urlManager->createUrl($params));

        //return $this->redirect(['user/update']);
    }

    /**
     * Finds the UserForm model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserForm the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {            
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
	
	public function actionChangePassword(){
        if(Yii::$app->user->id == 1){
            return $this->render('change_password', [
            ]);
        }
        else
            return $this->redirect(['properties/overview']);
    }
    
    public function actionChangePasswordProcess(){
        if(Yii::$app->user->id == 1 && Yii::$app->request->post()){
            $password = trim(Yii::$app->request->post('password'));
            if($password == Yii::$app->request->post('confirmPassword')){
                if($password != null && $password != ''){
                     $user = $this->findModel(1);
                    $user->password = $password;
                    $user->save(false);
                    return 1;
                }
                else
                    return 3;            
            }
            else
                return 2;
        }
        else
            return $this->redirect(['properties/overview']);
    }
	
}
