<?php
/**
var array $status;
 *
 */

namespace frontend\widgets;

use yii\base\Widget;
use yii\helpers\ArrayHelper;
use common\entities\mongo\Message_Phone_List;
class StatisticGraph extends Widget
{
    public $model;
    public function run()
    {
        $status=array();
        foreach ($this->model as $model ){
            $status[]=$model->Status();
        }
        $messagesPhoneList=new Message_Phone_List();
        $dataLi="";
        $dataArr=array();
        $countArr=$this->countArr($status,$messagesPhoneList);
        foreach ($countArr as $key =>$value){
            $dataLi.="<li><span class='fa fa-square fa-2x'  style='color: ".$this->fontColor($key,$messagesPhoneList)."'></span><span class='text'>$key <span class='right'>$value</span></li>";
            $dataArr[]=["label"=>$key,"value"=>$this->percent($countArr,$value)];
        }

        return $this->render('statisticGraph',[
            'datali'=>$dataLi,
            'dataArr'=>$dataArr,
            'background'=>$this->color($this->countArr($status),$messagesPhoneList),
            ]);
    }

    private function countArr($arrays){
        $result=[];
        foreach ($arrays as $array){
            $result=ArrayHelper::merge($result,$array);
        }
        return array_count_values ($result);
    }

    private function color($status,$messagesPhoneList){

        $background=array();
        foreach ($status as $key => $val)
            $keys[]=array_search($key,$messagesPhoneList->allStatus());
        if(!empty($keys)){
            foreach ($keys as $statusKey)
                $background[]=$messagesPhoneList->BgColor($statusKey);
        }
        return $background;
    }

    private function fontColor($value,$messagesPhoneList){
        $key=array_search($value,$messagesPhoneList->allStatus());
        if($key)
            return $messagesPhoneList->BgColor($key);
        return false;
    }

    private function percent($sumArr,$val){
        return ($val*100)/array_sum($sumArr);
    }

}