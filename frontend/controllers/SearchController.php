<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use yii\helpers\ArrayHelper;

use common\components\SiteHelpers;
use yii\helpers\VarDumper;

use common\models\Property;
use common\models\User;


class SearchController extends Controller
{
    /**
     * @inheritdoc
     */
    public $user;
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                /*'only' => ['logout', 'signup'],*/
                'rules' => [
                    [
                        'actions' => ['index', 'searching'],
                        'allow' => true,
                    ],                   
                    
                ],
            ],
           
        ];
    }

    public function beforeAction($action)
    {            
        if ($action->id == 'searching') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index', []);
    }

    public function actionSearching()
    {
        //echo "<pre>";
        $session = Yii::$app->session;
        $searchedproperties = array();
        if (Yii::$app->request->isPost && Yii::$app->request->post('coords') != null) {
           $session->remove('propertyCategory');
           $session->remove('searchedproperties');
           $session->remove('map_data');
           $cords=(string)Yii::$app->request->post('coords');
           preg_match_all('/\((.*?)\)/', $cords, $matches);
           $coords= $matches[1];
           //print_r($coords);
           $pX=array();
           $pY=array();
           foreach ($coords as $k=>$v)
           {      
               
               $arr= explode(",",$v);
               array_push($pX,trim($arr[0]));
               array_push($pY,trim($arr[1]));
           }
            // print_r($pX);
            // print_r($pY);
            $latitude_array = $pX;
            $longitude_array = $pY;
            // arsort($pX);
            // arsort($pY);
            // print_r($pX);
            // print_r($pY);
            // $max_lat = $pX[0];
            // $max_long = $pY[0];
            // print_r($max_lat); echo "<br/>";
            // print_r($max_long);
            // echo Property::find()->select('id, CAST(latitude as DECIMAL(10,5))latitude, CAST(longitude as DECIMAL(10,5))longitude')->where(['status' => 2])
            //                                 ->andWhere(['<=', 'CAST(latitude as DECIMAL(10,5))', $max_lat])
            //                                 ->andWhere(['<=', 'CAST(longitude as DECIMAL(10,5))', $max_long])->createCommand()->getRawSql();
            // $properties = Property::find()->select('id, latitude, longitude')->where(['status' => 2])
            //                                 ->andWhere(['<=', 'CAST(latitude as DECIMAL(10,5))', $max_lat])
            //                                 ->andWhere(['<=', 'CAST(longitude as DECIMAL(10,5))', $max_long])->asArray()->all();

            $propertyCategory = Yii::$app->request->post('propertyCategory');

            $properties = Property::find()->select('property.id, latitude, longitude, property_type, rent_price, no_of_room, no_of_bathroom, lot_size')->where(['property.status' => 2, 'property_category' => $propertyCategory, 'rent_status' => 1])->joinWith('user')->andWhere(['user.status' => User::STATUS_ACTIVE])->all();
            $map_data = array();

            //VarDumper::dump($properties);
            foreach ($properties as $pkey => $property) {
                    $vertices_x = $latitude_array; // x-coordinates of the vertices of the polygon
                    $vertices_y = $longitude_array; // y-coordinates of the vertices of the polygon
                    $points_polygon = count($vertices_x); // number vertices
                    $latitude_x = $property["latitude"];
                    $longitude_y = $property["longitude"];
                    if ($this->is_in_polygon($points_polygon, $vertices_x, $vertices_y, $latitude_x, $longitude_y)){
                        //echo "in polygon"; 
                        array_push($searchedproperties, array($property->id, $property->latitude, $property->longitude, $property->propertyCoverImage->property_image, $property->propertyType->translatateData->name, $property->rent_price, $property->lot_size, $property->no_of_room, $property->no_of_bathroom));
                        $map_data[] = $property->id;
                    }
                    //else echo "Is not in polygon";
            }
            $session['propertyCategory'] = $propertyCategory;
            $session['searchedproperties']= $searchedproperties;
            $session['map_data']= $map_data;
      }

      else {
        $propertyCategory = $session['propertyCategory'];
        $searchedproperties = $session['searchedproperties'];
        $map_data = $session['map_data'] ;
      }
      
      //VarDumper::dump($searchedproperties);
         return $this->render('map', [
          'searchedproperties' => $searchedproperties,
          'propertyCategory' => $propertyCategory,
          'map_data' => $map_data,
        ]);
}
    

  
private function is_in_polygon($points_polygon, $vertices_x, $vertices_y, $latitude_x, $longitude_y)
    {
          $i = $j = $c = 0;
          for ($i = 0, $j = $points_polygon-1 ; $i < $points_polygon; $j = $i++) {
            if ( (($vertices_y[$i] > $longitude_y != ($vertices_y[$j] > $longitude_y)) &&
            ($latitude_x < ($vertices_x[$j] - $vertices_x[$i]) * ($longitude_y - $vertices_y[$i]) / ($vertices_y[$j] - $vertices_y[$i]) + $vertices_x[$i]) ) ) 
                $c = !$c;
          }
          return $c;
    }

   
}