<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "sitetext".
 *
 * @property integer $id
 * @property string $text_description
 * @property string $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property SitetextTranslation[] $sitetextTranslations
 */
class Sitetext extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $names;
    public static function tableName()
    {
        return 'sitetext';
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
            [['text_description', 'status'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
            ['names', 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'text_description' => 'Text Description',
            'names' => 'Language Text',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSitetextTranslations()
    {
        return $this->hasMany(SitetextTranslation::className(), ['sitetext_id' => 'id']);
    }

    public function getDefaultSiteText()
    {
        $default_locale = Yii::$app->params['default_locale'];
        return $this->getSitetextTranslations()->where(['sitetext_translation.locale' => $default_locale])->one()->name;
    }
}
