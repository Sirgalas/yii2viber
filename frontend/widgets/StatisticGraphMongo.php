<?php
namespace frontend\widgets;

use yii\base\Widget;
use yii\helpers\ArrayHelper;
use common\entities\mongo\Message_Phone_List;
class StatisticGraphMongo extends Widget
{
    public $model;
    public function run()
    {
        $messageModel=new Message_Phone_List();
        $dataLi='';
        $dataArr=array();
        $background=array();
        $countArr=$this->countArr($this->model);
        foreach ($countArr as $key =>$value){
            $dataLi.="<li><span class='fa fa-square fa-2x'  style='color: ".$messageModel->BgColor($key)."'></span><span class='text'>".$messageModel->statusMessage($key)."</span> <span class='right'>$value</span></li>";
            $dataArr[]=["label"=>$messageModel->statusMessage($key),"value"=>$this->percent($countArr,$value)];
            $background[]=$messageModel->BgColor($key);
        }

        return $this->render('statisticGraph',[
            'datali'=>$dataLi,
            'dataArr'=>$dataArr,
            'background'=>$background,
            ]);
    }

    private function countArr($arrays){
        $result=[];
        foreach ($arrays as $array){
            $result[]=$array->status;
        }
        return array_count_values ($result);
    }

    private function percent($sumArr,$val){
        return round(($val*100)/array_sum($sumArr));
    }
    





}