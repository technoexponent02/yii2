<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\PropertyReports;

/**
 * ReportsSearch represents the model behind the search form of `common\models\Reports`.
 */
class PropertyReportsSearch extends PropertyReports
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          
             
             [['id', 'report_id',  'approved_by', 'post_id', 'status'], 'integer'],
            //[['status'], 'string'],
            [['user_id'], 'safe'],// Put the associated model foriegn key in safe 
            // for the search with the related model
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
    public function search($params)
    {
        $query = PropertyReports::find()->orderBy(['created_at' => SORT_DESC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            // 'pagination' => [
            //     'pageSize' => 2,
            // ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->joinWith('user');// write the relation name associated with the model

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'post_id' => $this->post_id,
            'report_id' => $this->report_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'user.name', $this->user_id ]);

        
        return $dataProvider;
    }
}
