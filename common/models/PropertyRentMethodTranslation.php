<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "property_rent_method_translation".
 *
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string $locale
 * @property int $created_at
 * @property int $updated_at
 *
 * @property PropertyRentMethod $category
 */
class PropertyRentMethodTranslation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'property_rent_method_translation';
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
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => PropertyRentMethod::className(), 'targetAttribute' => ['category_id' => 'id']],
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
        return $this->hasOne(PropertyRentMethod::className(), ['id' => 'category_id']);
    }
}
