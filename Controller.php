<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 20.01.14
 * Time: 13:26
 */

namespace mihaildev\elfinder;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller as BaseController;
use yii\web\JsExpression;



/**
 * Class Controller
 * @package mihaildev\elfinder
 * @property array $options
 */


class Controller extends BaseController{
    public $roots = [];
    public $access = ['@'];
    public $disabledCommands = ['netmount'];
	public $watermark;

    public function behaviors()
    {
      return [
        'access' => [
          'class' => AccessControl::className(),
          'rules' => [
            [
              'allow' => true,
              'roles' => $this->access,
            ],
          ],
        ],
      ];
    }


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

        return $this->_options;
    }

    public function actionConnect(){
       return $this->renderFile(__DIR__."/views/connect.php", ['options'=>$this->getOptions()]);
    }

    public function actionManager(){

        $options = [
            'url'=> Url::toRoute('connect'),
            'customData' => [
                Yii::$app->request->csrfParam => Yii::$app->request->csrfToken
            ],
            'resizable' => false
        ];

        if(isset($_GET['CKEditor'])){
            $options['getFileCallback'] = new JsExpression('function(file){ '.
            'window.opener.CKEDITOR.tools.callFunction('.Json::encode($_GET['CKEditorFuncNum']).', file.url); '.
            'window.close(); }');

            $options['lang'] = $_GET['langCode'];
        }

        if(isset($_GET['filter'])){
            if(is_array($_GET['filter']))
                $options['onlyMimes'] = $_GET['filter'];
            else
                $options['onlyMimes'] = [$_GET['filter']];
        }

        if(isset($_GET['lang']))
            $options['lang'] = $_GET['lang'];

        if(isset($_GET['callback'])){
            if(isset($_GET['multiple']))
                $options['commandsOptions']['getfile']['multiple'] = true;
            
            $options['getFileCallback'] = new JsExpression('function(file){ '.
                'if (window!=window.top) {var parent = window.parent;}else{var parent = window.opener;}'.
                'if(parent.mihaildev.elFinder.callFunction('.Json::encode($_GET['callback']).', file))'.
                'window.close(); }');
        }

        if(!isset($options['lang']))
            $options['lang'] = Yii::$app->language;

        if(!empty($this->disabledCommands))
            $options['commands'] = new JsExpression('ElFinderGetCommands('.Json::encode($this->disabledCommands).')');


        return $this->renderFile(__DIR__."/views/manager.php", ['options'=>$options]);
    }

} 
