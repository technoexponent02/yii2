<?php
namespace api\modules\v1\controllers;


//use frontend\models\PasswordResetRequestForm;

use yii\web\NotFoundHttpException;

//use common\models\UserAccountForm;

use yii\web\ForbiddenHttpException;

use api\modules\v1\Controller;
use Yii;
use common\models\User;
use common\models\UserDevice;
use common\models\UserSubscription;
use common\models\LoginForm;
use backend\models\UserSearch;
use api\modules\v1\models\ApiSignupForm;
use api\modules\v1\models\CkeckRegisterForm;
use common\models\Quiz;
use common\models\QuizQuestion;
use common\models\QuizAnswer;
use common\models\PlayedQuiz;
use common\models\PlayedQaQuiz;
use common\models\MaterialCategory;
use common\models\Material;
use common\models\MaterialAudio;
use yii\helpers\ArrayHelper;
use api\modules\v1\models\ApiQuestionForm;
use common\models\Friend;
use common\models\Setting;

use yii\filters\VerbFilter;
use yii\db\Expression;

class UserController extends Controller
{
	/**
	 * @inheritdoc
	 */
	public function verbs()
	{
		return [
			'login' => ['post'],
            'app-login' => ['post'],
			'account' => ['get','post'],
			'reset-password' => ['post'],
            'register' => ['post'],
            'quizes' => ['post'],
            'categories' => ['post'],
            'materials' => ['post'],
            'logout' => ['post'],
            'check-register' => ['post'],
            'add-quiz' => ['post'],
            'edit-quiz' => ['post'],
            'add-question' => ['post'],
            'edit-question' => ['post'],
            'delete-my-quiz' => ['post'],
            'delete-question' => ['post'],
            'take-quiz' => ['post'],
            'take-quiz-question' => ['post'],
            'take-quiz-result' => ['post'],
            'take-quiz-details-result' => ['post'],
            'send-friend-request' => ['post'],
            'friends' => ['post'],
            'accept-friend-request' => ['post'],
            'reject-friend-request' => ['post'],
            'delete-friend' => ['post'],
		];
	}
	
	public function authExcept()
	{
		return ['login', 'app-login', 'reset-password', 'register', 'quizes', 'categories', 'materials', 'logout', 'check-register', 'add-quiz', 'edit-quiz', 'add-question', 'edit-question', 'delete-my-quiz', 'delete-question', 'take-quiz', 'take-quiz-question', 'take-quiz-result', 'take-quiz-details-result', 'send-friend-request', 'friends', 'accept-friend-request', 'reject-friend-request', 'delete-friend'];
	}
    public function actionLogin()
    {
    	$model = new LoginForm();
        $model->scenario = 'app';
        $active_device_id = Yii::$app->getRequest()->getBodyParams('active_device_id');
        $user_exist = User::find()->where(['active_device_id' => $active_device_id])->one();
        if(count($user_exist) > 0)
        {
            if($user_exist->status == User::STATUS_DELETED)
            {
                return $this->addToJson(['errors' => ['active_device_id' => ['Device is in log out state.'],'is_registered' => 1]]);
            }
            else
            {
                if (Yii::$app->request->isPost && $model->load(['LoginForm'=>Yii::$app->getRequest()->getBodyParams()]) && $model->loginApi())
                {
                    $user = Yii::$app->user->identity;
                    return $this->addToJson(['user' => $user,'subscription' => $user->subscription,'subcribe_agency' => $user->subscription->agency,'subcribe_exam' => $user->subscription->exam]);
                }
                elseif($model->hasErrors())
                {
                    return $this->addToJson(['errors' => ['active_device_id' => ['Device is not registered.'],'is_registered' => 0]]);
                }
                else
                {
                    return $this->addToJson(['error' => 'Internal Server error!']);
                }
            }
        }
        else
        {
            return $this->addToJson(['errors' => ['active_device_id' => ['Device is not registered.'],'is_registered' => 0]]);
        }
    }
    public function actionAppLogin()
    {
        $model = new LoginForm();
        $model->scenario = 'app';
        $app_input = Yii::$app->getRequest()->getBodyParams();
        if(isset($app_input['mobile_no']) && $app_input['mobile_no'] != '' && isset($app_input['active_device_id']) && $app_input['active_device_id'] != '')
        {
            $mobile_no = $app_input['mobile_no'];
            $mobile_no = str_replace('(', '', $mobile_no);
            $mobile_no = str_replace(')', '', $mobile_no);
            $mobile_no = str_replace(' ', '', $mobile_no);
            $mobile_no = str_replace('-', '', $mobile_no);
            $active_device_id = $app_input['active_device_id'];
            $user_exist = User::find()->where(['mobile_no' => $mobile_no, 'active_device_id' => $active_device_id])->one();
            if(count($user_exist) > 0)
            {
                if($user_exist->status == User::STATUS_DELETED)
                {
                    $user_exist->status = User::STATUS_ACTIVE;
                    $user_exist->save();

                    if (Yii::$app->request->isPost && $model->load(['LoginForm'=> ['active_device_id' => $active_device_id]]) && $model->loginApi())
                    {
                        $user = Yii::$app->user->identity;
                        return $this->addToJson(['user' => $user,'subscription' => $user->subscription,'subcribe_agency' => $user->subscription->agency,'subcribe_exam' => $user->subscription->exam]);
                    }
                    elseif($model->hasErrors())
                    {
                        return $this->addToJson(['errors' => ['mobile_no' => ['Device is not registered.'],'is_registered' => 0]]);
                    }
                    else
                    {
                        return $this->addToJson(['error' => 'Internal Server error!']);
                    }
                }
                else
                {
                    return $this->addToJson(['errors' => ['mobile_no' => ['You are in Login status.']]]);
                }
            }
            else
            {
                return $this->addToJson(['errors' => ['mobile_no' => ['Invalid Mobile Number.']]]);
            }
        }
        else
        {
            if((!isset($app_input['mobile_no']) || $app_input['mobile_no'] == '') && (!isset($app_input['active_device_id']) || $app_input['active_device_id'] == '')){
                return $this->addToJson(['errors' => ['mobile_no' => ['Mobile Number can not be blank.'],'active_device_id' => ['Device ID can not be blank.']]]);
            }
            elseif(!isset($app_input['mobile_no']) || $app_input['mobile_no'] == ''){
                return $this->addToJson(['errors' => ['mobile_no' => ['Mobile Number can not be blank.']]]);
            }
            else
            {
                return $this->addToJson(['errors' => ['active_device_id' => ['Device ID can not be blank.']]]);
            }
        }
        
    }
    public function actionLogout()
    {
        $model = new LoginForm();
        $model->scenario = 'app';
        if (Yii::$app->request->isPost && $model->load(['LoginForm'=>Yii::$app->getRequest()->getBodyParams()]) && $model->loginApi())
        {
            $user = Yii::$app->user->identity;
            $user->status = User::STATUS_DELETED;
            $user->save();
            Yii::$app->user->logout();
            return $this->addToJson(['info' => 'You have been successfully logout from the system.']);
        }
        elseif($model->hasErrors())
        {
            return $this->sendErrors($model->getErrors());
        }
        else
        {
            return $this->addToJson(['error' => 'Internal Server error!']);
        }
    }

