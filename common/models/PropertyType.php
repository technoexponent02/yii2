<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "property_type".
 *
 * @property int $id
 * @property string $category
 * @property int $property_category_id
 * @property string $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Property[] $properties
 * @property PropertyCategory $propertyCategory
 * @property PropertyTypeTranslation[] $propertyTypeTranslations
 */
class PropertyType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'property_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category', 'property_category_id', 'created_at', 'updated_at'], 'required'],
            [['property_category_id', 'created_at', 'updated_at'], 'integer'],
            [['status'], 'string'],
            [['category'], 'string', 'max' => 100],
            [['property_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => PropertyCategory::className(), 'targetAttribute' => ['property_category_id' => 'id']],
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
            'property_category_id' => 'Property Category ID',
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
        return $this->hasMany(Property::className(), ['property_type' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPropertyCategory()
    {
        return $this->hasOne(PropertyCategory::className(), ['id' => 'property_category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPropertyTypeTranslations()
    {
        return $this->hasMany(PropertyTypeTranslation::className(), ['category_id' => 'id']);
    }

    public function getTranslatateData()
    {
        $default_locale = Yii::$app->params['default_locale'];
        //echo Yii::$app->language;exit;
        $current_locale = Yii::$app->language;
        $translated_obj = $this->getPropertyTypeTranslations()->where(['locale' => $current_locale])->one();
        if(!$translated_obj)
        {
            $translated_obj = $this->getPropertyTypeTranslations()->where(['locale' => $default_locale])->one();
        }
        return $translated_obj;
    }

    public static function getCategoryDropdownList($id = null)
    {
        if($id)
            $property_categories = self::find()->where(['property_category_id' => $id])->all();
        else
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
