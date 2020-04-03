<?php

/**
 * Date: 28.03.19
 * Time: 10:39
 */

namespace mihaildev\elfinder\volume;

use Yii;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;

/**
 * @property array defaults
 */
class Base extends BaseObject
{
    /**
     * @var string
     */
	public $driver = 'LocalFileSystem';

    /**
     * @var string
     */
	public $name = 'Root';

    /**
     * @var array
     */
	public $options = [];

    /**
     * @var array
     */
	public $access = ['read' => '*', 'write' => '*'];

    /**
     * @var string
     */
	public $tmbPath;

    /**
     * @var array
     */
	public $plugin = [];

    /**
     * @var array
     */
	private $_defaults;

    /**
     * @return string
     */
	public function getAlias()
    {
		if (is_array($this->name)) {
			return Yii::t($this->name['category'], $this->name['message']);
		}

		return $this->name;
	}

    /**
     * @return bool
     */
	public function isAvailable()
    {
		return $this->defaults['read'];
	}

    /**
     * @return array
     */
	public function getDefaults()
    {
		if ($this->_defaults !== null)
			return $this->_defaults;

		$this->_defaults['read'] = false;
		$this->_defaults['write'] = false;

		if (isset($this->access['write'])) {
			$this->_defaults['write'] = true;
			if ($this->access['write'] != '*') {
				$this->_defaults['write'] = Yii::$app->user->can($this->access['write']);
			}
		}

		if ($this->_defaults['write']) {
			$this->_defaults['read'] = true;
		} elseif (isset($this->access['read'])) {
			$this->_defaults['read'] = true;
			if ($this->access['read'] != '*') {
				$this->_defaults['read'] = Yii::$app->user->can($this->access['read']);
			}
		}

		return $this->_defaults;
	}

    /**
     * @param array $options
     * @return array
     */
    protected function optionsModifier($options)
    {
        return $options;
    }

    /**
     * @return array
     */
	public function getRoot()
    {
		$options['driver'] = $this->driver;
		$options['plugin'] = $this->plugin;
		$options['defaults'] = $this->getDefaults();
		$options['alias'] = $this->getAlias();

		$options['tmpPath'] = Yii::getAlias('@runtime/elFinderTmpPath');
		if (!empty($this->tmbPath)) {
			$this->tmbPath = trim($this->tmbPath, '/');
			$options['tmbPath'] = Yii::getAlias('@webroot/' . $this->tmbPath);
			$options['tmbURL'] = Yii::$app->request->hostInfo . Yii::getAlias('@web/'.$this->tmbPath);
		}else{
			$subPath = md5($this->className() . '|' . serialize($this->name));
			$options['tmbPath'] = Yii::$app->assetManager->getPublishedPath(__DIR__) . DIRECTORY_SEPARATOR . $subPath;
			$options['tmbURL'] = Yii::$app->request->hostInfo . Yii::$app->assetManager->getPublishedUrl(__DIR__) . '/' . $subPath;
		}

		FileHelper::createDirectory($options['tmbPath']);


		$options['mimeDetect'] = 'internal';
		$options['imgLib'] = 'auto';
		$options['attributes'][] = [
			'pattern' => '#.*(\.tmb|\.quarantine)$#i',
			'read' => false,
			'write' => false,
			'hidden' => true,
			'locked' => false,
		];

        $options = $this->optionsModifier($options);

		return ArrayHelper::merge($options, $this->options);
	}
}
