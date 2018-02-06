<?php
namespace common\components;

use dosamigos\resourcemanager\FileSystemResourceManager;

use yii\helpers\ArrayHelper;
use yii\helpers\BaseFileHelper;


class FileSystemResourseManager extends FileSystemResourceManager
{
    public function save($file, $name, $options = [])
    {
        $folder = ArrayHelper::getValue($options, 'folder');
        $path = $folder
            ? $this->getBasePath() . DIRECTORY_SEPARATOR . $folder
            : $this->getBasePath() . DIRECTORY_SEPARATOR ;
        BaseFileHelper::createDirectory($path);
        return $file->saveAs($path . ltrim($name, DIRECTORY_SEPARATOR));
    }



}