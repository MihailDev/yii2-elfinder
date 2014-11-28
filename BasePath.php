<?php
/**
 * Date: 22.01.14
 * Time: 10:39
 */

namespace mihaildev\elfinder;

use Yii;
use yii\base\Component as BaseComponent;


class BasePath extends BaseComponent{

    public $driver = 'LocalFileSystem';

    public $name = 'Root';

    public $options = [];

    public $access = ['read' => '*', 'write' => '*'];

    public function getAlias(){
        if(is_array($this->name)){
            return Yii::t($this->name['category'], $this->name['message']);
        }

        return $this->name;
    }

    public function isAvailable(){
        return $this->defaults['read'];
    }

    private $_defults;

    public function getDefaults(){
        if($this->_defults !== null)
            return $this->_defults;
        $this->_defults['read'] = false;
        $this->_defults['write'] = false;

        if(isset($this->access['write'])){
            $this->_defults['write'] = true;
            if($this->access['write'] != '*'){
                $this->_defults['write'] = Yii::$app->user->can($this->access['write']);
            }
        }

        if($this->_defults['write']){
            $this->_defults['read'] = true;
        }elseif(isset($this->access['read'])){
            $this->_defults['read'] = true;
            if($this->access['read'] != '*'){
                $this->_defults['read'] = Yii::$app->user->can($this->access['read']);
            }
        }

        return $this->_defults;
    }

    public function getRoot(){
        $options['driver'] = $this->driver;
        $options['defaults'] = $this->getDefaults();
        $options['alias'] = $this->getAlias();
        $options['mimeDetect'] = 'internal';
        $options['imgLib'] = 'gd';
        $options['attributes'][] = [
            'pattern' => '#.*(\.tmb|\.quarantine)$#i',
            'read' => false,
            'write' => false,
            'hidden' => true,
            'locked' => false
        ];

        return \yii\helpers\ArrayHelper::merge($options, $this->options);
    }



} 