    public function actionCheckRegister()
    {
        $model = new CkeckRegisterForm();
        if (Yii::$app->request->isPost && $model->load(['CkeckRegisterForm'=>Yii::$app->getRequest()->getBodyParams()]) && $model->validate())
        {
            $user = $model->getUserByUniqueCode();
            $userDevice = UserDevice::find()->where(['user_unique_code' => $model->user_unique_code, 'device_id' => $model->device_id, 'device_type' => $model->device_type])->one();
            if(count($userDevice) > 0)
            {
                return $this->addToJson(['hasPreviousDevice' => 1]); 
            }
            else
            {
                return $this->addToJson(['hasPreviousDevice' => 0]); 
            }
        }
        else
        {
            $this->addToJson(['hasPreviousDevice' => 2]);
            return $this->sendErrors($model->getErrors());
        }
    }

    public function actionRegister()
    {
        $model = new ApiSignupForm();
        if (Yii::$app->request->isPost && $model->load(['ApiSignupForm'=>Yii::$app->getRequest()->getBodyParams()]) && $model->registerApi())
        {
            $user = Yii::$app->user->identity;
            $user->active_device_id = $model->device_id;
            $user->active_device_type = $model->device_type;
            $user->active_device_os_version = $model->device_os_version;
            $user->save();
            $userDevice = UserDevice::find()->where(['user_unique_code' => $model->user_unique_code, 'device_id' => $model->device_id])->one();
            if(count($userDevice) > 0)
            {   
                $userDevice->device_status = UserDevice::STATUS_ACTIVE;
                $userDevice->save();
            }
            else
            {
                $userDevice = new UserDevice();
                $userDevice->user_id = $user->id;
                $userDevice->user_unique_code = $user->user_unique_code;
                $userDevice->device_id = $model->device_id;
                $userDevice->device_type = $model->device_type;
                $userDevice->device_os_version = $model->device_os_version;
                $userDevice->save();
            }

            return $this->addToJson(['user' => $user,'subscription' => $user->subscription,'subcribe_agency' => $user->subscription->agency,'subcribe_exam' => $user->subscription->exam]);
        }
        else
        {
            return $this->sendErrors($model->getErrors());
        }
    }

