<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "property_rent_method".
 *
 * @property int $id
 * @property string $category
 * @property string $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Property[] $properties
 * @property PropertyRentMethodTranslation[] $propertyRentMethodTranslations
 */
class PropertyRentMethod extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'property_rent_method';
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
        return $this->hasMany(Property::className(), ['rent_method' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPropertyRentMethodTranslations()
    {
        return $this->hasMany(PropertyRentMethodTranslation::className(), ['category_id' => 'id']);
    }

    public function getTranslatateData()
    {
        $default_locale = Yii::$app->params['default_locale'];
        //echo Yii::$app->language;exit;
        $current_locale = Yii::$app->language;
        $translated_obj = $this->getPropertyRentMethodTranslations()->where(['locale' => $current_locale])->one();
        if(!$translated_obj)
        {
            $translated_obj = $this->getPropertyRentMethodTranslations()->where(['locale' => $default_locale])->one();
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
