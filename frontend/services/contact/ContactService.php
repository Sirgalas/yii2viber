<?php


namespace frontend\services\contact;

use Yii;
use yii\mail\MailerInterface;
use frontend\forms\ContactForm;
class ContactService
{

    private $suportEmail;

    public function __construct($suportEmail/*,MailerInterface $mailer*/)
    {
        $this->$suportEmail=$suportEmail;

    }

    public function send(ContactForm $form): void
    {
        $sent=Yii::$app->mailer->compose()
            ->setTo($this->suportEmail)
            ->setSubject($this->subject)
            ->setTextBody($this->body)
            ->send();
        if (!$sent) {
            throw new \RuntimeException('Sending error.');
        }
    }

}