    public function actionQuizes()
    {
        $model = new LoginForm();
        $model->scenario = 'app';
        if (Yii::$app->request->isPost && $model->load(['LoginForm'=>Yii::$app->getRequest()->getBodyParams()]) && $model->loginApi())
        {
            $user = Yii::$app->user->identity;
            $included_quizes_obj = Quiz::find()->where(['agency_id' => $user->subscription->agency_id, 'exam_id' => $user->subscription->exam_id, 'user_id' => 1, 'quiz_status' => Quiz::STATUS_ACTIVE])->orderBy(['created_at' => SORT_DESC])->all();
            $included_quizes = [];
            if(count($included_quizes_obj) > 0)
            {
                $included_quizes = ArrayHelper::toArray($included_quizes_obj);
                $i = 0;
                foreach($included_quizes_obj as $in_q)
                {
                    if(count($in_q->questions) > 0)
                    {
                        $included_quizes[$i]['questions'] = ArrayHelper::toArray($in_q->questions);
                        $j = 0;
                        foreach($in_q->questions as $ques)
                        {
                            $included_quizes[$i]['questions'][$j]['answers'] =  ArrayHelper::toArray($ques->answers);
                            $j++;
                        }

                    }
                    $i++;
                }
            }

            $my_quizes = [];
            $my_quizes_obj = Quiz::find()->where(['agency_id' => $user->subscription->agency_id, 'exam_id' => $user->subscription->exam_id, 'user_id' => $user->id, 'quiz_status' => Quiz::STATUS_ACTIVE])->orderBy(['created_at' => SORT_DESC])->all();
            if(count($my_quizes_obj) > 0)
            {
                $my_quizes = ArrayHelper::toArray($my_quizes_obj);
                $k = 0;
                foreach($my_quizes_obj as $my_q)
                {
                    if(count($my_q->questions) > 0)
                    {
                        $my_quizes[$k]['questions'] = ArrayHelper::toArray($my_q->questions);
                        $m = 0;
                        foreach($my_q->questions as $ques)
                        {
                            $my_quizes[$k]['questions'][$m]['answers'] =  ArrayHelper::toArray($ques->answers);
                            $m++;
                        }

                    }
                    $k++;
                }
            }

            $share_quizes = [];

            $friend_obj = Friend::find()
                                ->where(['from_user_id' => $user->id, 'friendship_status' => Friend::STATUS_ACCEPT])
                                ->orWhere(['to_user_id' => $user->id, 'friendship_status' => Friend::STATUS_ACCEPT])
                                ->orderBy(['send_time' => SORT_DESC])
                                ->all();

            if(count($friend_obj) > 0)
            {
                foreach ($friend_obj as $frnd)
                {
                    if($frnd->from_user_id == $user->id)
                    {
                        $friend_id = $frnd->to_user_id;
                    }
                    else
                    {
                        $friend_id = $frnd->from_user_id;
                    }

                    $friend_quizes_obj = Quiz::find()->where(['user_id' => $friend_id, 'quiz_status' => Quiz::STATUS_ACTIVE])->orderBy(['created_at' => SORT_DESC])->all();
                    if(count($friend_quizes_obj) > 0)
                    {
                        $friend_quizes = ArrayHelper::toArray($friend_quizes_obj);
                        $x = 0;
                        foreach($friend_quizes_obj as $frnd_q)
                        {
                            if(count($frnd_q->questions) > 0)
                            {
                                $friend_quizes[$x]['questions'] = ArrayHelper::toArray($frnd_q->questions);
                                $y = 0;
                                foreach($frnd_q->questions as $ques)
                                {
                                    $friend_quizes[$x]['questions'][$y]['answers'] =  ArrayHelper::toArray($ques->answers);
                                    $y++;
                                }

                            }
                            $x++;
                        }
                        $share_quizes = ArrayHelper::merge($share_quizes, $friend_quizes);
                    }
                }
            }

            $quizes = ['included_quizes' => $included_quizes, 'my_quizes' => $my_quizes, 'share_quizes' => $share_quizes];
            return $this->addToJson(['user' => $user,'subscription' => $user->subscription,'subcribe_agency' => $user->subscription->agency,'subcribe_exam' => $user->subscription->exam, 'quizes' => $quizes]);
        }
        elseif($model->hasErrors())
        {
            return $this->sendErrors($model->getErrors());
        }
        else
        {
            return $this->addToJson(['error' => 'Internal Server error!']);
        }
    }
    public function actionCategories()
    {
        $model = new LoginForm();
        $model->scenario = 'app';
        if (Yii::$app->request->isPost && $model->load(['LoginForm'=>Yii::$app->getRequest()->getBodyParams()]) && $model->loginApi())
        {
            $user = Yii::$app->user->identity;
            $categories_obj = MaterialCategory::find()->where(['cat_status' => MaterialCategory::STATUS_ACTIVE, 'agency_id' => $user->subscription->agency_id, 'exam_id' => $user->subscription->exam_id])->orderBy(['cat_order' => SORT_ASC])->all();
            return $this->addToJson(['user' => $user,'subscription' => $user->subscription,'subcribe_agency' => $user->subscription->agency,'subcribe_exam' => $user->subscription->exam, 'categories' => $categories_obj]);
        }
        elseif($model->hasErrors())
        {
            return $this->sendErrors($model->getErrors());
        }
        else
        {
            return $this->addToJson(['error' => 'Internal Server error!']);
        }
    }
    public function actionMaterials()
    {
        $model = new LoginForm();
        $model->scenario = 'app';
        $LoginForm_data = ['LoginForm' => ['active_device_id' => Yii::$app->getRequest()->getBodyParams('active_device_id')]];
        if (Yii::$app->request->isPost && $model->load($LoginForm_data) && $model->loginApi())
        {
            $user = Yii::$app->user->identity;
            $input = Yii::$app->getRequest()->getBodyParams();
            unset($input['active_device_id']);
            if(isset($input['category_id']) && $input['category_id'] != '' && isset($input['matetial_type']) && $input['matetial_type'] != '')
            {
                $materials_obj = Material::find()->where(['material_status' => Material::STATUS_ACTIVE, 'category_id' => $input['category_id'], 'matetial_type' => $input['matetial_type']])->orderBy(['mat_order' => SORT_ASC])->all();
                $materials = [];
                if(count($materials_obj) > 0)
                {
                    $materials = ArrayHelper::toArray($materials_obj);
                    $i = 0;
                    foreach($materials_obj as $mat)
                    {
                        if($mat->matetial_type == 2)
                        {
                            $materials[$i]['audios'] = ArrayHelper::toArray($mat->materialAudios);
                        }
                        $i++;
                    }
                }
                return $this->addToJson(['user' => $user,'subscription' => $user->subscription,'subcribe_agency' => $user->subscription->agency,'subcribe_exam' => $user->subscription->exam, 'materials' => $materials]);
            }
            else
            {
                if(!isset($input['category_id']) || $input['category_id'] == '')
                {
                    return $this->addToJson(['errors' => ['category_id' => ['Category ID cannot be blank.']]]);
                }
                else
                {
                    return $this->addToJson(['errors' => ['matetial_type' => ['Material Type cannot be blank.']]]);
                }
            }
            
        }
        elseif($model->hasErrors())
        {
            return $this->sendErrors($model->getErrors());
        }
        else
        {
            return $this->addToJson(['error' => 'Internal Server error!']);
        }
    }

    public function actionAddQuiz()
    {
        $model = new LoginForm();
        $model->scenario = 'app';
        $LoginForm_data = ['LoginForm' => ['active_device_id' => Yii::$app->getRequest()->getBodyParams('active_device_id')]];
        if(Yii::$app->request->isPost && $model->load($LoginForm_data) && $model->loginApi())
        {
            $user = Yii::$app->user->identity;
            $input = Yii::$app->getRequest()->getBodyParams();
            unset($input['active_device_id']);
            $modelQuiz = new Quiz();
            $QuizForm_data = ['Quiz' => [
                                    'user_id' => $user->id,
                                    'agency_id' => $user->subscription->agency->id,
                                    'exam_id' => $user->subscription->exam->id,
                                    'quiz_name' => isset($input['quiz_name'])?$input['quiz_name']:'',
                                    'quiz_type' => 2,
                                ]
                            ];
            if($modelQuiz->load($QuizForm_data) && $modelQuiz->validate())
            {
                $modelQuiz->save();
                return $this->addToJson(['info' => 'Quiz has been added successfully', 'quiz' => $modelQuiz, 'user' => $user,'subscription' => $user->subscription,'subcribe_agency' => $user->subscription->agency,'subcribe_exam' => $user->subscription->exam]);
            }
            else
            {
                return $this->sendErrors($modelQuiz->getErrors());
            }

        }
        elseif($model->hasErrors())
        {
            return $this->sendErrors($model->getErrors());
        }
        else
        {
            return $this->addToJson(['error' => 'Internal Server error!']);
        }
    }

