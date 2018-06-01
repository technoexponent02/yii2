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

use common\models\User;
use common\models\UserTransaction;
use common\models\Message;
use yii\helpers\ArrayHelper;
use backend\models\PasswordResetRequestForm;
use yii\web\JsExpression;
/**
 * User controller
 */
class UserController extends Controller
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
                        'actions' => ['profile', 'request-password-reset', 'search-deatils'],
                        'allow' => true,
                        'roles' => [User::ROLE_WORKER, User::ROLE_CUSTOMER, User::ROLE_AGENCY],
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
     * Displays Profile.
     *
     * @return mixed
     */
    public function actionProfile()
    {
        $user_id = $this->user->id;
        $curr_month = date('m');
        $curr_year = date('Y');
        $current_month_start_date = '01-'.$curr_month.'-'.$curr_year;
        $last_month_start_date = date('d-m-Y', strtotime($current_month_start_date.' - 1 month'));
        $current_month_start_time = strtotime($current_month_start_date);
        $last_month_start_time = strtotime($last_month_start_date);
        $user = Yii::$app->user->identity;
        $last_year = time() - (86400*365);
        $current_year_credit_amount = UserTransaction::find()->where(['user_id' => $user_id, 'transaction_type' => 1, 'type' => UserTransaction::TYPE_CREDIT])->andWhere(['>=', 'created_at', $last_year])->orderBy(['created_at' => SORT_ASC])->sum('amount');
        
        $current_month_credit_amount = UserTransaction::find()->where(['user_id' => $user_id, 'transaction_type' => 1, 'type' => UserTransaction::TYPE_CREDIT])->andWhere(['>=', 'created_at', $current_month_start_time])->orderBy(['created_at' => SORT_ASC])->sum('amount');
        
        $last_month_credit_amount = UserTransaction::find()->where(['user_id' => $user_id, 'transaction_type' => 1, 'type' => UserTransaction::TYPE_CREDIT])->andWhere(['>=', 'created_at', $last_month_start_time])->orderBy(['created_at' => SORT_ASC])->sum('amount');

        $total_send_messages = Message::find()->where(['moderator_user_id' => $user_id, 'message_type' => 2])->orderBy(['id' => SORT_DESC])->count();
        $total_recieved_messages = UserTransaction::find()->where(['user_id' => $user_id, 'transaction_type' => 1, 'type' => UserTransaction::TYPE_CREDIT])->orderBy(['created_at' => SORT_ASC])->count();

        /***Activity graph related***/

        $current_month_credit_arr = UserTransaction::find()->where(['user_id' => $user_id, 'transaction_type' => 1, 'type' => UserTransaction::TYPE_CREDIT])->andWhere(['>=', 'created_at', $current_month_start_time])->orderBy(['created_at' => SORT_ASC])->all();

        $current_month_send_msg_arr = Message::find()->where(['moderator_user_id' => $user_id, 'message_type' => 2])->andWhere(['>=', 'created_at', $current_month_start_time])->orderBy(['created_at' => SORT_ASC])->all();

        $current_month_recieved_msg_arr = UserTransaction::find()->where(['user_id' => $user_id, 'transaction_type' => 1, 'type' => UserTransaction::TYPE_CREDIT])->andWhere(['>=', 'created_at', $current_month_start_time])->orderBy(['created_at' => SORT_ASC])->all();

        $no_of_days = ceil((time() - $current_month_start_time)/(24 * 60 * 60));
        //echo $no_of_days;

        $date_arr = [];

        $series_column_arr = [];
        $series_column_arr[0]['type'] = 'column';
        $series_column_arr[0]['name'] = 'Income';
        $series_column_arr[0]['data'] = [];
        $series_column_arr[1]['type'] = 'column';
        $series_column_arr[1]['name'] = 'Message sent';
        $series_column_arr[1]['data'] = [];
        $series_column_arr[2]['type'] = 'column';
        $series_column_arr[2]['name'] = 'Message Recieved';
        $series_column_arr[2]['data'] = [];
        $income_spline_line_arr = ['type' => 'spline', 'name' => 'Income Line', 'marker' => [
                                        'lineWidth' => 2,
                                        'lineColor' => new JsExpression('Highcharts.getOptions().colors[3]'),
                                        'fillColor' => 'white',
                                    ],
                                ];

        for($i = 0; $i < $no_of_days; $i++)
        {
            $date_time = $current_month_start_time + ($i * 24 * 60 * 60);
            $next_day_date_time = $current_month_start_time + (($i + 1) * 24 * 60 * 60);
            $date = date('m/d/Y', $date_time);
            $date_arr[$i] = $date;
            $series_column_arr[0]['data'][$i] = 0;
            //echo count($cureent_month_credit_arr);exit;
            $income = 0;
            foreach($current_month_credit_arr as $cr_crd)
            {
                if($cr_crd->created_at >= $date_time && $cr_crd->created_at < $next_day_date_time)
                {
                    $income = $income + $cr_crd->amount;
                }
            }
            $series_column_arr[0]['data'][$i] = $income;


            $sent_msg_count = 0;
            foreach($current_month_send_msg_arr as $cr_send_msg)
            {
                if($cr_send_msg->created_at >= $date_time && $cr_send_msg->created_at < $next_day_date_time)
                {
                    $sent_msg_count++;
                }
            }
            $series_column_arr[1]['data'][$i] = $sent_msg_count;

            $recieved_msg_count = 0;
            foreach($current_month_recieved_msg_arr as $cr_recv_msg)
            {
                if($cr_recv_msg->created_at >= $date_time && $cr_recv_msg->created_at < $next_day_date_time)
                {
                    $recieved_msg_count++;
                }
            }
            $series_column_arr[2]['data'][$i] = $recieved_msg_count;
        }

        $income_spline_line_arr['data'] = $series_column_arr[0]['data'];

        $chat_data = ['date_arr' => $date_arr, 'series_column_arr' => $series_column_arr, 'income_spline_line_arr' => $income_spline_line_arr, 'current_month_credit_amount', $current_month_credit_amount, 'current_month_send_msg_count' => count($current_month_send_msg_arr), 'current_month_recieved_msg_count' => count($current_month_recieved_msg_arr)];

        return $this->render('profile', [
                'model' => $this->user,
                'current_month_credit_amount' => $current_month_credit_amount,
                'last_month_credit_amount' => $last_month_credit_amount,
                'current_year_credit_amount' => $current_year_credit_amount,
                'total_send_messages' => $total_send_messages,
                'total_recieved_messages' => $total_recieved_messages,
                'chat_data' => $chat_data,
            ]);
    }

    public function actionRequestPasswordReset()
    {
        /* print_r(Yii::$app->request->post()); */
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            return $model->sendEmail();
        }else   {
            
            return $model->getErrors() ;
        };

    }

    public function actionSearchDeatils()
    {
        $req = Yii::$app->request->post();
        //print_r($req);
        //echo strtotime($req['start_dt']);
        //echo strtotime($req['end_dt']);
        $res_arr=array();
        if(!empty($req['start_dt']) && !empty($req['end_dt']))
        {
            $start_dt = strtotime($req['start_dt']);
            $end_dt = strtotime($req['end_dt']);            
            
            $user_id = $req['user_id'];
            
            $res_arr['total_send_messages'] = Message::find()->where(['moderator_user_id' => $user_id, 'message_type' => 2])->andWhere(['>=', 'created_at', $start_dt])->andWhere(['<=', 'created_at', $end_dt])->orderBy(['id' => SORT_DESC])->count();
            
            $res_arr['total_recieved_messages'] = UserTransaction::find()->where(['user_id' => $user_id, 'transaction_type' => 1, 'type' => UserTransaction::TYPE_CREDIT])->andWhere(['>=', 'created_at', $start_dt])->andWhere(['<=', 'created_at', $end_dt])->orderBy(['created_at' => SORT_ASC])->count();
            $res_arr['duration_credit_amount'] = UserTransaction::find()->where(['user_id' => $user_id, 'transaction_type' => 1, 'type' => UserTransaction::TYPE_CREDIT])->andWhere(['>=', 'created_at', $start_dt])->andWhere(['<=', 'created_at', $end_dt])->orderBy(['created_at' => SORT_ASC])->sum('amount');
        }
        return json_encode($res_arr);
    }

}
