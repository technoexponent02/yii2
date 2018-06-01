<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\UserForm;
use common\models\User;

/**
 * UserSearch represents the model behind the search form about `backend\models\UserForm`.
 */
class UserSearch extends UserForm
{
    public $all_user = 0;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'all_user', 'user_type'], 'integer'],
            [['name', 'phone', 'username', 'auth_key', 'password_hash', 'password_reset_token', 'email'], 'safe'],
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
    public function search($params, $type = 'paginate')
    {
        //echo $this->all_user; die;
        //print_r($params); die;
        $current_time = time();

        

        $query = UserForm::find()
                                ->join('LEFT JOIN','auth_assignment','auth_assignment.user_id = user.id')
                                ->where(['!=', 'user_type', 1])
                                ->andWhere(['!=', 'user_type', 2])
                                ->andWhere(['!=', 'user_type', 7])
                                ->andWhere(['!=', 'status', self::STATUS_INCOMPLETE])
                                ->andWhere(['<', "TIMESTAMPDIFF(HOUR, DATE_FORMAT(FROM_UNIXTIME(user.created_at), '%Y-%m-%d H:i:s'), DATE_FORMAT(FROM_UNIXTIME($current_time), '%Y-%m-%d H:i:s'))", 72])
                                ->orderBy(['created_at' => SORT_DESC]);
        
        if (isset($params['UserSearch']['all_user']) && $params['UserSearch']['all_user'] == 1)
        {
            $query = UserForm::find()
                                ->join('LEFT JOIN','auth_assignment','auth_assignment.user_id = user.id')
                                ->where(['!=', 'user_type', 1])
                                ->andWhere(['!=', 'user_type', 2])
                                ->andWhere(['!=', 'user_type', 7]);
        }

        // add conditions that should always apply here

        if($type == 'all')
        {
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => false,
                'sort' => ['attributes' => ['id']]
            ]);
        }
        else
        {
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'sort' => ['attributes' => ['id']]
            ]);
        }

        // $dataProvider->setSort([
        //     'defaultOrder' => ['created_at' => SORT_DESC],            
        // ]);

        if (!($this->load($params) && $this->validate())) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
           // $query->joinWith(['device']);
            return $dataProvider;
        }
        /*$this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }*/

        // grid filtering conditions
        $filter_cond_arr = ['id' => $this->id];
        if ($this->user_type>0)
        {
            $filter_cond_arr['user_type'] = $this->user_type;
        }
        if ($this->status != "")
        {
            if ($this->status == User::STATUS_UNACTIVATED)
                {
                     $filter_cond_arr['status'] = User::STATUS_ACTIVE;
                     $filter_cond_arr['pending_verification'] = 2;
                }
                else
                {
                    $filter_cond_arr['status'] = $this->status;
                    $filter_cond_arr['pending_verification'] = 0;
                }
           
        }
        
        
        $query->andFilterWhere($filter_cond_arr); 
        $query->andFilterWhere(['like', 'phone', $this->phone]);

        // $query->andFilterWhere(['like', 'name', $this->name])
        //     ->andFilterWhere(['like', 'email', $this->email])
        //     ->andFilterWhere(['like', 'phone', $this->phone]);

        return $dataProvider;
    }
}