    public function actionEditQuiz()
    {
        $model = new LoginForm();
        $model->scenario = 'app';
        $LoginForm_data = ['LoginForm' => ['active_device_id' => Yii::$app->getRequest()->getBodyParams('active_device_id')]];
        if(Yii::$app->request->isPost && $model->load($LoginForm_data) && $model->loginApi())
        {
            $user = Yii::$app->user->identity;
            $input = Yii::$app->getRequest()->getBodyParams();
            unset($input['active_device_id']);
            if(isset($input['quiz_id']) && $input['quiz_id'] != '')
            {
                $quiz_id = $input['quiz_id'];
                $modelQuiz = Quiz::findOne($quiz_id);
                if(count($modelQuiz) > 0)
                {
                    if($modelQuiz->user_id == $user->id)
                    {
                        $QuizForm_data = ['Quiz' => [
                                            'quiz_name' => isset($input['quiz_name'])?$input['quiz_name']:'',
                                        ]
                                    ];
                        if($modelQuiz->load($QuizForm_data) && $modelQuiz->validate())
                        {
                            $modelQuiz->save();
                            return $this->addToJson(['info' => 'Quiz has been updated successfully', 'quiz' => $modelQuiz, 'user' => $user,'subscription' => $user->subscription,'subcribe_agency' => $user->subscription->agency,'subcribe_exam' => $user->subscription->exam]);
                        }
                        else
                        {
                            return $this->sendErrors($modelQuiz->getErrors());
                        }
                    }
                    else
                    {
                        return $this->addToJson(['error' => 'You are not authorized to edit this quiz!']);
                    }
                }
                else
                {
                    return $this->addToJson(['errors' => ['quiz_id' => ['Invalid Quiz ID!']]]);
                }
            }
            else
            {
                return $this->addToJson(['errors' => ['quiz_id' => ['Quiz ID cannot be blank.']]]);
            }
        }
        elseif($model->hasErrors())
        {
            return $this->sendErrors($model->getErrors());
        }
        else
        {
            return $this->addToJson(['error' => 'Internal Server error!']);
        }
    }

    public function actionAddQuestion()
    {
        $model = new LoginForm();
        $model->scenario = 'app';
        $LoginForm_data = ['LoginForm' => ['active_device_id' => Yii::$app->getRequest()->getBodyParams('active_device_id')]];
        if(Yii::$app->request->isPost && $model->load($LoginForm_data) && $model->loginApi())
        {
            $user = Yii::$app->user->identity;
            $input = Yii::$app->getRequest()->getBodyParams();
            unset($input['active_device_id']);
            $modelApiQuestion = new ApiQuestionForm();
            $modelApiQuestion->scenario = 'create';
            if($modelApiQuestion->load(['ApiQuestionForm' => $input]) && $modelApiQuestion->validate())
            {
                $question = new QuizQuestion();
                $question->quiz_id = $modelApiQuestion->quiz_id;
                $question->quiz_question = $modelApiQuestion->question;
                $question->save();
                for($i = 1;$i <= 4; $i++)
                {
                    $answer_no = 'answer'.$i;
                    $answer = new QuizAnswer();
                    $answer->quiz_id = $modelApiQuestion->quiz_id;
                    $answer->question_id = $question->id;
                    $answer->answer = $modelApiQuestion->$answer_no;
                    $answer->save();
                    if($i == $modelApiQuestion->currect_answer)
                    {
                        $answer->is_correct = QuizAnswer::STATUS_CORRECT;
                        $answer->save();
                        $question->correct_answer_id = $answer->id;
                        $question->save();
                    }
                }
                $question_arr = ArrayHelper::toArray($question);
                $question_arr['answers'] = ArrayHelper::toArray($question->answers);
                 return $this->addToJson(['info' => 'Question has been added successfully.', 'question' => $question_arr, 'user' => $user,'subscription' => $user->subscription,'subcribe_agency' => $user->subscription->agency,'subcribe_exam' => $user->subscription->exam]);
            }
            else
            {
                return $this->sendErrors($modelApiQuestion->getErrors());
            }

        }
        elseif($model->hasErrors())
        {
            return $this->sendErrors($model->getErrors());
        }
        else
        {
            return $this->addToJson(['error' => 'Internal Server error!']);
        }
    }

    public function actionEditQuestion()
    {
        $model = new LoginForm();
        $model->scenario = 'app';
        $LoginForm_data = ['LoginForm' => ['active_device_id' => Yii::$app->getRequest()->getBodyParams('active_device_id')]];
        if(Yii::$app->request->isPost && $model->load($LoginForm_data) && $model->loginApi())
        {
            $user = Yii::$app->user->identity;
            $input = Yii::$app->getRequest()->getBodyParams();
            unset($input['active_device_id']);
            $modelApiQuestion = new ApiQuestionForm();
            $modelApiQuestion->scenario = 'update';
            if($modelApiQuestion->load(['ApiQuestionForm' => $input]) && $modelApiQuestion->validate())
            {
                $question = QuizQuestion::findOne($modelApiQuestion->question_id);
                $question->quiz_question = $modelApiQuestion->question;
                $question->save();
                QuizAnswer::deleteAll('question_id=:question_id',['question_id' => $modelApiQuestion->question_id]);
                for($i = 1;$i <= 4; $i++)
                {
                    $answer_no = 'answer'.$i;
                    $answer = new QuizAnswer();
                    $answer->quiz_id = $question->quiz_id;
                    $answer->question_id = $question->id;
                    $answer->answer = $modelApiQuestion->$answer_no;
                    $answer->save();
                    if($i == $modelApiQuestion->currect_answer)
                    {
                        $answer->is_correct = QuizAnswer::STATUS_CORRECT;
                        $answer->save();
                        $question->correct_answer_id = $answer->id;
                        $question->save();
                    }
                }
                $question_arr = ArrayHelper::toArray($question);
                $question_arr['answers'] = ArrayHelper::toArray($question->answers);
                 return $this->addToJson(['info' => 'Question has been updated successfully.', 'question' => $question_arr, 'user' => $user,'subscription' => $user->subscription,'subcribe_agency' => $user->subscription->agency,'subcribe_exam' => $user->subscription->exam]);
            }
            else
            {
                return $this->sendErrors($modelApiQuestion->getErrors());
            }

        }
        elseif($model->hasErrors())
        {
            return $this->sendErrors($model->getErrors());
        }
        else
        {
            return $this->addToJson(['error' => 'Internal Server error!']);
        }
    }

