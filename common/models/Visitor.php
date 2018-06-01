<?php

namespace common\models;

use Yii;

use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "Visitor".
 *
 * @property int $id
 * @property int $ad_type 0 = One Ads, 10 = Two Ads
 * @property string $uploaded_image1
 * @property string $uploaded_image2
 * @property int $created_at
 * @property int $updated_at
 */
class Visitor extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'visitors';
    }
     /**

     * @inheritdoc

     */

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
            [['created_at', 'updated_at'], 'integer'],
            [['id', 'user_agent', 'ip'], 'string'],
       
        ];
    }

}