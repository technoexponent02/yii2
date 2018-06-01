<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Json;

class Language extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public static function tableName()
    {
        return 'language';
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
        ];
    }

    /**
     * @inheritdoc
     */
    /*public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'weight' => Yii::t('app', 'Weight'),
            'fallback_locale' => Yii::t('app', 'Fallback Locale'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
*/
     public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', /*'ID'*/getDbLanguageText(144)),
            'name' => Yii::t('app', /*'Name'*/getDbLanguageText(22)),
            'weight' => Yii::t('app', /*'Weight'*/getDbLanguageText(151)),
            'fallback_locale' => Yii::t('app', 'Fallback Locale'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
  
}
