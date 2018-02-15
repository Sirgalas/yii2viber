<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 15.02.2018
 * Time: 12:48
 */

namespace frontend\forms;

use common\entities\ViberMessage;
use yii\base\Model;
class ViberTestForm extends Model
{
    public $title;

    public $text;

    public $image;

    public $title_button;

    public $url_button;

    public $type;

    public $alpha_name;

    public $time_start;
    public $date_start;

    public $just_now;

    public $upload_file;

    public $phone1;

    public $phone2;

    public $phone3;

    public function rules()
    {
        return [
            [['phone1', 'phone2', 'phone3'], 'integer'],
            [
                ['phone1', 'phone2', 'phone3'],
                function ($attribute, $params) {
                    if (strlen(''.$this->$attribute) !== 1) {
                        $this->addError($attribute, 'Неправильная длина номера');
                    }
                },
            ],
            [['title'], 'required'],
            [['title'], 'string', 'max' => 50],
            [['text'], 'string', 'max' => 120],
            [['image', 'url_button'], 'string', 'max' => 255],
            [
                ['upload_file'],
                'file',
                'skipOnEmpty' => true,
                'extensions' => 'jpg, png, gif',
                'mimeTypes' => 'image/jpeg, image/png, image/gif',
            ],
            [['title_button', 'alpha_name'], 'string', 'max' => 32],
            ['type', 'in', 'range' => array_keys(ViberMessage::listTypes())],
            [['time_start'], 'time', 'format' => 'php:H:i'],
            [['date_start'], 'date', 'format' => 'php:Y-m-d'],
            ['just_now', 'boolean'],
            [['time_start'], 'compare', 'compareValue' => date('H:i'), 'operator' => '>=', 'type' => 'time'],
            [['date_start'], 'compare', 'compareValue' => date('Y-m-d'), 'operator' => '>=', 'type' => 'date'],

        ];
    }

    public function attributeLabels()
    {
        return [

            'title' => 'Название рассылки',
            'text' => 'Поле для ввода техта SMS',
            'image' => 'Изображение',
            'title_button' => 'Текст на кнопке',
            'url_button' => 'Url по кнопке',
            'type' => 'Тип сообщения',
            'alpha_name' => 'Отправитель',
            'time_start' => 'Время отправки',
            'upload_file' => 'Картинка',
            'just_now' => 'Прямо сейчас',

        ];
    }
}