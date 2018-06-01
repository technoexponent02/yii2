<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $email;
    public $rememberMe = true;

    private $_user;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required', 'on' => 'admin_login'],
            [['email', 'password'], 'required', 'on' => 'frontend_login'],
            //['email', 'email', 'on' => 'frontend_login'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword','on' => 'admin_login'],
            ['password', 'validatePasswordFrontend','on' => 'frontend_login'],
            /*['password', 'validatePasswordAffiliate','on' => 'affiliate_login'],*/
        ];
    }

    public function attributeLabels()
    {
        return [
            
            'email' => getDbLanguageText('Email'),
            'password' => getDbLanguageText('Password'),
        ];
    }


    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password) || $user->getRole($user->id) != User::ROLE_ADMIN) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    public function validatePasswordFrontend($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password) || !in_array($user->getRole($user->id), [User::ROLE_INDIVIDUAL, User::ROLE_ORGANIZATION, User::ROLE_ORGANIZATION_USER, User::ROLE_BUYER])) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /*public function validatePasswordAffiliate($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password) || $user->getRole($user->id) != User::ROLE_AFFILIATE) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }*/
    
    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            if($this->scenario == 'admin_login')
            {
                $this->_user = User::findByUsername($this->username);
            }
            elseif($this->scenario == 'frontend_login')
            {
                if(User::findByEmail($this->email))
                    $this->_user = User::findByEmail($this->email);
                else if(User::findByMobileno($this->email))
                    $this->_user = User::findByMobileno($this->email);
                else if(User::findById($this->email))
                    $this->_user = User::findById($this->email);
                else if(User::findByEmailBlocked($this->email))
                    $this->_user = User::findByEmailBlocked($this->email);                
            }
        }

        return $this->_user;
    }

    /*protected function getUser()
    {
        if ($this->_user === null) {
            if($this->scenario == 'admin_login')
            {
                $this->_user = User::findByUsername($this->username);
            }
            elseif($this->scenario == 'frontend_login')
            {
                $this->_user = User::findByEmail($this->email);
            }
        }

        return $this->_user;
    }*/



    public function loginApi()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUserApi());
        } else {
            return false;
        }
    }

}
