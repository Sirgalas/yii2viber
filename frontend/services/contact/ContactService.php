<?php


namespace services\contact;


class ContactService
{

    private $suportEmail;
    private $adminEmail;

    public function __construct($suportEmail,$adminEmail)
    {
        $this->$suportEmail=$suportEmail;
        $this->adminEmail=$adminEmail;
    }

    public function send($email)
    {
        return Yii::$app->mailer->compose()
            ->setTo($this->suportEmail)
            ->setFrom([$this->adminEmail])
            ->setSubject($this->subject)
            ->setTextBody($this->body)
            ->send();
    }

}