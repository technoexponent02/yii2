<?php

namespace api\modules\v1\controllers;

use api\modules\v1\Controller;
use Yii;
use common\models\Property;
use yii\helpers\VarDumper;
use api\modules\v1\models\PropertyFilterSearch;

use yii\helpers\ArrayHelper;
use common\models\PropertyCategoryTranslation;
use common\models\PropertyTypeTranslation;

class PropertySearchController extends Controller
{
    public function verbs()
    {
        return [
            //'rank-list'                => ['post'],
          ];
    }
    public function authExcept()
    {
        return ['index', 'auto-complete-search', 'province-list',
                'search-properties', 'search-properties-map', 'property-category',
                'property-type'];
    }
    public function actionIndex()
    {
        // echo 1; die;
        return 'v1';
        // echo Yii::$app->language;
        // Yii::$app->language = 'en';
        // echo Yii::$app->language;
        // $properties = Property::find()->joinWith(['propertyCategory'])->all();
        // // VarDumper::dump($properties);
        // return ArrayHelper::toArray($properties, [
        //     'common\models\Property' => [
        //         'id',
        //         'propertyCategory'
        //     ]
        // ]);
    }

    public function actionPropertyCategory()
    {
        $api_input = Yii::$app->request->get();
        if (isset($api_input['language']))
        {
            Yii::$app->language = $api_input['language'];
        }
        // echo Yii::$app->language;
        $query = PropertyCategoryTranslation::find()->where(['locale' =>  Yii::$app->language]);
        $property_category = $query->all();
        return $this->addToJson(['results' => $property_category]);
    }
    public function actionPropertyType()
    {
        $api_input = Yii::$app->request->get();
        if (isset($api_input['language']))
        {
            Yii::$app->language = $api_input['language'];
        }
        // echo Yii::$app->language;
        $query = PropertyTypeTranslation::find()->where(['locale' =>  Yii::$app->language]);
        if (isset($api_input['property_category']))
        {
            $query->where(['property_category_id' =>  $api_input['property_category']]);
        }
        $property_type = $query->all();
        return $this->addToJson(['results' => $property_type]);
    }
    public function actionAutoCompleteSearch()
    {
            $api_input = Yii::$app->request->get();
            $keyword = isset($api_input['keyword']) ? $api_input['keyword'] : null;
            if(trim($keyword) != null)
            {
               $autocompleteList = $this->makeAutoCompleteList($keyword);
               if($autocompleteList){
                    return $this->addToJson(['results' => $autocompleteList]);
                }
            }
            else
            {
                $autocompleteList = $this->makeAutoCompleteList();
                //VarDumper::dump($autocompleteList);
                if($autocompleteList){
                    return $this->addToJson(['results' => $autocompleteList]);
                }
            }
    }
    public function actionProvinceList()
    {
        $query = Property::find()->select('province')
                        ->andWhere(['status' => 2, 'rent_status' => 1])
                        ->andWhere(['not', ['province' => null]])
                        ->groupBy('province');
        //echo $query->createCommand()->getRawSql(); die;
        $provinces = $query->asArray()->all();
        return $this->addToJson(['results' => $provinces]);
    }

    private function makeAutoCompleteList($search_keyword)
    {
        $autocompleteList = array();
        // make property_type list

        // make city list
        $city_list = $this->makeCityPropertiesList($search_keyword);
        //make neighbourhood list
        $neighbourhood_list = $this->makeNeighbourhoodPropertiesList($search_keyword);
        //$propertyType_list = $this->makePropertiesTypeList($search_keyword);
        //$autocompleteList =array_merge($city_list, $neighbourhood_list, $propertyType_list);
        //return $autocompleteList;

        return array_merge($city_list, $neighbourhood_list);
    }
    private function makeCityPropertiesList($search_keyword = null)
    {
        $properties = Property::find()->select('city as listname');
        if ($search_keyword != null)
        {
            $properties = $properties->where(['LIKE', 'city', $search_keyword.'%', false]);
        }
        $properties = $properties->andWhere(['status' => 2, 'rent_status' => 1])
                        ->groupBy('city')
                        ->asArray()
                        ->all();
        return $properties;
    }
    private function makeNeighbourhoodPropertiesList($search_keyword)
    {
        $properties = Property::find()->select('neighbourhood as listname')
                        ->where(['LIKE', 'neighbourhood', $search_keyword.'%', false])
                        ->groupBy('neighbourhood')
                        ->asArray()
                        ->all();
        return $properties;
    }

    public function actionSearchProperties()
    {
        $api_input = Yii::$app->request->queryParams;
        $searchModel = new PropertyFilterSearch();
        $searchModel->load(['PropertyFilterSearch' => $api_input]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $propertyList = array();
        if (is_array($dataProvider) && isset($dataProvider['error']) && $dataProvider['error'] == 1)
        {
            return $this->sendErrors($dataProvider['errors']);
        }
        if ($dataProvider->getCount() > 0)
            {
                foreach ($dataProvider->getModels() as $key => $property) {
                    $propertyList[] = $this->preparePropertyResponse($property);
                }
            }
        return $this->addToJson(['results' => $propertyList, 'pagination_details' => $dataProvider->getPagination()]);

    }

    public function actionSearchPropertiesMap()
    {
        $api_input = Yii::$app->request->queryParams;

        $searchModel = new PropertyFilterSearch();
        $searchModel->load(['PropertyFilterSearch' => $api_input]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if (is_array($dataProvider) && isset($dataProvider['error']) && $dataProvider['error'] == 1)
        {
            return $this->sendErrors($dataProvider['errors']);
        }
        $dataProvider->setPagination(false);

        $cords = isset($api_input['cords']) ? $api_input['cords'] : null;
        $propertyList = array();
        if ($cords == null && $cords == "")
        {

            if ($dataProvider->getCount() > 0)
            {
                foreach ($dataProvider->getModels() as $key => $property) {
                    $propertyList[] = $this->preparePropertyResponse($property);
                }
            }
            //print_r($cords); die;

        }
        else
        {
            $propertyList = $this->polygonSearch($dataProvider, $cords);
        }
        return $this->addToJson(['results' => $propertyList]);

        //return $this->addToJson(['results' => $searchedproperties]);
    }

    private function polygonSearch($dataProvider, $cords = null)
    {
        preg_match_all('/\((.*?)\)/', $cords, $matches);
        $coords= $matches[1];
        //print_r($coords); die;
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

        $searchedproperties = array();
        if ($dataProvider->getCount() > 0)
        {
            foreach ($dataProvider->getModels() as $pkey => $property) {
                $vertices_x = $latitude_array; // x-coordinates of the vertices of the polygon
                $vertices_y = $longitude_array; // y-coordinates of the vertices of the polygon
                $points_polygon = count($vertices_x); // number vertices
                $latitude_x = $property["latitude"];
                $longitude_y = $property["longitude"];
                if ($this->is_in_polygon($points_polygon, $vertices_x, $vertices_y, $latitude_x, $longitude_y)){
                    //echo "in polygon";
                   $searchedproperties[] = $this->preparePropertyResponse($property);
                }
                //else echo "Is not in polygon";
            }
        }
        return $searchedproperties;
    }

    private function preparePropertyResponse($property = null)
    {
        return  [
                            'property' => $property,
                            'property_images' => $property->propertyImages,
                            'property_type' => $property->propertyType,
                            'property_category' => $property->propertyCategory
                    ];
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
