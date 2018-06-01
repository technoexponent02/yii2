<?php
namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\User;

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
            //[['email', 'password'], 'required', 'on' => 'frontend_login'],
            //['email', 'email', 'on' => 'frontend_login'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword','on' => 'admin_login'],
            //['password', 'validatePasswordFrontend','on' => 'frontend_login'],
            /*['password', 'validatePasswordAffiliate','on' => 'affiliate_login'],*/
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
            if (!$user || !$user->validatePassword($this->password))
             {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

   

    
    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {

        if ($this->validate()) {
             //echo 1; die;
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
            }
        }

        return $this->_user;
    }  


   

}