    public function actionDeleteMyQuiz()
    {
        $model = new LoginForm();
        $model->scenario = 'app';
        $LoginForm_data = ['LoginForm' => ['active_device_id' => Yii::$app->getRequest()->getBodyParams('active_device_id')]];
        if(Yii::$app->request->isPost && $model->load($LoginForm_data) && $model->loginApi())
        {
            $user = Yii::$app->user->identity;
            $input = Yii::$app->getRequest()->getBodyParams();
            $quiz_id = $input['quiz_id'];
            $quiz = Quiz::findOne($quiz_id);
            if(count($quiz) > 0)
            {
                if($quiz->user_id == $user->id)
                {
                    QuizQuestion::deleteAll('quiz_id=:quiz_id',['quiz_id' => $quiz_id]);
                    QuizAnswer::deleteAll('quiz_id=:quiz_id',['quiz_id' => $quiz_id]);
                    $quiz->delete();
                    return $this->addToJson(['info' => 'This quiz has been deleted successfully.']);
                }
                else
                {
                    return $this->addToJson(['error' => 'You are not authorized to delete this quiz!']);
                }
            }
            else
            {
                return $this->addToJson(['errors' => ['quiz_id' => ['Invalid Quiz ID!']]]);
            }
        }
        elseif($model->hasErrors())
        {
            return $this->sendErrors($model->getErrors());
        }
        else
        {
            return $this->addToJson(['error' => 'Internal Server error!']);
        }
    }
    public function actionDeleteQuestion()
    {
        $model = new LoginForm();
        $model->scenario = 'app';
        $LoginForm_data = ['LoginForm' => ['active_device_id' => Yii::$app->getRequest()->getBodyParams('active_device_id')]];
        if(Yii::$app->request->isPost && $model->load($LoginForm_data) && $model->loginApi())
        {
            $user = Yii::$app->user->identity;
            $input = Yii::$app->getRequest()->getBodyParams();
            if(isset($input['question_id']) && $input['question_id'] != '')
            {
                $question_id = $input['question_id'];
                $question = QuizQuestion::findOne($question_id);
                if(count($question) > 0)
                {
                    QuizAnswer::deleteAll('question_id=:question_id',['question_id' => $question_id]);
                    $question->delete();
                    return $this->addToJson(['info' => 'This question has been deleted successfully.']);
                }
                else
                {
                    return $this->addToJson(['errors' => ['question_id' => ['Invalid Question ID!']]]);
                }
            }
            else
            {
                return $this->addToJson(['errors' => ['question_id' => ['Question ID cannot be blank.']]]);
            }
        }
        elseif($model->hasErrors())
        {
            return $this->sendErrors($model->getErrors());
        }
        else
        {
            return $this->addToJson(['error' => 'Internal Server error!']);
        }
    }

    public function actionTakeQuiz()
    {
        $model = new LoginForm();
        $model->scenario = 'app';
        $LoginForm_data = ['LoginForm' => ['active_device_id' => Yii::$app->getRequest()->getBodyParams('active_device_id')]];
        if(Yii::$app->request->isPost && $model->load($LoginForm_data) && $model->loginApi())
        {
            $user = Yii::$app->user->identity;
            $input = Yii::$app->getRequest()->getBodyParams();
            if(isset($input['quiz_id']) && $input['quiz_id'] != '')
            {
                $quiz_id = $input['quiz_id'];
                $quiz = Quiz::findOne($quiz_id);
                if(count($quiz) > 0)
                {
                    if(count($quiz->questions) > 0)
                    {
                        $PlayedQuiz = new PlayedQuiz();
                        $PlayedQuiz->quiz_id = $quiz->id;
                        $PlayedQuiz->user_id = $user->id;
                        $PlayedQuiz->play_start_time = time();
                        $PlayedQuiz->total_question = count($quiz->questions);
                        $PlayedQuiz->save();

                        $question_arr = [];
                        $question = QuizQuestion::find()->where(['quiz_id' => $quiz->id])->orderBy(new Expression('rand()'))->one();
                        $question_arr = ArrayHelper::toArray($question);
                        $question_arr['answers'] = ArrayHelper::toArray($question->answers);
                        $hidden_data = [
                                        'quiz_id' => $quiz->id,
                                        'playedQuiz_id' => $PlayedQuiz->id,
                                        'used_question_ids' => $question->id,
                                        'is_finish' => 0,
                                    ];
                        return $this->addToJson(['hidden_data' => $hidden_data, 'question' => $question_arr]);
                    }
                    else
                    {
                        if($quiz->user_id != $user->id){
                            return $this->addToJson(['errors' => ['quiz_id' => ['This Quiz has no question!']]]);
                        }
                        else{
                            return $this->addToJson(['errors' => ['quiz_id' => ['This Quiz has no question! Use the "+" icon to add one!']]]);
                        }
                    }
                }
                else
                {
                    return $this->addToJson(['errors' => ['quiz_id' => ['Invalid Quiz ID!']]]);
                }
            }
            else
            {
                return $this->addToJson(['errors' => ['quiz_id' => ['Quiz ID cannot be blank.']]]);
            }
        }
        elseif($model->hasErrors())
        {
            return $this->sendErrors($model->getErrors());
        }
        else
        {
            return $this->addToJson(['error' => 'Internal Server error!']);
        }
    }

