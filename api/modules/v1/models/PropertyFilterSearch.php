<?php

namespace api\modules\v1\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Property;
use common\models\PropertyTypeTranslation;
use yii\helpers\VarDumper;
use common\models\User;
/**
 * PropertySearch represents the model behind the search form of `common\models\Property`.
 */
class PropertyFilterSearch extends Property
{
    public $size;
    public $price;
    public $price_sort;

    public $keyword;
    public $coords;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'user_type', 'property_category', 'property_type', 'property_condition', 'lot_size', 'built', 'no_of_room', 'no_of_bathroom', 'no_of_floor', 'units', 'rent_method', 'one_time_payment', 'additional_information', 'phone', 'rent_status', 'status', 'created_at', 'updated_at'], 'integer'],
            [['location', 'latitude', 'longitude'], 'safe'],
            [['rent_price', 'one_time_payment_price','size','price', 'price_sort'], 'number'],
            [[ 'keyword', 'city', 'province', 'neighbourhood', 'coords'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $data_limit = 0)
    {

        $query = Property::find()->where(['property.status' => 2, 'rent_status' => 1])
                ->joinWith('user')->andWhere(['user.status' => User::STATUS_ACTIVE]);
        //$query = Property::find();
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => ($data_limit>0)? $data_limit : 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'updated_at' => SORT_DESC,
                ],
            ],
        ]);

        //$sql = 'SELECT * FROM `property` WHERE `created_at` > '.$start_date.' AND created_at< '.$end_date;

        $this->load($params);

        if (!$this->validate()) {
            //print_r($this->getErrors()); die;
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            //return $dataProvider;

            return ['error' => 1, 'errors' => $this->getErrors()];
        }

        //echo $this->keyword; die;

        if($this->keyword){
            $property_types = $this->makePropertiesTypeList($this->keyword);
            //print_r($property_types);
            $property_types_arr = array();
            foreach ($property_types as $key => $value) {
                $property_types_arr[] = $value['category_id'];
            }
            //print_r($property_types_arr);
            $property_types_arr = implode(",", $property_types_arr);
        }

        //print_r($property_types_arr);
        //die;

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_type' => $this->user_type,
            'property_category' => $this->property_category,
            'property_type' => $this->property_type,
            'property_condition' => $this->property_condition,
            'lot_size' => $this->lot_size,
            'built' => $this->built,
            'no_of_room' => $this->no_of_room,
            'no_of_bathroom' => $this->no_of_bathroom,
            'no_of_floor' => $this->no_of_floor,
            'units' => $this->units,
            //'rent_price' => $this->rent_price,
            'rent_method' => $this->rent_method,
            //'one_time_payment' => $this->one_time_payment,
            'one_time_payment_price' => $this->one_time_payment_price,
            'additional_information' => $this->additional_information,
            'phone' => $this->phone,
            'rent_status' => $this->rent_status,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'location', $this->location])
            ->andFilterWhere(['like', 'neighbourhood', $this->neighbourhood])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'province', $this->province])
            ->andFilterWhere(['<=', 'rent_price', $this->price])
            ->andFilterWhere(['>=', 'one_time_payment', $this->one_time_payment]);


        if($this->size){
            $query->andWhere(['or',
                    ['<=', 'lot_size', $this->size],
                    ['lot_size' => NULL]
                    ]);
        }
        if($this->price_sort){
            $query->orderBy(['rent_price' => ($this->price_sort == 1) ? SORT_ASC : SORT_DESC]);
        }



        /*$query->andFilterWhere(['like', 'location', $this->location])
            ->andFilterWhere(['like', 'latitude', $this->latitude])
            ->andFilterWhere(['like', 'longitude', $this->longitude]);*/

         if ($this->keyword) {
                $query->andFilterWhere(['or',
                    ['like','city',$this->keyword],
                    ['like','province',$this->province],
                    //['property_type'=> [$property_types_arr]],
                    ['IN', 'property_type', array_map('intval', explode(',', $property_types_arr))],
                    ['like','neighbourhood',$this->keyword],
                    ['=', 'property.id', $this->keyword]
                ]);
                // foreach ($property_types_arr as $key => $value) {
                //      $query->andFilterWhere(['or',
                //         ['=','property_type',$value],
                //  ]);
                // }
                 // $query->andFilterWhere(['IN', 'address_category.cate_id', array_map('intval', explode(',', $category))]);

           }
           // echo $query->createCommand()->getRawSql();
           // die;
        return $dataProvider;
    }

    public function makePropertiesTypeList($search_keyword)
    {
        $properties = PropertyTypeTranslation::find()->select('category_id')
                        ->where(['LIKE', 'name', $search_keyword])
                        ->andWhere(['=', 'locale', Yii::$app->language])
                        ->groupBy('name')
                        ->asArray()
                        ->all();
        /*$properties = PropertyType::translatateData()->find()->select('name as listname')
                        ->all();*/
        return $properties;
    }
}
