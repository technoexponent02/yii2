<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "property_features_translation".
 *
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string $locale
 * @property int $created_at
 * @property int $updated_at
 *
 * @property PropertyFeatures $category
 */
class PropertyFeaturesTranslation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'property_features_translation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id'], 'required'],
            [['category_id', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string'],
            [['locale'], 'string', 'max' => 10],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => PropertyFeatures::className(), 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Category ID',
            'name' => 'Name',
            'locale' => 'Locale',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(PropertyFeatures::className(), ['id' => 'category_id']);
    }
}
