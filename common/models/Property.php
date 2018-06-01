<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "property".
 *
 * @property int $id
 * @property string $user_id
 * @property string $modified_by
 * @property string $approved_by
 * @property int $user_type 1 = Admin, 2 = Quality team(sub-admin), 3 = Individual, 4 = Organization, 5 = Organization user, 6 = Buyer
 * @property string $parent_id
 * @property int $property_category
 * @property int $property_type
 * @property int $property_condition
 * @property int $lot_size
 * @property int $built
 * @property string $city
 * @property string $neighbourhood
 * @property string $location
 * @property string $latitude
 * @property string $longitude
 * @property int $no_of_room
 * @property int $no_of_bathroom
 * @property int $no_of_floor
 * @property int $units
 * @property string $rent_price
 * @property int $rent_method
 * @property int $one_time_payment 1: Yes; 0:No
 * @property string $one_time_payment_price
 * @property string $additional_information
 * @property int $phone
 * @property int $rent_status 1: Availaible ; 0: Rented
 * @property int $status 0: Incomplete; 1: Completed
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $user
 * @property PropertyCategory $propertyCategory
 * @property PropertyCondition $propertyCondition
 * @property PropertyRentMethod $rentMethod
 * @property PropertyType $propertyType
 * @property User $approvedBy
 * @property User $parent
 * @property User $modifiedBy
 * @property PropertyFeatureMatcher[] $propertyFeatureMatchers
 * @property PropertyImages[] $propertyImages
 * @property PropertyReports[] $propertyReports
 */
class Property extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'property';
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
            /*[['user_id', 'user_type', 'property_category', 'property_type', 'created_at', 'updated_at'], 'required'],*/
            [['user_id', 'modified_by', 'approved_by', 'user_type', 'parent_id', 'property_category', 'property_type', 'property_condition', 'lot_size', 'built', 'no_of_room', 'no_of_bathroom', 'no_of_floor', 'units', 'living_room', 'rent_method', 'one_time_payment', 'phone', 'rent_status', 'status', 'created_at', 'updated_at'], 'integer'],
            [['location', 'additional_information'], 'string'],
            [['rent_price', 'one_time_payment_price'], 'number'],
            [['city', 'neighbourhood', 'latitude', 'longitude', 'province'], 'string', 'max' => 100],
            [['locale'], 'string', 'max' => 10],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['property_category'], 'exist', 'skipOnError' => true, 'targetClass' => PropertyCategory::className(), 'targetAttribute' => ['property_category' => 'id']],
            [['property_condition'], 'exist', 'skipOnError' => true, 'targetClass' => PropertyCondition::className(), 'targetAttribute' => ['property_condition' => 'id']],
            [['rent_method'], 'exist', 'skipOnError' => true, 'targetClass' => PropertyRentMethod::className(), 'targetAttribute' => ['rent_method' => 'id']],
            [['property_type'], 'exist', 'skipOnError' => true, 'targetClass' => PropertyType::className(), 'targetAttribute' => ['property_type' => 'id']],
            [['approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['approved_by' => 'id']],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['parent_id' => 'id']],
            [['modified_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['modified_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            
            'id' => 'ID',
            'user_id' => 'User ID',
            'modified_by' => 'Modified By',
            'approved_by' => 'Approved By',
            'user_type' => 'User Type',
            'parent_id' => 'Parent ID',
            'property_category' => getDbLanguageText('Property_Category'),
            'property_type' => getDbLanguageText('Property_Type'),
            'property_condition' => getDbLanguageText('Condition'),
            'lot_size' => getDbLanguageText('Lot_Size'),
            'built' => getDbLanguageText('Built'),
            'city' => getDbLanguageText('City'),
            'living_room' => getDbLanguageText('living_room'),
            'neighbourhood' => getDbLanguageText('neighborhood'),
            'location' => getDbLanguageText('Address'),
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'no_of_room' => getDbLanguageText('Rooms'),
            'no_of_bathroom' => getDbLanguageText('Bathroom'),
            'no_of_floor' => getDbLanguageText('Floor'),
            'units' => getDbLanguageText('Units'),
            'rent_price' => getDbLanguageText('Price'),
            'rent_method' => 'Rent Method',
            'one_time_payment' => getDbLanguageText('One-time-payment'),
            'one_time_payment_price' => getDbLanguageText('One-time-payment'),
            //'one_time_payment_price' => 'One-Time Payment Price',
            'additional_information' =>  getDbLanguageText('Additional_Information'),
            'phone' => 'Phone',
            'rent_status' => 'Rent Status',
            'status' => 'Status',
            'locale' => 'Locale',
            'province' => 'Province',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPropertyCategory()
    {
        return $this->hasOne(PropertyCategory::className(), ['id' => 'property_category']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPropertyCondition()
    {
        return $this->hasOne(PropertyCondition::className(), ['id' => 'property_condition']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRentMethod()
    {
        return $this->hasOne(PropertyRentMethod::className(), ['id' => 'rent_method']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPropertyType()
    {
        return $this->hasOne(PropertyType::className(), ['id' => 'property_type']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApprovedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'approved_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(User::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModifiedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'modified_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPropertyFeatureMatchers()
    {
        return $this->hasMany(PropertyFeatureMatcher::className(), ['property_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPropertyImages()
    {
        return $this->hasMany(PropertyImages::className(), ['property_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPropertyReports()
    {
        return $this->hasMany(PropertyReports::className(), ['post_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReports()
    {
        return $this->hasOne(Reports::className(), ['id' => 'reason_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPropertyCoverImage()
    {
        return $this->getPropertyImages()->where(['is_cover'=> 1])->one();
    }

}
