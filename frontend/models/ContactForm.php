<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\ContactMessages;

/**
 * ContactForm is the model behind the contact form.
 */
class ContactForm extends ContactMessages
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['email', 'messages'], 'string'],
            [['email', 'messages', 'name'], 'required'],
            ['email','email'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
 
    /**
     * Sends an email to the specified email address using the information collected by this model.
     *
     * @param string $email the target email address
     * @return boolean whether the email was sent
     */
   /* public function sendEmail($email)
    {
        return Yii::$app->mailer->compose()
            ->setTo($email)
            ->setFrom([$this->email => $this->name])
            ->setCc([$setting->contact_email, 'pritam@technoexponent.com'])
            ->setSubject($this->subject)
            ->setTextBody($this->body)
            ->send();
    }*/
}
