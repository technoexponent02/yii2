<?php

namespace common\models;

use Yii;
use common\models\PropertyTypeTranslation;
/**
 * This is the model class for table "property_category_translation".
 *
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string $locale
 * @property int $created_at
 * @property int $updated_at
 *
 * @property PropertyCategory $category
 */
class PropertyCategoryTranslation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'property_category_translation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string'],
            [['locale'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => PropertyCategory::className(), 'targetAttribute' => ['category_id' => 'id']],
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
        return $this->hasOne(PropertyCategory::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPropertyTypeTranslations()
    {
        return $this->hasMany(PropertyTypeTranslation::className(), ['property_category_id' => 'category_id', 'locale' =>'locale']);
    }

}
