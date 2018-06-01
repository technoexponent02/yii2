<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\Property;

/**
 * Signup form
 */
class AddPostForm extends Property
{
    public $features = [];
    public $property_image = [];
    public $property_image_old = [];
    public $old_image_id = [];

    public $_image;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['property_category', 'property_type'],'required'],
            [['rent_price', 'features', 'city'],'required', 'on' => 'save_post'],
            [['user_id', 'modified_by', 'approved_by', 'user_type', 'parent_id', 'property_category', 'property_type', 'property_condition', 'built', 'no_of_room', 'no_of_bathroom', 'no_of_floor', 'units', 'living_room', 'rent_method', 'one_time_payment', 'rent_status', 'status', 'created_at', 'updated_at'], 'integer'],
            [['location', 'additional_information'], 'string'],
            [['rent_price', 'one_time_payment_price'], 'match', 'pattern' => '/^[0-9٠-٩,،]/'],
            [['phone','lot_size'], 'match', 'pattern' => '/^[0-9٠-٩]/'],
            [['latitude', 'longitude', 'neighbourhood', 'city', 'province'], 'string', 'max' => 100],
            [['locale'], 'string', 'max' => 10],
            [['features','old_image_id'], 'each', 'rule' => ['integer']],
            [['property_image','property_image_old'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'maxFiles' => 10],
            [['rent_price', 'lot_size'],'required', 'on' => 'add_post_step_2'],
            [['location' ,'city'],'required', 'on' => 'add_post_step_4'],
            ['_image', 'safe'],

        ];

    } 
   

    /*public function beforeValidate()
    {
        if(parent::beforeValidate())
        {
            $this->mobile_no = str_replace('(', '', $this->mobile_no);
            $this->mobile_no = str_replace(')', '', $this->mobile_no);
            $this->mobile_no = str_replace(' ', '', $this->mobile_no);
            $this->mobile_no = str_replace('-', '', $this->mobile_no);
            return true;
        }
    }*/
    
    



}
