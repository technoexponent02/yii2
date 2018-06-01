<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "reports".
 *
 * @property int $id
 * @property string $category
 * @property string $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property ReportsTranslation[] $reportsTranslations
 */
class Reports extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reports';
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
    public function getReportsTranslations()
    {
        return $this->hasMany(ReportsTranslation::className(), ['category_id' => 'id']);
    }

    public function getTranslatateData()
    {
        $default_locale = Yii::$app->params['default_locale'];
        //echo Yii::$app->language;exit;
        $current_locale = Yii::$app->language;
        $translated_obj = $this->getReportsTranslations()->where(['locale' => $current_locale])->one();
        if(!$translated_obj)
        {
            $translated_obj = $this->getReportsTranslations()->where(['locale' => $default_locale])->one();
        }
        return $translated_obj;
    }

    public static function getCategoryDropdownList()
    {
        $reports = self::find()->all();
        $reports_arr = [];
        if(count($reports) > 0)
        {
            $i = 0;
            foreach ($reports as $report) 
            {
                $reports_arr[$i]['id'] = $report->id;
                $reports_arr[$i]['category'] = $report->translatateData->name;
                $i++;
            }
        }
        return $reports_arr;
    }
}