    public function actionTakeQuizQuestion()
    {
        $model = new LoginForm();
        $model->scenario = 'app';
        $LoginForm_data = ['LoginForm' => ['active_device_id' => Yii::$app->getRequest()->getBodyParams('active_device_id')]];
        if(Yii::$app->request->isPost && $model->load($LoginForm_data) && $model->loginApi())
        {
            $user = Yii::$app->user->identity;
            $input = Yii::$app->getRequest()->getBodyParams();
            if(isset($input['quiz_id']) && $input['quiz_id'] != '' && isset($input['playedQuiz_id']) && $input['playedQuiz_id'] != '' && isset($input['used_question_ids']) && $input['used_question_ids'] != '')
            {
                $quiz_id = $input['quiz_id'];
                $quiz = Quiz::findOne($quiz_id);
                if(count($quiz) > 0)
                {
                    $PlayedQuiz = PlayedQuiz::findOne($input['playedQuiz_id']);
                    $PlayedQaQuiz = new PlayedQaQuiz();
                    $PlayedQaQuiz->played_quiz_id = $PlayedQuiz->id;
                    $PlayedQaQuiz->quiz_id = $quiz->id;
                    $PlayedQaQuiz->question_id = $input['question_id'];
                    if($input['is_skip'] == PlayedQaQuiz::SKIP_YES)
                    {
                        $PlayedQaQuiz->answer_id = 0;
                        $PlayedQaQuiz->is_currect = PlayedQaQuiz::STATUS_INCURRECT;
                        $PlayedQaQuiz->is_skip = PlayedQaQuiz::SKIP_YES;
                        $PlayedQuiz->skip_question = $PlayedQuiz->skip_question + 1;
                        $PlayedQaQuiz->save();
                        $PlayedQuiz->save();
                    }
                    else
                    {
                        $played_question = QuizQuestion::findOne($PlayedQaQuiz->question_id);
                        $PlayedQaQuiz->answer_id =  $input['answer_id'];
                        if($PlayedQaQuiz->answer_id == $played_question->correct_answer_id)
                        {
                            $PlayedQaQuiz->is_currect = PlayedQaQuiz::STATUS_CURRECT;
                            $PlayedQuiz->currect_answer = $PlayedQuiz->currect_answer + 1;
                        }
                        else
                        {
                            $PlayedQaQuiz->is_currect = PlayedQaQuiz::STATUS_INCURRECT;
                        }
                        $PlayedQaQuiz->is_skip = PlayedQaQuiz::SKIP_NO;
                        $PlayedQaQuiz->save();
                        $PlayedQuiz->save();
                    }

                    $used_question_ids = explode(',', $input['used_question_ids']);
                    if(count($quiz->questions) > count($used_question_ids))
                    {
                        $question_arr = [];
                        $question = QuizQuestion::find()->where(['not in', 'id', $used_question_ids])->andWhere(['quiz_id' => $quiz->id])->orderBy(new Expression('rand()'))->one();
                        $question_arr = ArrayHelper::toArray($question);
                        $question_arr['answers'] = ArrayHelper::toArray($question->answers);
                        $hidden_data = [
                                        'quiz_id' => $quiz->id,
                                        'playedQuiz_id' => $PlayedQuiz->id,
                                        'used_question_ids' => $input['used_question_ids'].','.$question->id,
                                        'is_finish' => 0,
                                    ];
                        return $this->addToJson(['hidden_data' => $hidden_data, 'question' => $question_arr]);

                    }
                    else
                    {
                        $PlayedQuiz->is_complete = PlayedQuiz::STATUS_COMPLETE;
                        $PlayedQuiz->currect_percentage = round((($PlayedQuiz->currect_answer/$PlayedQuiz->total_question) * 100),1);
                        $PlayedQuiz->wrong_percentage = (100 - $PlayedQuiz->currect_percentage);
                        $PlayedQuiz->play_end_time = time();
                        $PlayedQuiz->save();
                        $hidden_data = [
                                        'quiz_id' => $quiz->id,
                                        'playedQuiz_id' => $PlayedQuiz->id,
                                        'is_finish' => 1,
                                    ];
                        return $this->addToJson(['hidden_data' => $hidden_data]);
                    }
                }
                else
                {
                    return $this->addToJson(['errors' => ['quiz_id' => ['Invalid Quiz ID!']]]);
                }
            }
            else
            {
                return $this->addToJson(['errors' => ['quiz_id' => ['Invalid Quiz.']]]);
            }
        }
        elseif($model->hasErrors())
        {
            return $this->sendErrors($model->getErrors());
        }
        else
        {
            return $this->addToJson(['error' => 'Internal Server error!']);
        }
    }

    public function actionTakeQuizResult()
    {
        $model = new LoginForm();
        $model->scenario = 'app';
        $LoginForm_data = ['LoginForm' => ['active_device_id' => Yii::$app->getRequest()->getBodyParams('active_device_id')]];
        if(Yii::$app->request->isPost && $model->load($LoginForm_data) && $model->loginApi())
        {
            $user = Yii::$app->user->identity;
            $input = Yii::$app->getRequest()->getBodyParams();
            if(isset($input['playedQuiz_id']) && $input['playedQuiz_id'] != '')
            {
                $quiz_result = $this->getQuizResult($input['playedQuiz_id']);
                return $quiz_result;
            }
            else
            {
                return $this->addToJson(['error' => 'No quiz found!']);
            }
        }
        elseif($model->hasErrors())
        {
            return $this->sendErrors($model->getErrors());
        }
        else
        {
            return $this->addToJson(['error' => 'Internal Server error!']);
        }
    }

    private function getQuizResult($take_quiz_id)/***NEED TO MODIFY*/
    {
        $PlayedQuiz = PlayedQuiz::findOne($take_quiz_id);
        if(count($PlayedQuiz) > 0)
        {
            $played_quiz_result = [];
            $played_quiz_result = ArrayHelper::toArray($PlayedQuiz);
            $played_quiz_result['quiz'] = ArrayHelper::toArray($PlayedQuiz->quiz);
            return $this->addToJson(['played_quiz_result' => $played_quiz_result]);
        }
        else
        {
            return $this->addToJson(['error' => 'No played quiz found!']);
        }
    }

    public function actionTakeQuizDetailsResult()
    {
        $model = new LoginForm();
        $model->scenario = 'app';
        $LoginForm_data = ['LoginForm' => ['active_device_id' => Yii::$app->getRequest()->getBodyParams('active_device_id')]];
        if(Yii::$app->request->isPost && $model->load($LoginForm_data) && $model->loginApi())
        {
            $user = Yii::$app->user->identity;
            $input = Yii::$app->getRequest()->getBodyParams();
            if(isset($input['playedQuiz_id']) && $input['playedQuiz_id'] != '')
            {
                $PlayedQuiz_obj = PlayedQuiz::findOne($input['playedQuiz_id']);
                $PlayedQuiz_qa_obj = PlayedQaQuiz::find()->where(['played_quiz_id' => $PlayedQuiz_obj->id])->all();
                $PlayedQuiz = array();
                $PlayedQuiz = ArrayHelper::toArray($PlayedQuiz_obj);
                $PlayedQuiz['PlayedQuiz_qa'] = ArrayHelper::toArray($PlayedQuiz_qa_obj);
                $i = 0;
                foreach($PlayedQuiz_qa_obj as $pl_qa)
                {
                   $PlayedQuiz['PlayedQuiz_qa'][$i]['question'] = $pl_qa->question;
                   $PlayedQuiz['PlayedQuiz_qa'][$i]['given_answer'] = $pl_qa->givenAnswer;
                   $PlayedQuiz['PlayedQuiz_qa'][$i]['correct_answer'] = $pl_qa->question->correctAnswer;
                   $i++;
                }
                return $this->addToJson(['PlayedQuiz' => $PlayedQuiz]);
            }
            else
            {
                return $this->addToJson(['error' => 'No quiz found!']);
            }
        }
        elseif($model->hasErrors())
        {
            return $this->sendErrors($model->getErrors());
        }
        else
        {
            return $this->addToJson(['error' => 'Internal Server error!']);
        }
    }

