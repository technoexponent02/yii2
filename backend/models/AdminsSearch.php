<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;

/**
 * AdminsSearch represents the model behind the search form of `common\models\User`.
 */
class AdminsSearch extends User
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'otp_request', 'parent_id', 'user_type', 'last_login', 'country_id', 'is_badge', 'is_online', 'status', 'pending_verification', 'created_at', 'updated_at'], 'integer'],
            [['name', 'username', 'first_name', 'last_name', 'organization_name', 'auth_key', 'password_hash', 'password_reset_token', 'verification_code', 'email', 'phone', 'usr_lat', 'usr_lng', 'sign_up_ip', 'login_ip', 'user_image', 'preferred_locale', 'reason', 'user_device', 'user_browser'], 'safe'],
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

        $logged_user = Yii::$app->user->identity;
        $logged_user_role = $logged_user->getRole($logged_user->id);
        //echo $logged_user_role; die;
        if ($logged_user_role == self::ROLE_ADMIN)
            {
                $query = User::find()->join('LEFT JOIN','auth_assignment','auth_assignment.user_id = user.id')
                    //->where(['=', 'auth_assignment.item_name', self::ROLE_ADMIN])
                    ->where(['=', 'auth_assignment.item_name', self::ROLE_QUALITY_TEAM])
                    ->orWhere(['=', 'auth_assignment.item_name', self::ROLE_SUPERVISOR])
                    ->andWhere(['!=', 'status', self::STATUS_DELETED])
                    ->orderBy(['created_at' => SORT_DESC]);
            }
            else
            {
                $query = User::find()->join('LEFT JOIN','auth_assignment','auth_assignment.user_id = user.id')
                    // ->where(['=', 'auth_assignment.item_name', self::ROLE_ADMIN])
                    ->where(['=', 'auth_assignment.item_name', self::ROLE_QUALITY_TEAM])
                    ->orWhere(['=', 'auth_assignment.item_name', self::ROLE_SUPERVISOR])
                    ->andWhere(['!=', 'status', self::STATUS_DELETED])
                    ->andWhere(['!=', 'id', $logged_user->id])
                    ->orderBy(['created_at' => SORT_DESC]);

            }
        // $query = User::find()->join('LEFT JOIN','auth_assignment','auth_assignment.user_id = user.id')
        //             //->where(['=', 'auth_assignment.item_name', self::ROLE_ADMIN])
        //             ->where(['=', 'auth_assignment.item_name', self::ROLE_QUALITY_TEAM])
        //             ->orWhere(['=', 'auth_assignment.item_name', self::ROLE_SUPERVISOR])
        //             ->andWhere(['!=', 'status', self::STATUS_DELETED])
        //             ->orderBy(['created_at' => SORT_DESC]);
            //echo $query->createCommand()->getRawSql(); die;

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
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
            'is_badge' => $this->is_badge,
            'is_online' => $this->is_online,
            'status' => $this->status,
            'pending_verification' => $this->pending_verification,
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
            ->andFilterWhere(['like', 'preferred_locale', $this->preferred_locale])
            ->andFilterWhere(['like', 'reason', $this->reason])
            ->andFilterWhere(['like', 'user_device', $this->user_device])
            ->andFilterWhere(['like', 'user_browser', $this->user_browser]);

        return $dataProvider;
    }
}
