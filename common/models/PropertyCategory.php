<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "property_category".
 *
 * @property int $id
 * @property string $category
 * @property string $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Property[] $properties
 * @property PropertyCategoryTranslation[] $propertyCategoryTranslations
 * @property PropertyType[] $propertyTypes
 */
class PropertyCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'property_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category', 'created_at', 'updated_at'], 'required'],
            [['status'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
            [['category'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category' => 'Category',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProperties()
    {
        return $this->hasMany(Property::className(), ['property_category' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPropertyCategoryTranslations()
    {
        return $this->hasMany(PropertyCategoryTranslation::className(), ['category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPropertyTypes()
    {
        return $this->hasMany(PropertyType::className(), ['property_category_id' => 'id']);
    }

    public function getTranslatateData()
    {
        $default_locale = Yii::$app->params['default_locale'];
        //echo Yii::$app->language;exit;
        $current_locale = Yii::$app->language;
        $translated_obj = $this->getPropertyCategoryTranslations()->where(['locale' => $current_locale])->one();
        if(!$translated_obj)
        {
            $translated_obj = $this->getPropertyCategoryTranslations()->where(['locale' => $default_locale])->one();
        }
        return $translated_obj;
    }

    public static function getCategoryDropdownList()
    {
        $property_categories = self::find()->all();
        $property_categories_arr = [];
        if(count($property_categories) > 0)
        {
            $i = 0;
            foreach ($property_categories as $property_category) 
            {
                $property_categories_arr[$i]['id'] = $property_category->id;
                $property_categories_arr[$i]['category'] = $property_category->translatateData->name;
                $i++;
            }
        }
        return $property_categories_arr;
    }
}
