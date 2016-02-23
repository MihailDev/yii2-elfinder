<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 20.01.14
 * Time: 13:26
 */

namespace mihaildev\elfinder;

use Yii;
use yii\helpers\ArrayHelper;



/**
 * Class Controller
 * @package mihaildev\elfinder
 * @property array $options
 */


class Controller extends BaseController{
	public $roots = [];
	public $disabledCommands = ['netmount'];
	public $watermark;
	public $plugins;

	private $_options;

	public function getOptions()
	{
		if($this->_options !== null)
			return $this->_options;

		$this->_options['roots'] = [];

		foreach($this->roots as $root){
			if(is_string($root))
				$root = ['path' => $root];

			if(!isset($root['class']))
				$root['class'] = 'mihaildev\elfinder\LocalPath';

			$root = Yii::createObject($root);

			/** @var \mihaildev\elfinder\LocalPath $root*/

			if($root->isAvailable())
				$this->_options['roots'][] = $root->getRoot();
		}

		if(!empty($this->watermark)){
			$this->_options['bind']['upload.presave'] = 'Plugin.Watermark.onUpLoadPreSave';

			if(is_string($this->watermark)){
				$watermark = [
					'source' => $this->watermark
				];
			}else{
				$watermark = $this->watermark;
			}

			$this->_options['plugin']['Watermark'] = $watermark;
		}
		
		if( !empty( $this->plugins ) ){
	            foreach( $this->plugins as $pluginName=>$plugin ){
	                $this->_options['bind']['upload.presave'][$pluginName] = 'Plugin.'.$pluginName.'.onUpLoadPreSave';
	                $this->_options['plugin'][$pluginName] = $plugin;
	            }
	        }

		$this->_options = ArrayHelper::merge($this->_options, $this->connectOptions);

		return $this->_options;
	}
}
