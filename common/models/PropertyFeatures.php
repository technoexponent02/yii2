<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "property_features".
 *
 * @property int $id
 * @property string $category
 * @property string $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property PropertyFeatureMatcher[] $propertyFeatureMatchers
 * @property PropertyFeaturesTranslation[] $propertyFeaturesTranslations
 */
class PropertyFeatures extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'property_features';
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
    public function getPropertyFeatureMatchers()
    {
        return $this->hasMany(PropertyFeatureMatcher::className(), ['feature_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPropertyFeaturesTranslations()
    {
        return $this->hasMany(PropertyFeaturesTranslation::className(), ['category_id' => 'id']);
    }

    public function getTranslatateData()
    {
        $default_locale = Yii::$app->params['default_locale'];
        //echo Yii::$app->language;exit;
        $current_locale = Yii::$app->language;
        $translated_obj = $this->getPropertyFeaturesTranslations()->where(['locale' => $current_locale])->one();
        if(!$translated_obj)
        {
            $translated_obj = $this->getPropertyFeaturesTranslations()->where(['locale' => $default_locale])->one();
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
                $property_categories_arr[$i]['cat_img'] = $property_category->cat_img;
                $i++;
            }
        }
        return $property_categories_arr;
    }
}


