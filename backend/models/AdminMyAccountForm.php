<?php
namespace backend\models;
use Yii;
use yii\base\Model;
use common\models\User;
/**
 * AdminMyAccountForm form
 */
class AdminMyAccountForm extends User
{
    public $password_val;
    public $permissions;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'first_name', 'last_name', 'phone','user_type'], 'required'],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            // ['email', 'unique', 'targetClass' => 'common\models\User', 
            //     'message' => 'This email address has already been taken.'],
            [
               ['email'],
               'unique',
               'when' => function ($model, $attribute) {
                   return $model->{$attribute} != $model->getOldAttribute($attribute);
               }
           ],
           ['username', 'filter', 'filter' => 'trim'],
           ['username', 'string', 'max' => 255],
           [
               ['username'],
               'unique',
               'when' => function ($model, $attribute) {
                   return $model->{$attribute} != $model->getOldAttribute($attribute);
               }
           ],
            ['phone', 'match', 'pattern' => '/^\d{9}(?:\d{1})?$/', 'message' => 'Invalid phone number.'],
            [
               ['phone'],
               'unique',
               'when' => function ($model, $attribute) {
                   return $model->{$attribute} != $model->getOldAttribute($attribute);
               }
           ],
            [['first_name', 'last_name'], 'match','pattern' =>  '/^[a-zA-Z0-9\s]+$/' , 'message' => '{attribute} must not have any special charecters.'],           
            [['password_val'] , 'string'],
             
            
        ];
    }   
    public function attributeLabels()
    {
      
    }
    


}
