<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\models\User;
/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $name
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 */
class UserForm extends User
{
    public $password;
    public $notify=1;
    public $first_name;
    public $last_name;    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name', 'email'], 'required'],
            [['created_at', 'updated_at'/*, 'is_online', 'last_login'*/], 'integer'],
            [['name', 'first_name', 'last_name', 'email'], 'string', 'max' => 255],
            ['email', 'email'],
            ['password', 'safe'],
            ['email', 'unique', 'on' => 'create'],
            [
                ['email'],
                'unique',
                'when' => function ($model, $attribute) {
                    return $model->{$attribute} !== $model->getOldAttribute($attribute);
                },
                'on' => 'update'
            ],
			
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'name' => 'Name',
            'first_name' => 'first Name',
            'last_name' => 'Last Name',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
	
}
