<?php
/**
 * Date: 12.05.15
 * Time: 17:29
 *
 * This file is part of the MihailDev project.
 *
 * (c) MihailDev project <http://github.com/mihaildev/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace mihaildev\elfinder;

/**
 * Class S3Path
 *
 */
class S3Path extends BasePath{
	public $driver = 'S3';
	public $accessKey = '';
	public $secretKey = '';
	public $bucket = '';
	public $region = '';
	public $path = '/';

	public function getRoot(){
		$options = parent::getRoot();

		$options['s3']= [
			'key' => $this->accessKey,
			'secret' => $this->secretKey,
			'region' => $this->region,
			'ssl.certificate_authority' => false
		];

		$options['bucket'] = $this->bucket;
		$options['path'] = $this->path;
		$options['URL'] = $this->path;

		$options['acl'] = 'public';

		if($options['defaults']['read'])
			$options['acl'] .= '-read';

		if($options['defaults']['write'])
			$options['acl'] .= '-write';

		/*unset($options['attributes']);
		unset($options['tmbPath']);
		unset($options['tmpPath']);
		unset($options['defaults']);
		unset($options['alias']);
		unset($options['imgLib']);
		unset($options['mimeDetect']);*/

		return $options;
	}

}