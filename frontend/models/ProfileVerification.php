<?php
namespace frontend\models;

use yii\base\Model;
use yii\base\InvalidParamException;
use common\models\User;
use common\components\SiteHelpers;
/**
 * RegisterVerification
 */
class ProfileVerification extends Model
{
    public $verification_code;

    /**
     * @var \common\models\User
     */
    private $_user;
    //public $authy_verification_code;

    /**
     * Creates a form model given a token.
     *
     * @param string $token
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidParamException('Verification Code cannot be blank.');
        }
        $this->_user = User::findIdentityByAccessToken($token);
        if (!$this->_user) {
            throw new InvalidParamException('Wrong Verification Code.');
        }
        parent::__construct($config);
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['verification_code' /*, 'authy_verification_code'*/], 'required'],
            ['verification_code', 'integer'],
            ['verification_code', 'validateVerificationCode'],
            //['verification_code', 'match','pattern' =>  '/^[a-zA-Z0-9\s]+$/'],
        ];
    }

    public function attributeLabels()
    {
         return [
        //     'password_repeat' => Yii::t('app', 'Confirm password'),
        'verification_code' => getDbLanguageText('Verification_code')
         ];
    }

     public function validateVerificationCode($attribute, $params)
    {
        if(!$this->hasErrors())
        {
            $user = User::findIdentityByVerificationCode2($this->verification_code);
            if(!$user)
            {
                $this->addError($attribute, getDbLanguageText('Invalid_verification_code'));
            }
            elseif($user->id != $this->_user->id)
            {
                $this->addError($attribute, getDbLanguageText('Invalid_verification_code'));
            }
        }
    }
}