    public function actionSendFriendRequest()
    {
        $model = new LoginForm();
        $model->scenario = 'app';
        $LoginForm_data = ['LoginForm' => ['active_device_id' => Yii::$app->getRequest()->getBodyParams('active_device_id')]];
        if(Yii::$app->request->isPost && $model->load($LoginForm_data) && $model->loginApi())
        {
            $user = Yii::$app->user->identity;
            $input = Yii::$app->getRequest()->getBodyParams();
            if(isset($input['to_mobile_no']) && $input['to_mobile_no'] != '')
            {
                $input['to_mobile_no'] = str_replace('(', '', $input['to_mobile_no']);
                $input['to_mobile_no'] = str_replace(')', '', $input['to_mobile_no']);
                $input['to_mobile_no'] = str_replace(' ', '', $input['to_mobile_no']);
                $input['to_mobile_no'] = str_replace('-', '', $input['to_mobile_no']);
                if($input['to_mobile_no'] != $user->mobile_no)
                {
                    $already_friend = Friend::find()
                                        ->where(['from_mobile_no' => $user->mobile_no, 'to_mobile_no' => $input['to_mobile_no']])
                                        ->orWhere(['from_mobile_no' => $input['to_mobile_no'], 'to_mobile_no' => $user->mobile_no])
                                        ->one();
                    if(count($already_friend) > 0)
                    {
                        if($already_friend->friendship_status == 2)
                        {
                            return $this->addToJson(['errors' => ['to_mobile_no' => ['This Mobile No is already in your friend list.']]]);
                        }
                        elseif($already_friend->friendship_status == 1)
                        {
                            return $this->addToJson(['errors' => ['to_mobile_no' => ['You have already send a friend request to this mobile no.']]]);
                        }
                    }
                    else
                    {
                        $mobile_no = '+1'.$input['to_mobile_no'];
                        require_once(Yii::getAlias('@common')."/lib/Twilio/autoload.php");
                        $sid = TWILIO_ACCOUNT_SID;
                        $token = TWILIO_AUTH_TOKEN;
                        $client = new \Twilio\Rest\Client($sid, $token);
                        $number_status = $client->lookups->phoneNumbers($mobile_no)->fetch2(["type" => "carrier"]);
                        if ($number_status == 200)
                        {
                            $existing_user = User::findByMobileno($input['to_mobile_no']);
                            $friend = new Friend();
                            $time = time();
                            if(count($existing_user) > 0)
                            {
                                $friend->from_user_id = $user->id;
                                $friend->to_user_id = $existing_user->id;
                                $friend->from_mobile_no = $user->mobile_no;
                                $friend->to_mobile_no = $input['to_mobile_no'];
                                $friend->send_to_existing_user = Friend::EXISTING_USER;
                                $friend->friendship_status = Friend::STATUS_SEND;
                                $friend->send_time = $time;

                                $friend_request_sms_text = Yii::$app->view->renderFile('@common/sms/friend_request_existing_user_sms.php',['user' => $user]);
                                $info = 'Request send successfully! Your friend will recieve a text message soon with further details. Onence they approve your friend request you will begin to see their quizzes under the "Shared Quizzes" tab';
                            }
                            else
                            {
                                $friend->from_user_id = $user->id;
                                $friend->from_mobile_no = $user->mobile_no;
                                $friend->to_mobile_no = $input['to_mobile_no'];
                                $friend->send_to_existing_user = Friend::NON_EXISTING_USER;
                                $friend->friendship_status = Friend::STATUS_SEND;
                                $friend->send_time = $time;

                                $friend_request_sms_text = Yii::$app->view->renderFile('@common/sms/friend_request_non_existing_user_sms.php',['user' => $user]);
                                $info = 'Opps!Your friend does not have an account yet so we sent them a text message with more information on how to subscribe so that you can both study together.';
                            }
                            $friend->save();
                            /*********Twilio SMS Send*********/
                            $settings = Setting::findOne(1);
                            $sms = $client->messages->create(
                                '+1'.$input['to_mobile_no'],
                                array(
                                    'from' => $settings->contact_phone,
                                    'body' => $friend_request_sms_text
                                )
                            );

                            return $this->addToJson(['info' => $info, 'user' => $user,'subscription' => $user->subscription,'subcribe_agency' => $user->subscription->agency,'subcribe_exam' => $user->subscription->exam]);
                        }
                        else
                        {
                            return $this->addToJson(['errors' => ['to_mobile_no' => ['Invalid Mobile no.']]]);
                        }
                    }
                }
                else
                {
                    return $this->addToJson(['errors' => ['to_mobile_no' => ['This is your mobile no.']]]);
                }
            }
            else
            {
                return $this->addToJson(['errors' => ['to_mobile_no' => ['Friend Mobile No cannot be blank.']]]);
            }
            
        }
        elseif($model->hasErrors())
        {
            return $this->sendErrors($model->getErrors());
        }
        else
        {
            return $this->addToJson(['error' => 'Internal Server error!']);
        }
    }

    public function actionFriends()
    {
        $model = new LoginForm();
        $model->scenario = 'app';
        $LoginForm_data = ['LoginForm' => ['active_device_id' => Yii::$app->getRequest()->getBodyParams('active_device_id')]];
        if(Yii::$app->request->isPost && $model->load($LoginForm_data) && $model->loginApi())
        {
            $user = Yii::$app->user->identity;
            $friend_obj = Friend::find()
                                ->where(['from_user_id' => $user->id, 'friendship_status' => Friend::STATUS_ACCEPT])
                                ->orWhere(['to_user_id' => $user->id, 'friendship_status' => Friend::STATUS_ACCEPT])
                                ->orderBy(['send_time' => SORT_DESC])
                                ->all();

            $friends = [];
            if(count($friend_obj) > 0)
            {
                $i = 0;
                foreach($friend_obj as $frnd)
                {
                    if($frnd->from_user_id == $user->id)
                    {
                        $friends[$i] = $frnd->toUser;
                    }
                    else
                    {
                        $friends[$i] = $frnd->fromUser;
                    }
                    $i++;
                }
            }

            $requestFrom_obj = Friend::find()
                                ->orWhere(['to_user_id' => $user->id, 'friendship_status' => Friend::STATUS_SEND])
                                ->orderBy(['send_time' => SORT_DESC])
                                ->all();

            $request_froms = [];
            if(count($requestFrom_obj) > 0)
            {
                $i = 0;
                foreach($requestFrom_obj as $rq_frnd)
                {
                    $request_froms[$i] = $rq_frnd->fromUser;
                    $i++;
                }
            }

            return $this->addToJson(['friends' => $friends, 'request_froms' => $request_froms, 'user' => $user,'subscription' => $user->subscription,'subcribe_agency' => $user->subscription->agency,'subcribe_exam' => $user->subscription->exam]);
        }
        elseif($model->hasErrors())
        {
            return $this->sendErrors($model->getErrors());
        }
        else
        {
            return $this->addToJson(['error' => 'Internal Server error!']);
        }
    }

