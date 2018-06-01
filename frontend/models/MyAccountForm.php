<?php
namespace frontend\models;
use Yii;
use yii\base\Model;
use common\models\User;
use common\models\Country;
/**
 * MyAccount form
 */
class MyAccountForm extends User
{
    
    public $password;
    public $password_repeat;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            ['country_id', 'integer'],
            ['preferred_locale', 'string'],
            [['first_name', 'last_name'], 'required', 'on' => ['individual_update', 'subuser_update']],
            [['organization_name', 'name' ], 'required', 'on' => 'organization_update'],
            // [['first_name', 'last_name', 'organization_name'], 'match','pattern' =>  '/^[a-zA-Z0-9\s]+$/' , 'message' => '{attribute} must not have any special charecters.'],
            [['user_image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, gif, bmp'],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['country_id' => 'id']],
            ['password_repeat', 'string', 'min' => 6],
            ['phone', 'match', 'pattern' => '/(^[1-9][0-9]{8})$/'],
            [
                ['email'],
                'unique',
                'when' => function ($model, $attribute) {
                    return $model->{$attribute} != $model->getOldAttribute($attribute);
                },
            ],
            [
                ['phone'],
                'unique',
                'when' => function ($model, $attribute) {
                    return $model->{$attribute} != $model->getOldAttribute($attribute);
                },
            ],
            ['password', 'string', 'min' => 6],
            ['password_repeat', 'compare', 'compareAttribute'=>'password'],
        ];
    }   
    public function attributeLabels()
    {
         return [
        //     'password_repeat' => Yii::t('app', 'Confirm password'),
        'password' => getDbLanguageText('Password'),
        'first_name' => getDbLanguageText('First_name'),
        'last_name' => getDbLanguageText('Last_name'),
        'name' => getDbLanguageText('Name'),
        'organization_name' => getDbLanguageText('Organization_name'),
        'email' => getDbLanguageText('Email'),
        'phone' => getDbLanguageText('Phone'),
        'password_repeat' => getDbLanguageText('Confirm_password'),
         ];
    }


}
