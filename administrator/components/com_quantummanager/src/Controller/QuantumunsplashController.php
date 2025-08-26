<?php namespace Joomla\Component\QuantumManager\Administrator\Controller;

/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Http\Transport\CurlTransport;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\QuantumManager\Administrator\Filesystem\LocalFilesystem;
use Joomla\Component\QuantumManager\Administrator\Helper\QuantummanagerHelper;
use Joomla\Registry\Registry;

class QuantumunsplashController extends BaseController
{

	public function search(): void
	{

		try
		{
			$data           = $this->app->input->getArray();
			$dataForRequest = [
				'q'    => '',
				'page' => '1',
			];

			if (isset($data['q']))
			{
				$dataForRequest['q'] = $data['q'];
			}

			if (isset($data['page']))
			{
				$dataForRequest['page'] = $data['page'];
			}

			$query = http_build_query([
				'option' => 'com_yoohikashop',
				'task'   => 'unsplash.search'
			]);

			$curlTransport = new CurlTransport(new Registry());
			$uri           = new Uri();
			$uri->setScheme('https');
			$uri->setHost('hika.su/');
			$uri->setPath('index.php');
			$uri->setQuery($query);
			$request = $curlTransport->request('POST', $uri, $dataForRequest);
			echo $request->body;
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}

		QuantummanagerHelper::setHeadersNoCache();
		$this->app->close();
	}

	public function download(): void
	{
		$data = Factory::getApplication()->input->getArray();

		if (!isset($data['file'], $data['path'], $data['scope']))
		{
			$this->app->close();
		}

		$path  = $data['path'];
		$scope = $data['scope'];
		$file  = $data['file'];
		$id    = $data['id'];

		echo LocalFilesystem::downloadFileUnsplash($path, $scope, $file, $id);

		QuantummanagerHelper::setHeadersNoCache();
		$this->app->close();
	}

	public function downloadTrigger(): void
	{
		$data = $this->app->input->getArray();

		$query = http_build_query([
			'option' => 'com_yoohikashop',
			'task'   => 'unsplash.downloadTrigger',
			'uid'    => $data['id']
		]);

		$curlTransport = new CurlTransport(new Registry());
		$uri           = new Uri();
		$uri->setScheme('https');
		$uri->setHost('hika.su/');
		$uri->setPath('index.php');
		$uri->setQuery($query);
		$request = $curlTransport->request('GET', $uri);
		$photo   = json_decode($request->body, JSON_OBJECT_AS_ARRAY);

		QuantummanagerHelper::setHeadersNoCache();
		$this->app->close();
	}

}
