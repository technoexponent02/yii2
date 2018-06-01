<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "searches".
 *
 * @property string $id
 * @property int $results
 * @property int $baths
 * @property int $rooms
 * @property string $price
 * @property int $type
 * @property string $search
 * @property int $created_at
 * @property int $updated_at
 */
class Searches extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'searches';
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
            [['results', 'baths', 'rooms', 'created_at', 'updated_at'], 'integer'],
            [['price'], 'number'],
            ['search', 'required'],
            [['search'], 'string'],
            [['type'], 'exist', 'skipOnError' => true, 'targetClass' => PropertyType::className(), 'targetAttribute' => ['type' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'results' => 'Results',
            'baths' => 'Baths',
            'rooms' => 'Rooms',
            'price' => 'Price',
            'type' => 'Type',
            'search' => 'Search',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

   /** 
    * @return \yii\db\ActiveQuery 
    */ 
   public function getType0() 
   { 
       return $this->hasOne(PropertyType::className(), ['id' => 'type']); 
   } 
}
