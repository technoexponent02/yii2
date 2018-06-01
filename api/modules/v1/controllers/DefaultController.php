<?php
namespace api\modules\v1\controllers;

use api\modules\v1\Controller;
use Yii;
// use yii\web\Controller;
use common\models\SitetextTranslation;
class DefaultController extends Controller
{
	public function authExcept()
    {
        return ['index', 'static-text'];
    }
    public function actionIndex()
    {
        return 'v1';
    }

    public function actionStaticText()
    {
    	$api_input = Yii::$app->request->get();
    	if (isset($api_input['language']))
    	{
    		Yii::$app->language = $api_input['language'];
    	}
    	// echo Yii::$app->language;
    	$query = SitetextTranslation::find()->where(['locale' =>  Yii::$app->language]);
  //   	var_dump($query->prepare(Yii::$app->db->queryBuilder)->createCommand()->rawSql);
		// exit();
    	$site_texts = $query->all();

    	 return $this->addToJson(['results' => $site_texts]);
    }
}
