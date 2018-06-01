<?php

namespace common\models;
use yii\behaviors\TimestampBehavior;

use Yii;

/**
 * This is the model class for table "property_feature_matcher".
 *
 * @property int $id
 * @property int $property_id
 * @property int $feature_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Property $property
 * @property PropertyFeatures $feature
 */
class PropertyFeatureMatcher extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'property_feature_matcher';
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
            [['property_id', 'feature_id', 'created_at', 'updated_at'], 'required'],
            [['property_id', 'feature_id', 'created_at', 'updated_at'], 'integer'],
            [['property_id'], 'exist', 'skipOnError' => true, 'targetClass' => Property::className(), 'targetAttribute' => ['property_id' => 'id']],
            [['feature_id'], 'exist', 'skipOnError' => true, 'targetClass' => PropertyFeatures::className(), 'targetAttribute' => ['feature_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'property_id' => 'Property ID',
            'feature_id' => 'Feature ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProperty()
    {
        return $this->hasOne(Property::className(), ['id' => 'property_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeature()
    {
        return $this->hasOne(PropertyFeatures::className(), ['id' => 'feature_id']);
    }
}
