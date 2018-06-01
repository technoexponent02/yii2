<?php
namespace api\modules\v1;

use yii\helpers\ArrayHelper;
use common\models\Setting;

class Controller extends \yii\rest\Controller
{
	public $json=[];
	/**
	 * @inheritdoc
	 */
	public function init()
	{
		parent::init();
		// $setting = Setting::findOne(1);
		// $this->addToJson(['settings' => $setting]);
	}
	public function behaviors()
	{
		$behaviors=parent::behaviors();

		$behaviors['authenticator']['authMethods'] = [
		      \yii\filters\auth\QueryParamAuth::className(),
		];

		$behaviors['authenticator']['except']=$this->authExcept();
		return $behaviors;
	}

	protected function authExcept()
	{
		return [];
	}

	protected function addToJson($data)
	{
		return $this->json=ArrayHelper::merge($this->json, $data);
	}

	protected function sendData($data)
	{
		return $this->addToJson(['data'=>$data]);
	}

	protected function sendErrors($errors)
	{
		return $this->addToJson(['errors'=>$errors]);
	}
}