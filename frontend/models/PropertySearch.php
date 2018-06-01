<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Property;

/**
 * PropertySearch represents the model behind the search form of `common\models\Property`.
 */
class PropertySearch extends Property
{
    public $to;
    public $from;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'user_type', 'property_category', 'property_type', 'property_condition', 'lot_size', 'built', 'no_of_room', 'no_of_bathroom', 'no_of_floor', 'units', 'rent_method', 'one_time_payment', 'additional_information', 'phone', 'rent_status', 'status', 'created_at', 'updated_at'], 'integer'],
            [['location', 'latitude', 'longitude'], 'safe'],
            [['rent_price', 'one_time_payment_price'], 'number'],
            [['to','from'], 'string'],
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
        
        if($user = Yii::$app->user->identity){
            switch ($user->user_type) {
            case 3:
                $query = Property::find()->where(['user_id' => $user->id])->andWhere(['<>','status','4']);
                break;
            case 4:
                $query = Property::find()->where(['parent_id' => $user->id])->andWhere(['<>','status','4']);
                break;
            case 5:
                $query = Property::find()->where(['parent_id' => $user->parent_id])->andWhere(['<>','status','4']);
                break;
            
            default:
                    $query = Property::find();
                break;
            }
        }
        /*else
        {
            $query = Property::find()->where(['status' => 2]);
            //$query = Property::find()->where(['status' => '1']);
        }*/

        
        

        //$query = Property::find();
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => ($data_limit>0)? $data_limit : 10,
            ],
            'sort' => [
                'defaultOrder' => [
                'created_at' => SORT_DESC,
                ]
            ],
        ]);


        //$sql = 'SELECT * FROM `property` WHERE `created_at` > '.$start_date.' AND created_at< '.$end_date;

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

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
            'rent_price' => $this->rent_price,
            'rent_method' => $this->rent_method,
            'one_time_payment' => $this->one_time_payment,
            'one_time_payment_price' => $this->one_time_payment_price,
            'additional_information' => $this->additional_information,
            'phone' => $this->phone,
            'rent_status' => $this->rent_status,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'location', $this->location])
            ->andFilterWhere(['like', 'latitude', $this->latitude])
            ->andFilterWhere(['like', 'longitude', $this->longitude]);
            if($this->from){
                $start_date = strtotime($this->from);
                /*echo date('Y-m-d', $start_date);
                exit;*/
                $query->andFilterWhere(['>=', 'created_at', $start_date]);
            }
            if($this->to){
                $end_date = strtotime($this->to) + (24*60*60);
                $query->andFilterWhere(['<=', 'created_at', $end_date]);
            }
        return $dataProvider;
    }
}
