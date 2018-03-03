<?php

namespace common\components;

use yii\base\Component;
use common\entities\Config;
class ConfigСomponent extends Component
{
    protected $data = array();

    public function init()
    {
        $items = Config::find()->all();
        foreach ($items as $item){
            $param=$item->text;
            $this->data[$item->param] = $param;
        }
        parent::init();
    }

    public function get($key)
    {
        if (array_key_exists($key, $this->data)){
            return $this->data[$key];
        } else {
            return false;
        }
    }

}