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
	public $path = '/';

	public function getRoot(){
		$options = parent::getRoot();

		$options['accesskey'] = $this->accessKey;
		$options['secretkey'] = $this->secretKey;
		$options['bucket'] = $this->bucket;
		$options['path'] = $this->path;

		return $options;
	}

}