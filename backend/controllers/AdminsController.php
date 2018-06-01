<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use backend\models\AdminsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\AdminMyAccountForm;
use common\components\SiteHelpers;
use yii\helpers\VarDumper;

/**
 * AdminsController implements the CRUD actions for User model.
 */
class AdminsController extends Controller
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
                        //'actions' => ['update'],
                        'actions' => ['index', 'view', 'create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['accessToAdminManagement'],
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
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        //SiteHelpers::insertPermissions();
        $searchModel = new AdminsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $model = new AdminMyAccountForm();
        $permissions = Yii::$app->authManager->getPermissions();

        
        if (Yii::$app->request->isPost) 
        {
            $posted_data = Yii::$app->request->post();
            // VarDumper::dump(Yii::$app->request->post()); 
            // die;
            $model->load(Yii::$app->request->post());
            if ($model->password_val != null)
            {
                $model->password = $model->password_val; 
            }
            $model->name = $model->first_name . " " .$model->last_name;
            $model->save();
            if ($model->hasErrors())
            {
                return $this->render('create', [
                    'model' => $model,
                    'permissions' => $permissions
                ]);
            }
            if (isset($posted_data['AdminMyAccountForm']['permissions']) && 
                !empty($posted_data['AdminMyAccountForm']['permissions']))
            {
                switch ($model->user_type) {
                    case 1:
                        $model->assignRole(User::ROLE_ADMIN);
                        break;
                    case 2:
                        $model->assignRole(User::ROLE_QUALITY_TEAM);
                        break;
                    case 7:
                        $model->assignRole(User::ROLE_SUPERVISOR);
                        break;
                    default:
                        # code...
                        break;
                }
                foreach ($posted_data['AdminMyAccountForm']['permissions'] as $key => $permission) {
                    $permission = Yii::$app->authManager->getPermission($permission);
                    //VarDumper::dump($permission);
                    Yii::$app->authManager->assign($permission, $model->id);
                }                
            }

            
            
            return $this->redirect(['view', 'id' => $model->id]);
            
            //VarDumper::dump($model); 
            //die;
            
        } else {
            return $this->render('create', [
                'model' => $model,
                'permissions' => $permissions
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = AdminMyAccountForm::findOne($id);
        $permissions = Yii::$app->authManager->getPermissions();
        //VarDumper::dump($permissions); die;
        if (Yii::$app->request->isPost) 
        {
            $posted_data = Yii::$app->request->post();
            //VarDumper::dump(Yii::$app->request->post()); 
            //die;
            $model->load(Yii::$app->request->post());
            if ($model->password_val != null)
            {
                $model->password = $model->password_val; 
            }
            $model->name = $model->first_name . " " .$model->last_name;
            $model->save();
            if ($model->hasErrors())
            {
                return $this->render('update', [
                    'model' => $model,
                    'permissions' => $permissions
                ]);
            }
            if (isset($posted_data['AdminMyAccountForm']['permissions']) && 
                !empty($posted_data['AdminMyAccountForm']['permissions']))
            {
                switch ($model->user_type) {
                    case 1:
                        $model->assignRole(User::ROLE_ADMIN);
                        break;
                    case 2:
                        $model->assignRole(User::ROLE_QUALITY_TEAM);
                        break;
                    case 7:
                        $model->assignRole(User::ROLE_SUPERVISOR);
                        break;
                    default:
                        # code...
                        break;
                }
                foreach ($posted_data['AdminMyAccountForm']['permissions'] as $key => $permission) {
                    $permission = Yii::$app->authManager->getPermission($permission);
                    //VarDumper::dump($permission);
                    Yii::$app->authManager->assign($permission, $model->id);
                }                
            }

            
            
            return $this->redirect(['view', 'id' => $model->id]);
            
            //VarDumper::dump($model); 
            //die;
            
        } else {
            return $this->render('update', [
                'model' => $model,
                'permissions' => $permissions
            ]);
        }

        // if ($model->load(Yii::$app->request->post()) && $model->save()) {
        //     return $this->redirect(['view', 'id' => $model->id]);
        // } 
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        // $this->findModel($id)->delete();
        $user = User::findOne($id);
        $user->status = User::STATUS_DELETED;
        $user->save();
        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return User the loaded model
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
}
