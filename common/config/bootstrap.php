<?php
use common\models\Sitetext;
use common\models\SitetextTranslation;

Yii::setAlias('@main_root', realpath(dirname(__FILE__).'/../../'));
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('@api', dirname(dirname(__DIR__)) . '/api');


function encrypt($string, $key = 'frlan')
{
	$result = '';
	for($i=0; $i<strlen($string); $i++) {
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$char = chr(ord($char)+ord($keychar));
		$result.=$char;
	}
	return base64_encode($result);
}
function decrypt($string, $key = 'frlan')
{
	$result = '';
	$string = base64_decode($string);
	for($i=0; $i<strlen($string); $i++) {
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$char = chr(ord($char)-ord($keychar));
		$result.=$char;
	}
	return $result;
}
function timeElapsedString($ptime)
{
	$etime = time() - $ptime;

	if ($etime < 1)
	{
		return '0 seconds';
	}

	$a = array( 365 * 24 * 60 * 60  =>  'year',
				 30 * 24 * 60 * 60  =>  'month',
				  7 * 24 * 60 * 60  =>  'week',
					  24 * 60 * 60  =>  'day',
						   60 * 60  =>  'hour',
								60  =>  'minute',
								 1  =>  'second'
				);
	$a_plural = array( 'year'   => 'years',
					   'month'  => 'months',
					   'week'   => 'weeks',
					   'day'    => 'days',
					   'hour'   => 'hours',
					   'minute' => 'minutes',
					   'second' => 'seconds'
				);

	foreach ($a as $secs => $str)
	{
		$d = $etime / $secs;
		if ($d >= 1)
		{
			$r = round($d);
			return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
		}
	}
}

function getLatLong($address)
{
    $address = str_replace(' ', '+', $address);
    $json = file_get_contents('https://maps.google.com/maps/api/geocode/json?key='.Yii::$app->params['google_map_api_key'] . '&address=' . $address);
    $json = json_decode($json);
    $lat = '';
    $long = '';
    if($json->status == 'OK')
    {
    	$lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
    	$long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
    }

    return ['lat' => $lat, 'long' => $long];
}

function getDbLanguageText($slug = NULL)
{
    $default_locale = Yii::$app->params['default_locale'];
    $locale = Yii::$app->language;
    if($slug != NULL)
    {
        $site_text = Sitetext::find()->where(['text_description' => $slug])->one();
        $text_translation = $site_text->getSitetextTranslations()->where(['sitetext_translation.locale' => $locale])->one();

        if($text_translation == NULL)
        {
            $text_translation = $site_text->getSitetextTranslations()->where(['sitetext_translation.locale' => $default_locale])->one();

        }
        return $text_translation->name;
    }
}