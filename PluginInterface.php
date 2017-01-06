<?php
/**
 * Date: 05.01.2017
 * Time: 20:10
 */

namespace mihaildev\elfinder;


abstract class PluginInterface
{
    /**
     * @return string
     */
    abstract public function getName();

    public $bind = [];

    public $enable = true;

    /**
     * @param $name string
     * @param $volume \elFinderVolumeDriver
     * @return mixed
     */
    protected function getOption($name, $volume) {
        if (is_object($volume)) {
            $volumeOptions = $volume->getOptionsPlugin($this->getName());
            if (isset($volumeOptions[$name])) {
                return $volumeOptions[$name];
            }
        }
        return $this->$name;
    }

    /**
     * @param $volume \elFinderVolumeDriver
     * @return bool
     */
    public function isEnable($volume){
        return $this->getOption('enable', $volume);
    }
}