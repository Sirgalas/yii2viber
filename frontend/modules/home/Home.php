<?php

namespace frontend\modules\home;

/**
 * home module definition class
 */
class Home extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'frontend\modules\home\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->layout='main';
        parent::init();

        // custom initialization code goes here
    }
}
