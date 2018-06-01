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
class SitetextTranslation extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sitetext_translation';
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
            [['sitetext_id', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string'],
            [['locale'], 'string', 'max' => 10],
            [['sitetext_id'], 'exist', 'skipOnError' => true, 'targetClass' => Sitetext::className(), 'targetAttribute' => ['sitetext_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sitetext_id' => 'Sitetext ID',
            'name' => 'Name',
            'locale' => 'Locale',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSitetext()
    {
        return $this->hasOne(Sitetext::className(), ['id' => 'sitetext_id']);
    }
}
