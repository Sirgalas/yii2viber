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
class AdminModerateNotification extends Notification
{
    const KEY_NEW_ACCOUNT = 'new_account';

    /**
     * @var \yii\web\User the user object
     */
    public $user;

    public $message;

    /**
     * @inheritdoc
     */
    public function getTitle()
    {

        return Yii::t('app', 'Нужна модерация рассылки {title}', ['title' => '#'.$this->message->title]);
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
        if ($channel->id === 'adminEmail') {
            return true;
        }
        if ($channel->id === 'telegram') {
            return true;
        }

        return false;
    }

    public function toAdminEmail($channel)
    {

        $subject  = $this->getTitle();
        $template = 'moderateAdminNotification';

        $message = $channel->mailer->compose($template, ['notification' => $this, 'message' => $this->message]);

        Yii::configure($message, $channel->message);
        $message->setTo(Yii::$app->params['notify']['admin']['emails']);
        $message->setSubject($subject);
        $message->send($channel->mailer);
    }
}
