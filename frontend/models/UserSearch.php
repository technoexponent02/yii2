<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;

/**
 * UserSearch represents the model behind the search form of `common\models\User`.
 */
class UserSearch extends User
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'otp_request', 'parent_id', 'user_type', 'last_login', 'country_id', 'is_online', 'status', 'created_at', 'updated_at'], 'integer'],
            [['name', 'username', 'first_name', 'last_name', 'organization_name', 'auth_key', 'password_hash', 'password_reset_token', 'verification_code', 'email', 'phone', 'usr_lat', 'usr_lng', 'sign_up_ip', 'login_ip', 'user_image', 'preferred_locale'], 'safe'],
            [['account_balance'], 'number'],
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
        //$query = User::find();
        $query = User::find()->where(['parent_id' => Yii::$app->user->id]);
        //$query = User::find(['parent_id' => 1]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'otp_request' => $this->otp_request,
            'parent_id' => $this->parent_id,
            'user_type' => $this->user_type,
            'last_login' => $this->last_login,
            'account_balance' => $this->account_balance,
            'country_id' => $this->country_id,
            'is_online' => $this->is_online,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'organization_name', $this->organization_name])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'verification_code', $this->verification_code])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'usr_lat', $this->usr_lat])
            ->andFilterWhere(['like', 'usr_lng', $this->usr_lng])
            ->andFilterWhere(['like', 'sign_up_ip', $this->sign_up_ip])
            ->andFilterWhere(['like', 'login_ip', $this->login_ip])
            ->andFilterWhere(['like', 'user_image', $this->user_image])
            ->andFilterWhere(['like', 'preferred_locale', $this->preferred_locale]);

        return $dataProvider;
    }
}
