<?php
namespace api\modules\v1\models;

use Yii;
use yii\base\Model;
use common\models\User;
use common\models\UserSubscription;

/**
 * Signup form
 */
class ApiSignupForm extends Model
{
    public $user_unique_code;
    public $device_id;
    public $device_type;
    public $device_os_version;
    private $_user;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_unique_code', 'device_id', 'device_type', 'device_os_version'], 'required'],
            ['user_unique_code', 'uniqueCodeUser'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_unique_code' => Yii::t('app', 'User unique code'),
            'device_id' => Yii::t('app', 'Device ID'),
            'device_type' => Yii::t('app', 'Device Type'),
            'device_os_version' => Yii::t('app', 'OS Version'),
        ];
    }
    public function uniqueCodeUser($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUserByUniqueCode();
            if (!$user) {
                $this->addError($attribute, 'Unique code does not match!');
            }
            else
            {
                if($user->active_device_id != NULL || $user->active_device_type != NULL)
                {
                    $this->addError($attribute, 'This user has been already register with another device!');
                }   
            }
        }
    }
    protected function getUserByUniqueCode()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUniqueCode($this->user_unique_code);
        }

        return $this->_user;
    }
    public function registerApi()
    {
        if($this->validate()) {
            return Yii::$app->user->login($this->getUserByUniqueCode());
        } else {
            return false;
        }
    }
}
