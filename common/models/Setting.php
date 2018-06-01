<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "settings".
 *
 * @property integer $id
 * @property string $admin_name
 * @property string $admin_email
 * @property string $site_title
 * @property string $contact_email
 * @property string $contact_name
 * @property string $contact_phone
  * @property string $broker_email
 * @property string $site_logo
 * @property string $site_fb_link
 * @property string $site_twitter_link
 * @property string $site_gplus_link
 * @property string $site_linkedin_link
 * @property string $site_pinterest_link
 * @property string $take_chat_type
 * @property string $take_chat_idle_time
 * @property double $general_commision_amount
 * @property double $min_chat_letter
 * @property string $created_at
 * @property string $updated_at
 */
class Setting extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'settings';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['admin_name', 'admin_email', 'site_title', 'contact_email', 'contact_name', 'broker_email'], 'required'],
            /*[['take_chat_type', 'take_chat_idle_time', 'min_chat_letter'], 'integer'],*/
            /*[['general_commision_amount'], 'number'],*/
            [['created_at', 'updated_at'], 'safe'],
            [['admin_name', 'admin_email', 'site_title', 'contact_email', 'contact_name', 'contact_phone', 'broker_email', 'site_logo', 'site_fb_link', 'site_twitter_link', 'site_gplus_link', 'site_linkedin_link', 'site_pinterest_link'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'admin_name' => 'Admin Name',
            'admin_email' => 'Admin Email',
            'site_title' => 'Site Title',
            'contact_email' => 'Contact Email',
            'contact_name' => 'Contact Name',
            'contact_phone' => 'Contact Phone',
            'broker_email' => 'Broker Email Address',
            'site_logo' => 'Site Logo',
            'site_fb_link' => 'Site Fb Link',
            'site_twitter_link' => 'Site Twitter Link',
            'site_gplus_link' => 'Site Gplus Link',
            'site_linkedin_link' => 'Site Linkedin Link',
            'site_pinterest_link' => 'Site Pinterest Link',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    /*public function validateContactNo($attribute, $params)
    {
        if(!$this->hasErrors())
        {
            $contact_phone = $this->contact_phone;
            //$contact_phone = '+919038880155';
            require_once(Yii::getAlias('@common')."/lib/Twilio/autoload.php");
            $sid = TWILIO_ACCOUNT_SID;
            $token = TWILIO_AUTH_TOKEN;
            $client = new \Twilio\Rest\Client($sid, $token);
            $number_status = $client->lookups->phoneNumbers($contact_phone)->fetch2(["type" => "carrier"]);
            if ($number_status != 200) {
                $this->addError($attribute, 'Invalid Mobile Number!');
            }
        }
    }*/
    /*public function beforeValidate()
    {
        if(parent::beforeValidate())
        {
            $this->contact_phone = str_replace('(', '', $this->contact_phone);
            $this->contact_phone = str_replace(')', '', $this->contact_phone);
            $this->contact_phone = str_replace(' ', '', $this->contact_phone);
            $this->contact_phone = str_replace('-', '', $this->contact_phone);
            return true;
        }
    }*/
}