    public function actionAcceptFriendRequest()
    {
        $model = new LoginForm();
        $model->scenario = 'app';
        $LoginForm_data = ['LoginForm' => ['active_device_id' => Yii::$app->getRequest()->getBodyParams('active_device_id')]];
        if(Yii::$app->request->isPost && $model->load($LoginForm_data) && $model->loginApi())
        {
            $user = Yii::$app->user->identity;
            $input = Yii::$app->getRequest()->getBodyParams();
            if(isset($input['from_user_id']) && $input['from_user_id'] != '')
            {
                $friend = Friend::find()
                                ->where(['from_user_id' => $input['from_user_id'],'to_user_id' => $user->id, 'friendship_status' => Friend::STATUS_SEND])
                                ->one();
                if(count($friend) > 0)
                {
                    $friend->friendship_status = Friend::STATUS_ACCEPT;
                    $friend->accept_reject_time = time();
                    $friend->save();
                    return $this->addToJson(['info' => 'Friend request accepted successfully', 'user' => $user,'subscription' => $user->subscription,'subcribe_agency' => $user->subscription->agency,'subcribe_exam' => $user->subscription->exam]);
                }
                else
                {
                    return $this->addToJson(['errors' => ['from_user_id' => ['Invalid From user ID.']]]);
                }
            }
            else
            {
                return $this->addToJson(['errors' => ['from_user_id' => ['From user ID cannot be blank.']]]);
            }
        }
        elseif($model->hasErrors())
        {
            return $this->sendErrors($model->getErrors());
        }
        else
        {
            return $this->addToJson(['error' => 'Internal Server error!']);
        }
    }

    public function actionRejectFriendRequest()
    {
        $model = new LoginForm();
        $model->scenario = 'app';
        $LoginForm_data = ['LoginForm' => ['active_device_id' => Yii::$app->getRequest()->getBodyParams('active_device_id')]];
        if(Yii::$app->request->isPost && $model->load($LoginForm_data) && $model->loginApi())
        {
            $user = Yii::$app->user->identity;
            $input = Yii::$app->getRequest()->getBodyParams();
            if(isset($input['from_user_id']) && $input['from_user_id'] != '')
            {
                $friend = Friend::find()
                                ->where(['from_user_id' => $input['from_user_id'],'to_user_id' => $user->id, 'friendship_status' => Friend::STATUS_SEND])
                                ->one();
                if(count($friend) > 0)
                {
                    $friend->delete();
                    return $this->addToJson(['info' => 'Friend request rejected successfully', 'user' => $user,'subscription' => $user->subscription,'subcribe_agency' => $user->subscription->agency,'subcribe_exam' => $user->subscription->exam]);
                }
                else
                {
                    return $this->addToJson(['errors' => ['from_user_id' => ['Invalid From user ID.']]]);
                }
            }
            else
            {
                return $this->addToJson(['errors' => ['from_user_id' => ['From user ID cannot be blank.']]]);
            }
        }
        elseif($model->hasErrors())
        {
            return $this->sendErrors($model->getErrors());
        }
        else
        {
            return $this->addToJson(['error' => 'Internal Server error!']);
        }
    }

    public function actionDeleteFriend()
    {
        $model = new LoginForm();
        $model->scenario = 'app';
        $LoginForm_data = ['LoginForm' => ['active_device_id' => Yii::$app->getRequest()->getBodyParams('active_device_id')]];
        if(Yii::$app->request->isPost && $model->load($LoginForm_data) && $model->loginApi())
        {
            $user = Yii::$app->user->identity;
            $input = Yii::$app->getRequest()->getBodyParams();
            if(isset($input['friend_id']) && $input['friend_id'] != '')
            {
                $friend = Friend::find()
                                ->where(['from_user_id' => $input['friend_id'],'to_user_id' => $user->id, 'friendship_status' => Friend::STATUS_ACCEPT])
                                ->orWhere(['from_user_id' => $user->id,'to_user_id' => $input['friend_id'], 'friendship_status' => Friend::STATUS_ACCEPT])
                                ->one();
                if(count($friend) > 0)
                {
                    $friend->delete();
                    return $this->addToJson(['info' => 'This Friend has been deleted successfully', 'user' => $user,'subscription' => $user->subscription,'subcribe_agency' => $user->subscription->agency,'subcribe_exam' => $user->subscription->exam]);
                }
                else
                {
                    return $this->addToJson(['errors' => ['friend_id' => ['Invalid Friend ID.']]]);
                }
            }
            else
            {
                return $this->addToJson(['errors' => ['friend_id' => ['Friend ID cannot be blank.']]]);
            }
        }
        elseif($model->hasErrors())
        {
            return $this->sendErrors($model->getErrors());
        }
        else
        {
            return $this->addToJson(['error' => 'Internal Server error!']);
        }
    }



    
    public function actionAccount()
    {
    	$model=UserAccountForm::findOne(Yii::$app->user->id);
    	if(!$model)
    		throw new NotFoundHttpException('The requested page does not exist.');
    	$model->scenario = UserAccountForm::ROLE_USER;
    	if (Yii::$app->request->isPost && $model->load(['UserAccountForm'=>Yii::$app->getRequest()->getBodyParams()]) && $model->save()) {
    		$this->addToJson(['info'=>t("You have successfully updated your account!")]);
    		return $this->sendData($model);
    	}elseif($model->hasErrors())
    	{
    		return $this->sendErrors($model->getErrors());
    	}
    	
    	return $this->sendData(app()->user->identity);
    }
    
    public function actionResetPassword()
    {
    	$model = new PasswordResetRequestForm();
    	if (Yii::$app->request->isPost && $model->load(['PasswordResetRequestForm'=>Yii::$app->request->bodyParams]) && $model->validate()) {
    		if ($model->sendEmail()) {
    			return $this->addToJson(['info'=>t("Check your email for further instructions.")]);
    		} else {
    			$model->addError('.', 'Sorry, we are unable to reset password for email provided.');
    		}
    	}
    	
    	if($model->hasErrors())
    	{
    		return $this->sendErrors($model->getErrors());
    	}
    }
}
