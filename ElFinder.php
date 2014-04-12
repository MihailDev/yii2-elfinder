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

    public $containerOptions = [];
    public $frameOptions = [];
    public $controller = 'elfinder';

    public static function getManagerUrl($controller, $params = [])
    {
		$params[0] = '/'.$controller."/manager";
        return Yii::$app->urlManager->createUrl($params);
    }

    public static function ckeditorOptions($controller, $options = []){
        return ArrayHelper::merge([
            'filebrowserBrowseUrl' => self::getManagerUrl($controller),
            'filebrowserImageBrowseUrl' => self::getManagerUrl($controller, ['filter'=>'image']),
            'filebrowserFlashBrowseUrl' => self::getManagerUrl($controller, ['filter'=>'flash']),
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

        $managerOptions['lang'] = $this->language;

        $this->frameOptions['src'] = $this->getManagerUrl($this->controller, $managerOptions);

        if(!isset($this->frameOptions['style'])){
            $this->frameOptions['style'] = "width: 100%; height: 100%; border: 0;";
        }
    }

    static function getSupportedLanguage($languge)
    {
        $supportedLangs = array('bg', 'jp', 'sk', 'cs', 'ko', 'th', 'de', 'lv', 'tr', 'el', 'nl', 'uk',
            'es', 'no', 'vi', 'fr', 'pl', 'zh_CN', 'hr', 'pt_BR', 'zh_TW', 'hu', 'ro', 'it', 'ru');

        if(!in_array($languge, $supportedLangs)){
            if (strpos($languge, '_')) {
                $languge = substr($languge, 0, strpos($languge, '_'));
                if (!in_array($languge, $supportedLangs)) $languge = false;
            } else {
                $languge = false;
            }
        }

        return $languge;
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
            $this->getView()->registerJs("ElFinderFileCallback.register(".Json::encode($this->id).",".Json::encode($this->callbackFunction).");");
        }
    }

} 