<?php
/**
 * Date: 07.01.2017
 * Time: 0:14
 */

namespace mihaildev\elfinder\plugin;


use mihaildev\elfinder\PluginInterface;
use yii\helpers\Inflector;

class Sluggable extends PluginInterface
{
    public $bind = [
        'upload.presave' => 'createSlug'
    ];

    public $lowercase = true;

    public $replacement = '-';

    /**
     * @return string
     */
    public function getName()
    {
        return 'Sluggable';
    }

    public function createSlug(&$path, &$name, $src, $elfinder, $volume){
        if(!$this->isEnable($volume))
            return false;

        $ext = pathinfo($name, PATHINFO_EXTENSION);
        $filename = pathinfo($name, PATHINFO_FILENAME);

        $lowercase = $this->getOption('lowercase', $volume);

        $replacement = $this->getOption('replacement', $volume);

        $filename = Inflector::slug($filename, $replacement, $lowercase);

        if($lowercase)
            $ext = strtolower($ext);

        $name = empty($ext) ? $filename : $filename . "." . $ext;

        return true;
    }
}