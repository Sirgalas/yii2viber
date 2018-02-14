<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 14.02.2018
 * Time: 10:07
 */

namespace common\services;
use Yii;
use common\components\Viber;
use common\entities\ViberMessage;

class ViberCronHandler
{
    const VIBER_TIME_LIMIT =10;
    private $time_stop;

    /**
     * ViberCronHandler constructor.
     *
     * @param $time_stop
     */
    public function __construct()
    {
        $this->time_stop = time() + self::VIBER_TIME_LIMIT;
    }

    public function handle(){
        while ($this->time_stop > time()){
            $vm='';
            $vm = ViberMessage::find()->isProcess()->one();

            if (!$vm){
                echo " Vm for process not found\n";
                $vm = ViberMessage::find()->isNew()->one();
                if (!$vm){
                    echo 'Нечего отправлять!';
                    return;
                }
                $v=new Viber($vm);
                $v->prepareTransaction();
            }
            if ($vm){
                $v=new Viber($vm);
                $v->sendMessage();
            }
            sleep(Yii::$app->params['viber']['min_delay']);
        }
    }
}