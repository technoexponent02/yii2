<?php

namespace backend\controllers;



use Yii;

use common\models\User;

use yii\web\Controller;

use yii\filters\VerbFilter;

use yii\filters\AccessControl;

use backend\models\LoginForm;


use yii\helpers\Json;
use common\models\Property;



/**

 * Site controller

 */

class SiteController extends Controller

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

                        'actions' => ['login', 'error'],

                        'allow' => true,

                    ],

                    [

                        'actions' => ['logout'],

                        'allow' => true,

                        'roles' => ['@'],

                    ],

                    [

                        'actions' => ['index','refresh-msg-count','change-language'],

                        'allow' => true,

                        'roles' => [User::ROLE_ADMIN, User::ROLE_QUALITY_TEAM, User::ROLE_SUPERVISOR],

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
                'view' => '@backend/views/site/error_access_forbidden.php',
            ],
        ];

    }



    /**

     * Displays homepage.

     *

     * @return string

     */

    public function actionIndex()

    {
        if(Yii::$app->user->id != 1){
            $this->redirect(['properties/overview']);
        }

        $total_live_properties = Property::find()->where(['status'=> 2])->count();
        $total_today_posted_properties = Property::find()
                    ->where(['IN', 'property.status', [1, 2, 3]])
                    ->andWhere(['=', 'DATE_FORMAT(FROM_UNIXTIME(created_at), "%Y-%m-%d")', date("Y-m-d", time())])
                    ->count();
        $total_today_posted_properties_residential = Property::find()
                    ->where(['IN', 'property.status', [1, 2, 3]])
                    ->andWhere(['DATE_FORMAT(FROM_UNIXTIME(created_at), "%Y-%m-%d")'=> date("Y-m-d", time()), 'property_category' => 2])->count();
        $total_today_posted_properties_commercial = Property::find()
                    ->where(['IN', 'property.status', [1, 2, 3]])
                    ->andWhere(['DATE_FORMAT(FROM_UNIXTIME(created_at), "%Y-%m-%d")'=> date("Y-m-d", time()), 'property_category' => 1])->count();
        $current_time = time();
        $new_users = User::find()
                    ->join('LEFT JOIN','auth_assignment','auth_assignment.user_id = user.id')
                    ->where(['!=', 'auth_assignment.item_name', User::ROLE_ADMIN])
                    ->andWhere(['!=', 'status', User::STATUS_INCOMPLETE])
                    ->andWhere(['<', "TIMESTAMPDIFF(HOUR, DATE_FORMAT(FROM_UNIXTIME(user.created_at), '%Y-%m-%d H:i:s'), DATE_FORMAT(FROM_UNIXTIME($current_time), '%Y-%m-%d H:i:s'))", 72])
                    ->limit(7)
                    ->all();
        $new_posts = Property::find()
                    ->where(['IN', 'property.status', [1, 2, 3]])
                    ->andWhere(['<', "TIMESTAMPDIFF(HOUR, DATE_FORMAT(FROM_UNIXTIME(created_at), '%Y-%m-%d H:i:s'), DATE_FORMAT(FROM_UNIXTIME($current_time), '%Y-%m-%d H:i:s'))", 72])
                    ->limit(7)
                    ->all();
        return $this->render('index', [
            'total_live_properties' => $total_live_properties,
            'total_today_posted_properties' => $total_today_posted_properties,
            'total_today_posted_properties_residential' => $total_today_posted_properties_residential,
            'total_today_posted_properties_commercial' => $total_today_posted_properties_commercial,
            'new_users' => $new_users,
            'new_posts' => $new_posts,

        ]);

    }



    /**

     * Login action.

     *

     * @return string

     */

    public function actionLogin()

    {

        if (!Yii::$app->user->isGuest) {

            return $this->goHome();

        }

        $this->layout = "login";

        $model = new LoginForm();

        $model->scenario = 'admin_login';

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            //echo 1; die;

            if( Yii::$app->user->can(User::ROLE_ADMIN) || Yii::$app->user->can(User::ROLE_QUALITY_TEAM)
                || Yii::$app->user->can(User::ROLE_SUPERVISOR))

            {

                /*return $this->redirect(['device/index']);*/

                return $this->goBack();

            }

            else

            {

                Yii::$app->user->logout();

                /*return $this->redirect(['device/index']);*/

                return $this->goHome();

            }

        } else {
             //echo 2; die;
            return $this->render('login', [

                'model' => $model,

            ]);

        }

    }
    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
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

    public function actionError()
    {
        echo 1; die;
        $exception = Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            return $this->render('error', ['exception' => $exception]);
        }
    }


}

