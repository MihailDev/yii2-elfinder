<?php
/**
 * Date: 22.01.14
 * Time: 14:13
 */

namespace mihaildev\elfinder;

use Yii;

class UserPath extends LocalPath{

    public function isAvailable(){
        if(Yii::$app->user->isGuest)
            return false;

        return parent::isAvailable();
    }

    public function getUrl(){
        $path = strtr($this->path, ['{id}'=>Yii::$app->user->id]);
        return Yii::getAlias('@web/'.trim($path,'/'));
    }

    public function getRealPath(){
        $path = strtr($this->path, ['{id}'=>Yii::$app->user->id]);
        $path = Yii::getAlias('@webroot/'.trim($path,'/'));
        if(!is_dir($path))
            mkdir($path, 0777, true);

        return $path;
    }
}