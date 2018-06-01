<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class SignupForm extends User
{
    public $password_repeat;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'password', 'password_repeat', 'phone'], 'required'],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => 'common\models\User'],
            //['phone', 'match', 'pattern' => '/^\d{9}(?:\d{1}a{9})?$/'],
            ['phone', 'match', 'pattern' => '/(^[1-9][0-9]{8})$/'],
            ['phone', 'unique', 'targetClass' => 'common\models\User'],
            ['password', 'string', 'min' => 6],
            ['password_repeat', 'compare', 'compareAttribute'=>'password'],
            [['first_name', 'last_name'], 'required'],
            /*[['first_name', 'last_name'], 'match','pattern' =>  '/^[a-zA-Z0-9أ-ي\s]+$/' , 'message' => '{attribute} must not have any special charecters.'],*/
            
        ];
    }

    public function attributeLabels()
    {
        return [
            
            'email' => getDbLanguageText('Email'),
            'password' => getDbLanguageText('Password'),
            'password_repeat' => getDbLanguageText('Confirm_password'),
            'first_name' => getDbLanguageText('First_name'),
            'last_name' => getDbLanguageText('Last_name'),
            'phone' => getDbLanguageText('Phone'),
        ];
    }

   
    
    // public function attributeLabels()
    // {
    //     return [
    //         'password_repeat' => Yii::t('app', 'Confirm password'),
    //     ];
    // }

    /*public function beforeValidate()
    {
        if(parent::beforeValidate())
        {
            $this->mobile_no = str_replace('(', '', $this->mobile_no);
            $this->mobile_no = str_replace(')', '', $this->mobile_no);
            $this->mobile_no = str_replace(' ', '', $this->mobile_no);
            $this->mobile_no = str_replace('-', '', $this->mobile_no);
            return true;
        }
    }*/
    
    



}
