<?php
/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Http\Transport\CurlTransport;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Uri\Uri;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Joomla\Registry\Registry;

/**
 * Class QuantummanagerControllerquantumunsplash
 */
class QuantummanagerControllerquantumunsplash extends AdminController
{


	public function search()
	{

		$app = Factory::getApplication();

		try
		{
			$data = Factory::getApplication()->input->getArray();
			$dataForRequest = [
				'q' => '',
				'page' => '1',
			];

			if(isset($data['q']))
			{
				$dataForRequest['q'] = $data['q'];
			}

			if(isset($data['page']))
			{
				$dataForRequest['page'] = $data['page'];
			}

			$query = http_build_query([
				'option' => 'com_yoohikashop',
				'task' => 'unsplash.search'
			]);

			$curlTransport = new CurlTransport(new Registry());
			$uri = new Uri();
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
		$app->close();
	}


	public function download()
	{
		$app = Factory::getApplication();
		$data = Factory::getApplication()->input->getArray();
		$file = $data['file'];

		if(!isset($data['file'], $data['path'], $data['scope']))
		{
			$app->close();
		}

		$path = $data['path'];
		$scope = $data['scope'];
		$file = $data['file'];
		$id = $data['id'];

		JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
		echo QuantummanagerFileSystemLocal::downloadFileUnsplash($path, $scope, $file, $id);

		QuantummanagerHelper::setHeadersNoCache();
		$app->close();

	}


	public function downloadTrigger()
	{
		$app = Factory::getApplication();
		$data = $app->input->getArray();

		$query = http_build_query([
			'option' => 'com_yoohikashop',
			'task' => 'unsplash.downloadTrigger',
			'uid' => $data['id']
		]);

		$curlTransport = new CurlTransport(new Registry());
		$uri = new Uri();
		$uri->setScheme('https');
		$uri->setHost('hika.su/');
		$uri->setPath('index.php');
		$uri->setQuery($query);
		$request = $curlTransport->request('GET', $uri);
		$photo = json_decode($request->body, JSON_OBJECT_AS_ARRAY);

		QuantummanagerHelper::setHeadersNoCache();
		$app->close();
	}

}
