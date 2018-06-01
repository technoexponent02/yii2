<?php 
namespace common\components;
use Yii;
use common\models\Property;
use Authy;
use common\models\Setting;
use common\models\User;
use yii\helpers\VarDumper;
use common\models\Visitor;
use common\models\Notifications;
use common\models\PropertyImages;
class SiteHelpers
{
    public static function getClientIP()
    {
      if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
      } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
      } else {
        $ip = $_SERVER['REMOTE_ADDR'];
      }
      return $ip;
    }

    public static function ipDetails($ip) {
      $json = file_get_contents("http://ipinfo.io/{$ip}/geo");
      $details = json_decode($json, true);
      return $details;
    }

    public static function checkFileExists($filepath)
    {
      $imagePath = \yii::getAlias('@backend');
      $imagePath = str_replace('\\', '/', $imagePath)."/web".$filepath;
      if (file_exists($imagePath))
      {
        return $imagePath;
      }
      else{
        return false;
      }
    }

    public static function totalPostedRentedPropertiesCount($rent_status = 1)
    {
      $query = Property::find()
                      ->where(['IN', 'property.status', [1, 2, 3]]);
      if ($rent_status == 0)
      {
          $query = $query->andWhere(['=', 'property.rent_status', 0 ]);
      }
      $properties = $query->count();
      return $properties;
    }

    public static function getPropertiesMonthWiseTotalCountByCategoryOrPropertyType(
      $property_category = null, $property_type =null, $rent_status = 1, $month = null, $year = null)
    {
        if ($month == null )
        {
          $month = date("m", time());
          $year = date("Y", time());
        }
        $query = Property::find()
                      ->where(['IN', 'property.status', [1, 2, 3]]);
                      // ->andWhere(['DATE_FORMAT(FROM_UNIXTIME(created_at), "%m")'=> date("m", time())]);
        if ($month > 0 && $year > 0)
        {
          $query = $query->andWhere(['=', 'DATE_FORMAT(FROM_UNIXTIME(created_at), "%Y-%m")', "$year-$month"]);
        }
              
        if ($property_category != null)
        {
          $query = $query->andWhere(['=', 'property.property_category', $property_category ]);
        } 
        if ($property_type != null)
        {
          $query = $query->andWhere(['=', 'property.property_type', $property_type ]);
        } 
        if ($rent_status == 0)
        {
            $query = $query->andWhere(['=', 'property.rent_status', 0 ]);
        }           
        $properties = $query->count();
        return $properties;
    }


     /*
        create the twilio user id by registering with email, phone , country code
     */
    public static function authyRegisterUserApi($email, $phone, $country_code)
    {
          $authy_api = new Authy\AuthyApi(Yii::$app->params['twilio_authy_api_key']);
          $user = $authy_api->registerUser($email, $phone, $country_code);
          $response = array();
          if($user->ok())
            {
                $response['user_id'] = $user->id();
            }
          else
            {
                // $errorMsg = '';
                // foreach($user->errors() as $field => $message) {
                //   $errorMsg .= "$field = $message";
                // }
                $response['errors'] = $user->errors();
            }
          return $response;
    }

    public static function authySendVerificationSms($authy_user_id)
    {
        $authy_api = new Authy\AuthyApi(Yii::$app->params['twilio_authy_api_key']);
        $sms = $authy_api->requestSms($authy_user_id);
    }

    public static function authyUserVerify($authy_user_id , $sms_code)
    {
        $authy_api = new Authy\AuthyApi(Yii::$app->params['twilio_authy_api_key']);
        $verification = $authy_api->verifyToken($authy_user_id, $sms_code);
        $response = array();
        if ($verification->ok()) {
          $response['verified'] = true;
        }
        else
        {
          $response['errors'] = $verification->errors();
        }
        return $response;
    }

    public static function phoneTwilioVerificationSms($code, $phone = '9038880155', $register_sms_text = 'رمز توثيق جوالك في وارف هو: ' )
    {
        $register_sms_text = $register_sms_text.$code." ";
        require_once(Yii::getAlias('@common')."/lib/Twilio/autoload.php");
        $sid = //Removed for confidentiality
        $token = //Removed for confidentiality
        $response = array();
        $client = new \Twilio\Rest\Client($sid, $token);

        //$register_sms_text = Yii::$app->view->renderFile('@common/sms/register_sms.php');

        try 
        {
            $sms = $client->messages->create(
                            '+966'.$phone,
                            array(
                            'from' => //Removed for confidentiality,
                            'body' => $register_sms_text
                            )
            );
            $response['sid'] = $sms->sid;    
        } 
        catch ( \Twilio\Exceptions\RestException $e ) 
        {
            //VarDumper::dump($e->getMessage());
            $response['errors'] = $e->getMessage();
        }
    }
    public static function fbShare($url = '')
    {
        return "https://www.facebook.com/sharer/sharer.php?u=$url";
    }

    public static function twShare($url = '', $title = '')
    {
        $exploded = explode(' - ', $title);
        $hashtags = str_replace(' ', '_', $exploded[0]);

        return //Removed for confidentiality

    }

    public static function emailShare($url = '', $title = '')
    {
        return "mailto:?Subject=$title&Body=$url";
    }

    public static function whatsappShare($url = '')
    {
        return "whatsapp://send?text=$url";
    }

     /**
     * [sendSiteEmails description]
     * @param  [type] $email_type ["R" for Register, "F" for Forgot Password]
     * @param  array  $emails     [Array of the emails addresses]
     * @param  [type] $model      [model object passed to the email template]
     * @return [type]             [description]
     */
    public static function sendSiteEmails($email_type, $emails = array(), $model)
    {
        $html = "";
        $text = "";

/*        VarDumper::dump($model->id);
        die();*/

        $subject = "";
        if ($email_type == "R")
        {
          $html = "verificationCode-html";
          $text = "verificationCode-text";  
          $subject = "Registration";
          
        }
        elseif ($email_type == "W") // warning for post
        {
          $html = "postWarningMail-html";
          $text = "postWarningMail-text";  
          $subject = "Post Warning";          
        }
        elseif ($email_type == "B") // warning for post
        {
          $html = "blockedMail-html";
          $text = "blockedMail-text";  
          $subject = "تم حظر حسابك";          
        }
        elseif($email_type == "F")
        {
          $html = "passwordResetToken-html";
          $text = "passwordResetToken-text";
          $subject = "اعادة تعيين الرقم السري";
        }
        elseif($email_type == "C")
        {
          $html = "supportRequest-html";
          $text = "supportRequest-text";
          $subject = "#".$model->id;
          $emails = Yii::$app->params['supportEmail'];
        }
        elseif($email_type == "S")
        {
          $html = "subuserRequest-html";
          $text = "subuserRequest-text";
          $subject = "#".$model->user_id;
          $emails = Yii::$app->params['supportEmail'];
        }
        $setting = Setting::findOne(1);
        Yii::$app->mailer->compose(
                ['html' => "$html", 'text' => "$text"],
                ['model' => $model]
            )
        ->setFrom([$setting->contact_email => $setting->site_title])
        ->setTo($emails)
        ->setSubject($subject)
        ->send();
    }

    /**
     * [getBadgeProgress description]
     * @param  User   $user [description]
     * @return [integer]       [count the number of approved properties]
     */
    public static function getBadgeProgress(User $user)
    {
      $approved_properties = Property::find()
                      ->where(['=', 'property.status', '2'])
                      ->andWhere(['=', 'user_id', $user->id])
                      ->count();
      $percentProgress = ($approved_properties>10) ? 100 : $approved_properties*10;
      return $percentProgress;
    }
    public static function recordVisitorCount()
    {

      if (Visitor::find()->where(['id'=>Yii::$app->session->getId()])->count() == 0)
        {
          $visitor = new Visitor;
          $visitor->id = Yii::$app->session->getId();
          $visitor->user_agent = Yii::$app->getRequest()->getUserAgent();
          $visitor->ip = Yii::$app->getRequest()->getUserIP();
          $visitor->session_count = 1;
          $visitor->save();
          // if ($visitor->hasErrors())
          // {
          //   VarDumper::dump($visitor->getErrors());
          // }
        }
        else 
        {
          
          $visitor = Visitor::find()->where(['id'=>Yii::$app->session->getId()])->one();
          $visitor->session_count = $visitor->session_count + 1;
          $visitor->save();
          //die;
        }

      return Visitor::find()->count();

    }

    public static function getNoVisitorMonthWise($month, $year, $new_entries = 0)
    {
      $query = Visitor::find()
                  ->where(['=', 'DATE_FORMAT(FROM_UNIXTIME(updated_at), "%Y-%m")', "$year-$month"]);
      if ($new_entries > 0)
      {
        $current_time = time();
        $query = $query->andWhere(['<', "TIMESTAMPDIFF(HOUR, DATE_FORMAT(FROM_UNIXTIME(created_at), '%Y-%m-%d H:i:s'), DATE_FORMAT(FROM_UNIXTIME($current_time), '%Y-%m-%d H:i:s'))", $new_entries]);
      }
      $visitors = $query->count();
      return $visitors;
    }

    public static function getAverageTimeOnSite()
    {
      $avg_time_on_site = Visitor::find()->select('AVG(updated_at - created_at) as avg_diff')->asArray()->one();
      //VarDumper::dump($avg_time_on_site);
      return $avg_time_on_site != null ? (int) ($avg_time_on_site['avg_diff'] / 60) : 0;
    }

    /** 
      * [sendNotification description]
      * @param  User   $user [description]

        (W) -> Warning 
        Warnings are warning against post refering to the property reports table or Rejections
        $user -> One who will recieve the notification. In this case both Subusers who added and 
                 last modified by if they are different along with the Organaization they belong to.
        $type-> 'W'
        $model_id-> property id related the post the warning is being made of.
        ---------------------------------------------------------------------
        (P) -> Post published
        Post published.
        $user -> One who will recieve the notification. In this case both Subusers who added and 
                 last modified by if they are different along with the Organaization they belong to.
        $type-> 'P'
        $model_id-> property id related the post that is being published.
        ----------------------------------------------------------------------
        (N) -> New Subuser Added
        New Subuser Added or approved by Admin.
        $user -> Organaization / parent_id of the user that is being created.
        $type-> 'N'
        $model_id-> User id related the subuser that was created.
        ---------------------------------------------------------------------
        (E) -> Expired Subuser
        Subuser whose expiration date has passed the current date.
        $user -> Organaization / parent_id of the user that is being expired.
        $type-> 'E'
        $model_id-> User id related the subuser that has expired.
        --------------------------------------------------------------------
        (R) -> Reset Password
        After successfull reset password
        $user -> Organaization / Individual / Subuser.
        $type-> 'R'
        $model_id-> Null.
        ---------------------------------------------------------------------
      **/

    public static function sendNotification(User $user, $type, $model_id)
    {

      $model = new Notifications();
      $model->user_id = $user->id;
      $model->type = $type;
      $model->model_id = $model_id;
    
      if($type == 'W' || $type == 'P'){
        $property = Property::findOne($model_id);
        ($type == 'W' && $property->reason_id > 0) ? $model->report_id = $property->reason_id : '';
        if($property->parent_id > 0){
          $model_org = new Notifications();
          $model_org->user_id = $property->parent_id;
          $model_org->type = $type;
          $model_org->model_id = $model_id;
          ($type == 'W' && $property->reason_id > 0) ? $model_org->report_id = $property->reason_id : '';
          if(!$model_org->save())
            return false;
          if($property->user_id != $property->modified_by){
            $model_modified = new Notifications();
            $model_modified->user_id = $property->modified_by;
            $model_modified->type = $type;
            $model_modified->model_id = $model_id;
            ($type == 'W' && $property->reason_id > 0) ? $model_modified->report_id = $property->reason_id : '';
            if(!$model_modified->save())
              return false;
          }
        }
      } 

      if($model->save())
        return true;
      else
        return false;
      // print_r($model->getErrors());
      // die('end of noti');
    }

    public static function createSubuser($parent_id, $no_of_subuser){
      for($i=1; $i<= $no_of_subuser; $i++){
        $newSubuser = new User();
        $newSubuser->password = //Removed for confidentiality
        $newSubuser->parent_id = $parent_id;
        $newSubuser->is_requested = 1;
        $newSubuser->status = User::STATUS_REQUESTED;
        $newSubuser->name = //Removed for confidentiality
        $newSubuser->first_name =//Removed for confidentiality
        $newSubuser->last_name = //Removed for confidentiality
        $newSubuser->save(false);
        $newSubuser->assignRole(User::ROLE_ORGANIZATION_USER);                
        $newSubuser->user_type = 5;
        $newSubuser->pending_verification = 0;
        $newSubuser->username = User::ROLE_ORGANIZATION_USER . '-' . $newSubuser->id;
        $newSubuser->sign_up_ip = $_SERVER['REMOTE_ADDR'];
        $newSubuser->save(false);
      }
    }

    public static function deleteUser($user_id = 0){
      $user = User::findOne($user_id);
      if($user)
      {
        switch($user->getRole())
        {
          case User::ROLE_INDIVIDUAL:
              self::propertyImageDelete($user->id);
              self::profileImageDelete($user);
              $user->delete();
              break;
          case User::ROLE_ORGANIZATION:
              self::organizationDelete($user);              
              break;
          case User::ROLE_ORGANIZATION_USER:
              if($user->getProperties()->all() == null && $user->getProperties2()->all() == null)
              {
                self::profileImageDelete($user);
                $user->delete();
              }
              else
              {
                $user->status = USER::STATUS_DELETED;
                $user->save(false);
              }               
              break;
          default:
              break;
        }
      }
    }

    private static function profileImageDelete($user)
    {
      if($user->user_image!="" && 
          file_exists(Yii::getAlias('@backend').'/web/upload/user_image/'.$user->user_image))
      {
          unlink(Yii::getAlias('@backend').'/web/upload/user_image/'.$user->user_image);
      }
    }

    private static function propertyImageDelete($user_id)
    {
      $propertyImages = PropertyImages::find()->where(['user_id' => $user_id])->all();
      foreach ($propertyImages as $propertyImage) {
       if($propertyImage->property_image!="" && 
              file_exists(Yii::getAlias('@backend').'/web/upload/property_images/'.$propertyImage->property_image))
          {
              unlink(Yii::getAlias('@backend').'/web/upload/property_images/'.$propertyImage->property_image);
          }
      }
    }

    private static function organizationDelete($user)
    {
      $organizationSubusers = User::find()->where(['parent_id' => $user->id])->all();
      foreach($organizationSubusers as $subuser){
        self::propertyImageDelete($subuser->id);
        $subuser->delete();
      }
      self::profileImageDelete($user);
      $user->delete();
    }

    public static function getBrowser() 
    { 
      /*$yourbrowser= "Your browser: " . $ua['name'] . " " . $ua['version'] . " on " .$ua['platform'] . " reports: <br >" . $ua['userAgent'];*/
        $u_agent = $_SERVER['HTTP_USER_AGENT']; 
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version= "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        }
        elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        }
        elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }
        
        // Next get the name of the useragent yes seperately and for good reason
        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
        { 
            $bname = 'Internet Explorer'; 
            $ub = "MSIE"; 
        } 
        elseif(preg_match('/Firefox/i',$u_agent)) 
        { 
            $bname = 'Mozilla Firefox'; 
            $ub = "Firefox"; 
        } 
        elseif(preg_match('/Chrome/i',$u_agent)) 
        { 
            $bname = 'Google Chrome'; 
            $ub = "Chrome"; 
        } 
        elseif(preg_match('/Safari/i',$u_agent)) 
        { 
            $bname = 'Apple Safari'; 
            $ub = "Safari"; 
        } 
        elseif(preg_match('/Opera/i',$u_agent)) 
        { 
            $bname = 'Opera'; 
            $ub = "Opera"; 
        } 
        elseif(preg_match('/Netscape/i',$u_agent)) 
        { 
            $bname = 'Netscape'; 
            $ub = "Netscape"; 
        } 
        
        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
        ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }
        
        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
                $version= $matches['version'][0];
            }
            else {
                $version= $matches['version'][1];
            }
        }
        else {
            $version= $matches['version'][0];
        }
        
        // check if we have a number
        if ($version==null || $version=="") {$version="?";}
        
        return array(
            'userAgent' => $u_agent,
            'name'      => $bname,
            'version'   => $version,
            'platform'  => $platform,
            'pattern'    => $pattern
        );
    } 

    public static function getPropertiesMonthWiseTotalCountByCategoryOrPropertyTypeByDate(
      $property_category = null, $property_type =null, $rent_status = 1, $from_date = null, $to_date = null)
    {
        
        $query = Property::find()
                      ->where(['IN', 'property.status', [1, 2, 3]]);
                      // ->andWhere(['DATE_FORMAT(FROM_UNIXTIME(created_at), "%m")'=> date("m", time())]);
        if($from_date == null){
          $from_date = time() - (30*24*60*60);
        }

        if($to_date == null){
          $to_date = time() + (24*60*60);      
        }

        $query->andWhere(['>=', 'created_at', $from_date]);
        $query->andWhere(['<=', 'created_at', $to_date + (24*60*60)]);
              
        if ($property_category != null)
        {
          $query = $query->andWhere(['=', 'property.property_category', $property_category ]);
        } 
        if ($property_type != null)
        {
          $query = $query->andWhere(['=', 'property.property_type', $property_type ]);
        } 
        if ($rent_status == 0)
        {
            $query = $query->andWhere(['=', 'property.rent_status', 0 ]);
        }           
        $properties = $query->count();
        return $properties;
    }

    /**
     * Mobily verification api integration
     */

    public static function phoneMobilyVerificationSms($code, $phone = '//Removed for confidentiality', $register_sms_text = 'ر//Removed for confidentiality')
    {
      
      $register_sms_text = $register_sms_text.$code." ";
      $data_json = '{  
         "Data":{  
            "Method":"msgSend",
            "Params":{  
               "sender":"Waarf",
               "msg":"'.$register_sms_text.'",
               "numbers":"966'.$phone.'",
               "lang":"3",
               "applicationType":"65"
            },
            "Auth":{  
               "mobile"://Removed for confidentiality
               "password"://Removed for confidentiality
            }
         }
      }';
      
      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL => "https://mobily.ws/api/json/",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $data_json,
        CURLOPT_HTTPHEADER => array(
          "Cache-Control: no-cache",
          "Postman-Token: //Removed for confidentiality"
        ),
      ));

      $response = curl_exec($curl);
      $err = curl_error($curl);

      curl_close($curl);

      /*if ($err) {
        echo "cURL Error #:" . $err;
      } else {
        echo $response;
      }*/
    }

    
}
