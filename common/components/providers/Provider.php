<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 28.02.2018
 * Time: 23:48
 */

namespace common\components\providers;

abstract class Provider
{
    protected $from;

    protected $type;

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
     * @param $from
     * @param $type_message
     * @param $text
     * @param $title_button
     * @param $url_button
     * @param $image
     * @param $params
     * @param $image_id
     */
    public function __construct(

        $from,
        $params,
        $type_message,
        $text,
        $title_button = '',
        $url_button = '',
        $image = '',
        $image_id = 0
    ) {

        $this->from = $from;
        $this->type = $type_message;
        $this->text = $text;
        $this->title_button = $title_button;
        $this->url_button = $url_button;
        $this->image = $image;
        $this->params = $params;
        $this->image_id = $image_id;
    }


    /**
     * отправка запроса на отправку данных провайдеру
     * @param $phones
     * @param $transaction_id
     * @return mixed (в штатном режиме xml)
     */
    abstract function sendToViber($phones, $transaction_id);

    /**
     * Разбираем ответ провайдера и меняем статусы телефона
     *
     * @param $xml
     * @param $phonesArray - массив [Message_Phone_list]
     * @return bool
     */
    abstract function parseSendResult($xml, $phonesArray);


}