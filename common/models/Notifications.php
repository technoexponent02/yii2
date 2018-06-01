<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "notifications".
 *
 * @property int $id
 * @property int $user_id
 * @property int $parent_id
 * @property string $type
 * @property string $msg_table
 * @property int $msg_id
 * @property int $status 0:unseen ; 1:Seen
 * @property int $created_at
 * @property int $updated_at
 */
class Notifications extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notifications';
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
            [['user_id'], 'required'],
            [['user_id', 'model_id', 'status', 'created_at', 'updated_at', 'report_id'], 'integer'],
            [['type'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['report_id'], 'exist', 'skipOnError' => true, 'targetClass' => Reports::className(), 'targetAttribute' => ['report_id' => 'id']],
        ]; 
    } 

    /** 
     * @inheritdoc 
     */ 
    public function attributeLabels() 
    { 
        return [ 
            'id' => 'ID',
            'user_id' => 'User ID',
            'type' => 'Type',
            'model_id' => 'Model ID',
            'report_id' => 'Report ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ]; 
    } 

    /** 
     * @return \yii\db\ActiveQuery 
     */ 
    public function getUser() 
    { 
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    } 

    /** 
     * @return \yii\db\ActiveQuery 
     */ 
    public function getReport() 
    { 
        return $this->hasOne(Reports::className(), ['id' => 'report_id']);
    } 
} 
