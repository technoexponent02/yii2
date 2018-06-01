<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class SignupFormOrg extends User
{
    public $password_org;
    public $password_repeat_org;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'password_org', 'password_repeat_org', 'phone'], 'required'],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => 'common\models\User'],
            ['phone', 'match', 'pattern' => '/(^[1-9][0-9]{8})$/'],
            ['phone', 'unique', 'targetClass' => 'common\models\User'],
            ['password_org', 'string', 'min' => 6],
            ['password_repeat_org', 'compare', 'compareAttribute'=>'password_org'],
            [['organization_name', 'name'], 'required'],
            /*[['organization_name', 'name'], 'match','pattern' =>  '/^[a-zA-Z0-9أ-ي\s]+$/' , 'message' => '{attribute} must not have any special charecters.'],*/
            
        ];
    }

    public function attributeLabels()
    {
        return [
            'organization_name' => getDbLanguageText('organization_name'),
            'name' => getDbLanguageText('Admin_name'),
            'password_org' => getDbLanguageText('Password'),
            'password_repeat_org' => getDbLanguageText('Confirm_password'),
            'email' => getDbLanguageText('Email'),
            'first_name' => getDbLanguageText('First_name'),
            'last_name' => getDbLanguageText('Last_name'),
            'phone' => getDbLanguageText('Phone'),
        ];
    }

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
