<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 09.03.2018
 * Time: 15:44
 */

namespace common\notifications;

use Yii;
use webzop\notifications\Notification;

/**
 *
 * @property array $route
 * @property mixed $title
 */
class ClientRejectMessageNotification extends Notification
{
    const KEY_NEW_ACCOUNT    = 'new_account';


    /**
     * @var \yii\web\User the user object
     */
    public $user;

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        switch ($this->key) {
            case self::KEY_NEW_ACCOUNT:
                return Yii::t('app', 'New account {user} created', ['user' => '#'. $this->user->username]);

        }
    }

    /**
     * @inheritdoc
     */
    public function getRoute()
    {
        return ['/users/edit', 'id' => $this->user->id];
    }

    public function shouldSend($channel)
    {
        if ($channel->id ==='adminEmail'){
            return true;
        }
        if ($channel->id ==='telegram'){
            return true;
        }
        return false;
    }
    /**
     * Override send to email channel
     *
     * @param $channel the email channel
     * @return void
     */
    public function toEmail($channel){
        switch($this->key){
            case self::KEY_NEW_ACCOUNT:
                $subject = 'Welcome to MySite';
                $template = 'testNotification';
                break;
            case self::KEY_RESET_PASSWORD:
                $subject = 'Password reset for MySite';
                $template = 'testNotification';
                break;
        }

        $message = $channel->mailer->compose($template, [
            'user' => Yii::$app->user->identity,
            'notification' => $this,
            'user'=>$this->user
        ]);
        Yii::configure($message, $channel->message);

        $message->setTo($this->user->email);
        $message->setSubject($subject);
        $message->send($channel->mailer);
    }

    public function toAdminEmail($channel){
        switch($this->key){
            case self::KEY_NEW_ACCOUNT:
                $subject = 'User registered';
                $template = '/mail/registerAdminNotification';
                break;

        }
        $message = $channel->mailer->compose($template, [
            'notification' => $this,
            'user'=>$this->user
        ]);
        Yii::configure($message, $channel->message);
        $message->setTo(Yii::$app->params['notify']['admin']['emails']);
        $message->setSubject($subject);
        $message->send($channel->mailer);
    }
}
