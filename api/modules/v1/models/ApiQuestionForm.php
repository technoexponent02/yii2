<?php
namespace api\modules\v1\models;

use Yii;
use yii\base\Model;

/**
 * Signup form
 */
class ApiQuestionForm extends Model
{
    public $quiz_id;
    public $question_id;
    public $question;
    public $answer1;
    public $answer2;
    public $answer3;
    public $answer4;
    public $currect_answer;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question', 'answer1', 'answer2', 'answer3', 'answer4', 'currect_answer'], 'required'],
            [['quiz_id', 'currect_answer'], 'integer'],
            [['question', 'answer1', 'answer2', 'answer3', 'answer4'], 'string'],
            [['question_id'], 'required', 'on' => 'update'],
            [['quiz_id'], 'required', 'on' => 'create'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'quiz_id' => Yii::t('app', 'Quiz'),
            'question_id' => Yii::t('app', 'Question ID'),
            'question' => Yii::t('app', 'Question'),
            'answer1' => Yii::t('app', 'Answer1'),
            'answer2' => Yii::t('app', 'Answer2'),
            'answer3' => Yii::t('app', 'Answer3'),
            'answer4' => Yii::t('app', 'Answer4'),
            'currect_answer' => Yii::t('app', 'Currect Answer'),
        ];
    }
    
}
