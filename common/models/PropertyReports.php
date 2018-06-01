<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "property_reports".
 *
 * @property int $id
 * @property int $report_id
 * @property string $user_id
 * @property string $approved_by
 * @property int $post_id
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Property $post
 * @property Reports $report
 * @property User $user
 * @property User $approvedBy
 */
class PropertyReports extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'property_reports';
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
            [['report_id', 'user_id', 'post_id'], 'required'],
            // [['report_id', 'user_id', 'approved_by', 'post_id', 'created_at', 'updated_at'], 'required'],
            [['report_id', 'user_id', 'approved_by', 'post_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => Property::className(), 'targetAttribute' => ['post_id' => 'id']],
            [['report_id'], 'exist', 'skipOnError' => true, 'targetClass' => Reports::className(), 'targetAttribute' => ['report_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['approved_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'report_id' => 'Report ID',
            'user_id' => 'User ID',
            'approved_by' => 'Approved By',
            'post_id' => 'Post ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(Property::className(), ['id' => 'post_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReport()
    {
        return $this->hasOne(Reports::className(), ['id' => 'report_id']);
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
    public function getApprovedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'approved_by']);
    }
}
