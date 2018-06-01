<?php

namespace backend\controllers;

use Yii;
use common\models\Ads;
use backend\models\AdsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use common\models\FileUpload;
use yii\web\UploadedFile;

use common\components\SiteHelpers;
use common\models\User;
use yii\filters\AccessControl;

/**
 * AdsController implements the CRUD actions for Ads model.
 */
class AdsController extends Controller
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
                        'actions' => ['update'],
                        //'actions' => ['index', 'view', 'create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => [User::ROLE_ADMIN, User::ROLE_QUALITY_TEAM, User::ROLE_SUPERVISOR],
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
     * Lists all Ads models.
     * @return mixed
     */
    // public function actionIndex()
    // {
    //     $searchModel = new AdsSearch();
    //     $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    //     return $this->render('index', [
    //         'searchModel' => $searchModel,
    //         'dataProvider' => $dataProvider,
    //     ]);
    // }

    /**
     * Displays a single Ads model.
     * @param integer $id
     * @return mixed
     */
    // public function actionView($id)
    // {
    //     return $this->render('view', [
    //         'model' => $this->findModel($id),
    //     ]);
    // }

    /**
     * Creates a new Ads model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    // public function actionCreate()
    // {
    //     $model = new Ads();

    //     if ($model->load(Yii::$app->request->post()) && $model->save()) {
    //         return $this->redirect(['view', 'id' => $model->id]);
    //     } else {
    //         return $this->render('create', [
    //             'model' => $model,
    //         ]);
    //     }
    // }

    /**
     * Updates an existing Ads model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    //public function actionUpdate($id)
    public function actionUpdate()
    {
        $model = $this->findModel(1);

        $onead_image = $model->onead_image;
        $right_ad_image = $model->right_ad_image;
        $left_ad_image = $model->left_ad_image;


        if (Yii::$app->request->isPost) {

        
            if (UploadedFile::getInstance($model, 'onead_image'))
            {
                if ($model->onead_image!="" && SiteHelpers::checkFileExists($filepath = '/upload/ads/'.$model->onead_image))
                {
                    unlink(Yii::getAlias('@backend').'/web/upload/ads/'.$model->onead_image);
                }
            }

            if (UploadedFile::getInstance($model, 'right_ad_image'))
            {
                if ($model->right_ad_image!="" && SiteHelpers::checkFileExists($filepath = '/upload/ads/'.$model->right_ad_image))
                {
                    unlink(Yii::getAlias('@backend').'/web/upload/ads/'.$model->right_ad_image);
                }
            }

            if (UploadedFile::getInstance($model, 'left_ad_image'))
            {
                if ($model->left_ad_image!="" && SiteHelpers::checkFileExists($filepath = '/upload/ads/'.$model->left_ad_image))
                {
                    unlink(Yii::getAlias('@backend').'/web/upload/ads/'.$model->left_ad_image);
                }
            }

            $model->load(Yii::$app->request->post());

            if ($model->ad_type == 0)
            {
                if ($right_ad_image!="" && SiteHelpers::checkFileExists($filepath = '/upload/ads/'.$right_ad_image))
                {
                    unlink(Yii::getAlias('@backend').'/web/upload/ads/'.$right_ad_image);
                }
                if ($left_ad_image!="" && SiteHelpers::checkFileExists($filepath = '/upload/ads/'.$left_ad_image))
                {
                    unlink(Yii::getAlias('@backend').'/web/upload/ads/'.$left_ad_image);
                }

                if (UploadedFile::getInstance($model, 'onead_image'))
                {
                    $file_model = new FileUpload();
                    $file_model->singleFile = UploadedFile::getInstance($model, 'onead_image');
                    if($pro_image = $file_model->uploadSingle('web/upload/ads'))
                        {
                            $model->onead_image = $pro_image;
                        } 
                }
                else
                {
                    $model->onead_image = $onead_image;
                }  
                $model->right_ad_image = null;
                $model->right_ad_image_name = null;
                $model->left_ad_image = null;
                $model->left_ad_image_name = null;
            }
              
            if ($model->ad_type == 10)
            {
                if ($onead_image!="" && SiteHelpers::checkFileExists($filepath = '/upload/ads/'.$onead_image))
                {
                    unlink(Yii::getAlias('@backend').'/web/upload/ads/'.$onead_image);
                }

                if (UploadedFile::getInstance($model, 'right_ad_image'))
                {
                    $file_model = new FileUpload();
                    $file_model->singleFile = UploadedFile::getInstance($model, 'right_ad_image');
                    if($pro_image = $file_model->uploadSingle('web/upload/ads'))
                        {
                            $model->right_ad_image = $pro_image;
                        } 
                }
                else
                {
                    $model->right_ad_image = $right_ad_image;
                }
                if (UploadedFile::getInstance($model, 'left_ad_image'))
                {
                    $file_model = new FileUpload();
                    $file_model->singleFile = UploadedFile::getInstance($model, 'left_ad_image');
                    if($pro_image = $file_model->uploadSingle('web/upload/ads'))
                        {
                            $model->left_ad_image = $pro_image;
                        } 
                }
                else
                {
                    $model->left_ad_image = $left_ad_image;
                }  
                $model->onead_image = null;  
                $model->onead_image_name = null;
            }      

            $model->save();

            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Ads model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    // public function actionDelete($id)
    // {
    //     $this->findModel($id)->delete();

    //     return $this->redirect(['index']);
    // }

    /**
     * Finds the Ads model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Ads the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Ads::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
