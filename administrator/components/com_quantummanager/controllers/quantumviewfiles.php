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

JLoader::register('QuantummanagerController', JPATH_ADMINISTRATOR . '/components/com_quantummanager/controller.php');

/**
 * Class QuantummanagerControllerquantumviewfiles
 */
class QuantummanagerControllerQuantumviewfiles extends QuantummanagerController
{

	public function createDirectory()
	{
		$app  = Factory::getApplication();
		$data = $app->input->getArray();

		if (!isset($data['path'], $data['name'], $data['scope']))
		{
			$app->close();
		}

		JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
		echo QuantummanagerFileSystemLocal::createDirectory($data['path'], $data['scope'], $data['name']);

		QuantummanagerHelper::setHeadersNoCache();
		$app->close();
	}

	public function getFiles()
	{
		$app  = Factory::getApplication();
		$data = $app->input->getArray();

		if (!isset($data['path'], $data['scope']))
		{
			$app->close();
		}

		JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
		echo QuantummanagerFileSystemLocal::getFiles($data['path'], $data['scope']);

		QuantummanagerHelper::setHeadersNoCache();
		$app->close();
	}

	public function getMetaFile()
	{
		$app  = Factory::getApplication();
		$data = $app->input->getArray();

		if (!isset($data['path'], $data['scope']))
		{
			$app->close();
		}

		$name = '';

		if (isset($data['name']))
		{
			$name = $data['name'];
		}

		JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
		echo QuantummanagerFileSystemLocal::getMetaFile($data['path'], $data['scope'], $name);

		QuantummanagerHelper::setHeadersNoCache();
		$app->close();
	}

	public function watermark()
	{
		$app  = Factory::getApplication();
		$data = $app->input->getArray();

		if (!isset($data['path'], $data['scope']))
		{
			$app->close();
		}

		$list = '';

		if (isset($data['list']))
		{
			$list = json_decode($data['list']);
		}

		JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
		echo QuantummanagerFileSystemLocal::setWatermark($data['path'], $data['scope'], $list);

		QuantummanagerHelper::setHeadersNoCache();
		$app->close();
	}

	public function duplicate()
	{
		$app  = Factory::getApplication();
		$data = $app->input->getArray();

		if (!isset(
			$data['path'],
			$data['scope'],
			$data['list'])
		)
		{
			$app->close();
		}

		$path  = $data['path'];
		$scope = $data['scope'];
		$list  = json_decode($data['list'], JSON_OBJECT_AS_ARRAY);

		if (count($list) === 0)
		{
			$app->close();
		}

		JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
		echo QuantummanagerFileSystemLocal::duplicate($path, $scope, $list);

		QuantummanagerHelper::setHeadersNoCache();
		$app->close();
	}

	public function paste()
	{
		$app  = Factory::getApplication();
		$data = $app->input->getArray();

		if (!isset(
			$data['pathFrom'],
			$data['scopeFrom'],
			$data['pathTo'],
			$data['scopeTo'],
			$data['cut'],
			$data['list'])
		)
		{
			$app->close();
		}

		$pathFrom  = $data['pathFrom'];
		$scopeFrom = $data['scopeFrom'];
		$pathTo    = $data['pathTo'];
		$scopeTo   = $data['scopeTo'];
		$cut       = (int) $data['cut'];
		$list      = json_decode($data['list'], JSON_OBJECT_AS_ARRAY);

		if (count($list) === 0)
		{
			$app->close();
		}

		JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
		echo QuantummanagerFileSystemLocal::paste($pathFrom, $scopeFrom, $pathTo, $scopeTo, $cut, $list);

		QuantummanagerHelper::setHeadersNoCache();
		$app->close();
	}

	public function delete()
	{
		$app  = Factory::getApplication();
		$data = $app->input->getArray();

		if (!isset($data['path'], $data['list'], $data['scope']))
		{
			$app->close();
		}

		$path  = $data['path'];
		$scope = $data['scope'];
		$list  = json_decode($data['list']);

		JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
		echo QuantummanagerFileSystemLocal::delete($scope, $path, $list);

		QuantummanagerHelper::setHeadersNoCache();
		$app->close();

	}

	public function getParsePath()
	{
		try
		{
			$app  = Factory::getApplication();
			$data = $app->input->getArray();

			if (!isset($data['path'], $data['scope']))
			{
				$app->close();
			}

			$path  = $data['path'];
			$scope = $data['scope'];
			$host  = '';

			if (isset($data['host']))
			{
				$host = $data['host'];
			}

			JLoader::register('QuantummanagerHelper', JPATH_ROOT . '/administrator/components/com_quantummanager/helpers/quantummanager.php');
			echo json_encode([
				'path' => QuantummanagerHelper::preparePath($path, $host, $scope, true)
			]);

			QuantummanagerHelper::setHeadersNoCache();
			$app->close();
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}

	public function renameFile()
	{
		try
		{
			$app  = Factory::getApplication();
			$data = $app->input->getArray();
			$file = '';

			if (!isset($data['path'], $data['scope'], $data['file'], $data['name']))
			{
				$app->close();
			}

			if (empty($data['path']) || empty($data['scope']) || empty($data['file']) || empty($data['name']))
			{
				$app->close();
			}

			$path  = $data['path'];
			$scope = $data['scope'];
			$file  = $data['file'];
			$name  = $data['name'];

			JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
			echo QuantummanagerFileSystemLocal::renameFile($path, $scope, $file, $name);

			QuantummanagerHelper::setHeadersNoCache();
			$app->close();
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}

	public function renameDirectory()
	{
		try
		{
			$app  = Factory::getApplication();
			$data = $app->input->getArray();
			$file = '';

			if (!isset($data['path'], $data['scope'], $data['oldName'], $data['name']))
			{
				$app->close();
			}

			if (empty($data['path']) || empty($data['scope']) || empty($data['oldName']) || empty($data['name']))
			{
				$app->close();
			}

			$path    = $data['path'];
			$scope   = $data['scope'];
			$oldName = $data['oldName'];
			$name    = $data['name'];

			JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
			echo QuantummanagerFileSystemLocal::renameDirectory($path, $scope, $oldName, $name);

			QuantummanagerHelper::setHeadersNoCache();
			$app->close();
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}

	public function generatePreviewImage()
	{
		try
		{
			$app  = Factory::getApplication();
			$data = $app->input->getArray();
			$file = '';

			if (!isset($data['path'], $data['scope']))
			{
				$app->close();
			}

			$path  = $data['path'];
			$scope = $data['scope'];

			if (isset($data['file']))
			{
				$file = $data['file'];
			}

			JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
			QuantummanagerFileSystemLocal::generatePreviewImage($path, $scope, $file);

			QuantummanagerHelper::setHeadersNoCache();
			$app->close();
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}

	public function createPreview()
	{
		try
		{
			$app  = Factory::getApplication();
			$data = $app->input->getArray();
			$file = '';

			if (!isset($data['path'], $data['scope'], $data['list'], $data['preview']))
			{
				$app->close();
			}

			$path    = $data['path'];
			$scope   = $data['scope'];
			$list    = json_decode($data['list'], true);
			$preview = $data['preview'];

			JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
			echo QuantummanagerFileSystemLocal::createPreview($path, $scope, $list, $preview);

			QuantummanagerHelper::setHeadersNoCache();
			$app->close();
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}

}
