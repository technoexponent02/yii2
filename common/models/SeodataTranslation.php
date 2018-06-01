<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "sitetext_translation".
 *
 * @property integer $id
 * @property integer $sitetext_id
 * @property string $name
 * @property string $locale
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Sitetext $sitetext
 */
class SeodataTranslation extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'seodata_translation';
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
            [['seodata_id', 'created_at', 'updated_at'], 'integer'],
            [['page_title', 'page_keywords', 'page_description'], 'string'],
            [['page_title'], 'required'],
            [['locale'], 'string', 'max' => 10],
            [['seodata_id'], 'exist', 'skipOnError' => true, 'targetClass' => Seodata::className(), 'targetAttribute' => ['seodata_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'seodata_id' => 'Seodata ID',
            'page_title' => 'Page Title',
            'page_keywords' => 'Page Keywords',
            'page_description' => 'Page Description',
            'locale' => 'Locale',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeodata()
    {
        return $this->hasOne(Seodata::className(), ['id' => 'seodata_id']);
    }
}
