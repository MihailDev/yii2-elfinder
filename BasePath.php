<?php
/**
 * Date: 22.01.14
 * Time: 10:39
 */

namespace mihaildev\elfinder;

use Yii;
use yii\base\Component as BaseComponent;


/**
 * @property array defaults
 */
class BasePath extends BaseComponent{

    public $driver = 'LocalFileSystem';

    public $name = 'Root';

    public $options = [];

    public $access = ['read' => '*', 'write' => '*'];

	public $tmbPath = '.tmb';

    public function getAlias(){
        if(is_array($this->name)){
            return Yii::t($this->name['category'], $this->name['message']);
        }

        return $this->name;
    }

    public function isAvailable(){
        return $this->defaults['read'];
    }

    private $_defaults;

    public function getDefaults(){
        if($this->_defaults !== null)
            return $this->_defaults;
        $this->_defaults['read'] = false;
        $this->_defaults['write'] = false;

        if(isset($this->access['write'])){
            $this->_defaults['write'] = true;
            if($this->access['write'] != '*'){
                $this->_defaults['write'] = Yii::$app->user->can($this->access['write']);
            }
        }

        if($this->_defaults['write']){
            $this->_defaults['read'] = true;
        }elseif(isset($this->access['read'])){
            $this->_defaults['read'] = true;
            if($this->access['read'] != '*'){
                $this->_defaults['read'] = Yii::$app->user->can($this->access['read']);
            }
        }

        return $this->_defaults;
    }

    public function getRoot(){
        $options['driver'] = $this->driver;
        $options['defaults'] = $this->getDefaults();
        $options['alias'] = $this->getAlias();

		$options['tmpPath'] = Yii::getAlias('@runtime/elFinderTmpPath');
		$options['tmbPath'] = $this->tmbPath;

        $options['mimeDetect'] = 'internal';
        $options['imgLib'] = 'auto';
        $options['attributes'][] = [
            'pattern' => '#.*(\.tmb|\.quarantine)$#i',
            'read' => false,
            'write' => false,
            'hidden' => true,
            'locked' => false
        ];

		//var_export($options);exit;

        return \yii\helpers\ArrayHelper::merge($options, $this->options);
    }



} 