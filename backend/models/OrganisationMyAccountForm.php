<?php
namespace backend\models;
use Yii;
use yii\base\Model;
use common\models\User;
/**
 * OrganisationMyAccountForm form
 */
class OrganisationMyAccountForm extends User
{
    public $password_val;
    public $sub_user_id;
    public $sub_user_name;
    public $sub_user_status;
    public $sub_user_password;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // ['sub_user_id', 'each', 'rule' => ['integer']],
            // ['sub_user_status', 'each', 'rule' => ['integer']],
            // ['sub_user_name', 'each', 'rule' => ['required']],
            [['email', 'organization_name', 'name', 'phone', 'status'], 'required'],
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
            ['phone', 'match', 'pattern' => '/^\d{9}(?:\d{1})?$/', 'message' => 'Invalid phone number.'],
            [
               ['phone'],
               'unique',
               'when' => function ($model, $attribute) {
                   return $model->{$attribute} != $model->getOldAttribute($attribute);
               }
           ],
            //[[ 'organization_name', 'name'], 'match','pattern' =>  '/^[a-zA-Z0-9\s]+$/' , 'message' => '{attribute} must not have any special charecters.'],
            [['user_image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, gif, bmp, jpeg'],
            [['password_val'] , 'string'],
             ['reason', 'required', 'when' => function ($model) {
                    return $model->status == 3;
                }, 'whenClient' => "function (attribute, value) {                 
                    return $('#individualmyaccountform-status').val() == 3;
                }"],
            [['is_badge'], 'integer'],

            
        ];
    }   
    public function attributeLabels()
    {
         return [
                    'user_image' => Yii::t('app', ''),
                ];
    }
    


}
