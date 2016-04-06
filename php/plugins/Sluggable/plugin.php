<?php
/**
 * elFinder Plugin Sluggable automatically fills the specified attribute with a value that can be used a slug in a URL.
 * 
 *
 * ex. binding, configure on connector options
 *	$opts = array(
 *		'bind' => array(
 *			'mkdir.pre mkfile.pre rename.pre archive.pre' => array(
 *				'Plugin.Sluggable.cmdPreprocess'
 *			),
 *			'upload.presave' => array(
 *				'Plugin.Sluggable.onUpLoadPreSave'
 *			)
 *		),
 *		// global configure (optional)
 *		'plugin' => array(
 *			'Sluggable' => array(
 *				'enable' => true,
 *				'replacement'    => '-',
 *				'lowercase'   => true
 *			)
 *		),
 *		// each volume configure (optional)
 *		'roots' => array(
 *			array(
 *				'driver' => 'LocalFileSystem',
 *				'path'   => '/path/to/files/',
 *				'URL'    => 'http://localhost/to/files/'
 *				'plugin' => array(
 *					'Sluggable' => array(
 *						'enable' => true,
 *						'replacement'    => '-',
 *						'lowercase'   => true
 *					)
 *				)
 *			)
 *		)
 *	);
 *
 * @package elfinder
 */
class elFinderPluginSluggable
{
	private $opts = array();
	
	public function __construct($opts) {
		$defaults = array(
			'enable' => true, // For control by volume driver
			'replacement'    => '-',
			'lowercase'   => true
		);
	
		$this->opts = array_merge($defaults, $opts);
	}
	
	public function cmdPreprocess($cmd, &$args, $elfinder, $volume) {
		$opts = $this->getOpts($volume);
		if (! $opts['enable']) {
			return false;
		}
		
		if (isset($args['name'])) {
			$args['name'] = $this->slug($args['name'], $opts);
		}
		return true;
	}
	
	public function onUpLoadPreSave(&$path, &$name, $src, $elfinder, $volume) {
		$opts = $this->getOpts($volume);
		if (! $opts['enable']) {
			return false;
		}
		
		if ($path) {
			$path = $this->slug($path, $opts);
		}
		$name = $this->slug($name, $opts);
		return true;
	}

	/**
	 * @param elFinderVolumeDriver $volume
	 * @return array
	 */
	private function getOpts($volume) {
		$opts = $this->opts;
		if (is_object($volume)) {
			$volOpts = $volume->getOptionsPlugin('Normalizer');
			if (is_array($volOpts)) {
				$opts = array_merge($this->opts, $volOpts);
			}
		}
		return $opts;
	}
	
	private function slug($str, $opts) {
		$ext = strtolower(pathinfo($str,PATHINFO_EXTENSION));
		return \yii\helpers\Inflector::slug(pathinfo($str,PATHINFO_FILENAME), $opts['replacement'], $opts['lowercase']).(empty($ext)?"":".".$ext);
	}
}
