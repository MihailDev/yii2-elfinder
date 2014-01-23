<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 20.01.14
 * Time: 13:16
 */

namespace mihaildev\elfinder;

use Yii;
use yii\base\Component as BaseComponent;
use yii\helpers\ArrayHelper;

/**
 * @property array $options
 */

class Component extends BaseComponent{
    public $id = 'elfinder';
    public $controllerName = '';
    public $roots = [];
    public $access = '*';
    public $disabledCommands = ['netmount'];

    public function init()
    {
        Yii::$app->controllerMap[$this->controllerName] = 'mihaildev\elfinder\Controller';
    }

    public function isAvailable(){
        if($this->access == '*' || Yii::$app->user->checkAccess($this->access)){
            if(!empty($this->options['roots']))
                return true;
        }

        return false;
    }

    private $_options;

    public function getOptions()
    {
        if($this->_options !== null)
            return $this->_options;

        $this->_options['roots'] = [];

        foreach($this->roots as $root){
            if(!isset($root['class']))
                $root['class'] = 'mihaildev\elfinder\Path';

            $root = Yii::createObject($root);

            /** @var \mihaildev\elfinder\Path $root*/

            if($root->isAvailable())
                $this->_options['roots'][] = $root->getRoot();
        }



        return $this->_options;
    }

    public function getUrlConnect()
    {
        return Yii::$app->urlManager->createUrl('/'.$this->controllerName."/connect");
    }

    public function getUrlManager($params = [])
    {
        return Yii::$app->urlManager->createUrl('/'.$this->controllerName."/manager", $params);
    }

    public function ckeditorOptions($options = []){
        return ArrayHelper::merge([
            'filebrowserBrowseUrl' => $this->getUrlManager(),
            'filebrowserImageBrowseUrl' => $this->getUrlManager(['filter'=>'image']),
            'filebrowserFlashBrowseUrl' => $this->getUrlManager(['filter'=>'flash']),
        ], $options);
    }
} 