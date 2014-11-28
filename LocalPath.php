<?php
/**
 * Date: 23.01.14
 * Time: 22:47
 */

namespace mihaildev\elfinder;

use Yii;

class LocalPath extends BasePath{
    public $path;
    public $baseUrl = '@web';
    public $basePath = '@webroot';

    public $name = 'Root';

    public $options = [];

    public $access = ['read' => '*', 'write' => '*'];

    public function getUrl(){
        return Yii::getAlias($this->baseUrl.'/'.trim($this->path,'/'));
    }

    public function getRealPath(){
        $path = Yii::getAlias($this->basePath.'/'.trim($this->path,'/'));
        if(!is_dir($path))
            mkdir($path, 0777, true);

        return $path;
    }

    public function getRoot(){
        $options['driver'] = $this->driver;
        $options['path'] = $this->getRealPath();
        $options['URL'] = $this->getUrl();
        $options['defaults'] = $this->getDefaults();
        $options['alias'] = $this->getAlias();
        $options['mimeDetect'] = 'internal';
        //$options['onlyMimes'] = ['image'];
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