<?php
/*
author :: Pritam Das
change language by get language=EN, language=AR,...
or select on this widget
*/
 
namespace common\components;
 
use Yii;
use yii\base\Component;
use yii\base\Widget;
use yii\bootstrap\ButtonDropdown;
use yii\helpers\Url;
use yii\web\Cookie;
 
class LanguageSwitcher extends Widget
{
    public $languages = [];
 
    public function init()
    {
        if(php_sapi_name() === 'cli')
        {
            return true;
        }
        parent::init();
        $this->languages = Yii::$app->params['languages'];
        $session = Yii::$app->session;
        $languageNew = Yii::$app->request->get('language');
        if($languageNew)
        {
            if(isset($this->languages[$languageNew]))
            {
                Yii::$app->language = $languageNew;
                $session->set('language', $languageNew);
            }
        }
        elseif($session->has('language'))
        {
            Yii::$app->language = $session->get('language');
        }
 
    }
 
    public function run()
    {
        $languages = $this->languages;
        $current = $languages[Yii::$app->language];
        unset($languages[Yii::$app->language]);
 
        $items = [];
        foreach($languages as $code => $language)
        {
            $temp = [];
            $temp['label'] = $language;
            $temp['url'] = Url::toRoute(['site/change-language', 'language' => $code]);
            array_push($items, $temp);
        }
        echo ButtonDropdown::widget([
            'label' => $current,
            'dropdown' => [
                'items' => $items,
            ],
            'options' => ['class' => 'langSelectBtn'],
        ]);
    }
 
}