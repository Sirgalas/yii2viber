<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 28.02.2018
 * Time: 23:48
 */

namespace common\components\providers;

use common\entities\ViberMessage;

abstract class Provider
{
    protected $from;

    protected $type;
    protected $message_type;

    protected $text;

    protected $title_button;

    protected $url_button;

    protected $image;

    protected $params;

    public $image_id;

    public $viberQuery;

    public $debug = true;

    /**
     * Viber constructor.
     *
     * @param $params array
     */
    public function __construct($params)
    {
        $this->params = $params;
    }

    public function setMessage(ViberMessage $viberMessage

    ) {
        $this->from = $viberMessage->from;
        $this->type = $viberMessage->type;
        $this->message_type = $viberMessage->message_type;
        $this->text = $viberMessage->text;
        $this->title_button = $viberMessage->title_button;
        $this->url_button = $viberMessage->url_button;
        $this->image = $viberMessage->image;
        $this->image_id = $viberMessage->image_id;
    }

    /**
     * отправка запроса на отправку данных провайдеру
     *
     * @param $phones
     * @param $transaction_id
     * @return mixed (в штатном режиме xml)
     */
    abstract public function sendToViber($phones, $transaction_id);

    /**
     * Разбираем ответ провайдера и меняем статусы телефона
     *
     * @param $xml
     * @param $phonesArray - массив [Message_Phone_list]
     * @return bool
     */
    abstract public function parseSendResult($xml, $phonesArray);
}