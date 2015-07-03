<?php
/**
 * Date: 22.01.14
 * Time: 23:44
 */

namespace mihaildev\elfinder;

use Yii;
use yii\base\Widget as BaseWidjet;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * Class Widget
 * @package mihaildev\elfinder
 */

class ElFinder extends BaseWidjet{

	public $language;

	public $filter;

	public $callbackFunction;

	public $path;// work with PathController

	public $containerOptions = [];
	public $frameOptions = [];
	public $controller = 'elfinder';

	/**
	 * Generate URL hash for start dir
	 * @param string $path relative start dir, begin with `@x/` to define start root index.
	 * @param int $volume start volume, default to parse from `$path` or 1. 
	 * @return string
	 */
	public static function genElfHash($path, $volume = null)
	{
		// valid patterns: @1, @2/subdir, @1/sub1/sub2 ...
		if($volume === null){
			if(preg_match('/^@(\d+)/', $path, $match)){
				$volume = $match[1];
				$path = ltrim(substr($path, strlen($match[0])), '/');
				if(empty($path)){
					$path = '/';
				}
			}else{
				$volume = 1;
			}
		}
		$hash = rtrim(strtr(base64_encode($path), '+/=', '-_.'), '.');
		return 'elf_l' . intval($volume) . '_' . $hash;
	}

	public static function getManagerUrl($controller, $params = [])
	{
		$params[0] = '/'.$controller."/manager";
		if(isset($params['path']) && !isset($params['#'])){
			$params['#'] = self::genElfHash($params['path']);
			unset($params['path']);
		}
		return Yii::$app->urlManager->createUrl($params);
	}

	public static function ckeditorOptions($controller, $options = []){

		if(is_array($controller)){
			$id = $controller[0];
			unset($controller[0]);
			$params = $controller;
		}else{
			$id = $controller;
			$params = [];
		}

		return ArrayHelper::merge([
			'filebrowserBrowseUrl' => self::getManagerUrl($id, $params),
			'filebrowserImageBrowseUrl' => self::getManagerUrl($id, ArrayHelper::merge($params, ['filter'=>'image'])),
			'filebrowserFlashBrowseUrl' => self::getManagerUrl($id, ArrayHelper::merge($params, ['filter'=>'flash'])),
		], $options);
	}

	public function init()
	{
		if(empty($this->language))
			$this->language = self::getSupportedLanguage(Yii::$app->language);

		$managerOptions = [];
		if(!empty($this->filter))
			$managerOptions['filter'] = $this->filter;

		if(!empty($this->callbackFunction))
			$managerOptions['callback'] = $this->id;

		if(!empty($this->language))
			$managerOptions['lang'] = $this->language;

		if(!empty($this->path))
			$managerOptions['path'] = $this->path;

		$this->frameOptions['src'] = $this->getManagerUrl($this->controller, $managerOptions);

		if(!isset($this->frameOptions['style'])){
			$this->frameOptions['style'] = "width: 100%; height: 100%; border: 0;";
		}
	}

	static function getSupportedLanguage($language)
	{
		$supportedLanguages = array('bg', 'jp', 'sk', 'cs', 'ko', 'th', 'de', 'lv', 'tr', 'el', 'nl', 'uk',
			'es', 'no', 'vi', 'fr', 'pl', 'zh_CN', 'hr', 'pt_BR', 'zh_TW', 'hu', 'ro', 'it', 'ru', 'en');

		if(!in_array($language, $supportedLanguages)){
			if (strpos($language, '-')){
				$language = str_replace('-', '_', $language);
				if(!in_array($language, $supportedLanguages)) {
					$language = substr($language, 0, strpos($language, '_'));
					if (!in_array($language, $supportedLanguages))
						$language = false;
				}
			} else {
				$language = false;
			}
		}

		return $language;
	}

	public function run()
	{
		$container = 'div';
		if(isset($this->containerOptions['tag'])){
			$container = $this->containerOptions['tag'];
			unset($this->containerOptions['tag']);
		}

		echo Html::tag($container, Html::tag('iframe','', $this->frameOptions), $this->containerOptions);

		if(!empty($this->callbackFunction)){
			AssetsCallBack::register($this->getView());
			$this->getView()->registerJs("mihaildev.elFinder.register(".Json::encode($this->id).",".Json::encode($this->callbackFunction).");");
		}
	}
}
