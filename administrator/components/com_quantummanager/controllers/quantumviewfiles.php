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
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\Filesystem\Folder;

/**
 * Quantummanager Controller.
 *
 * @package  quantummanager
 * @since    1.0
 */
class QuantummanagerControllerquantumviewfiles extends AdminController
{

	public function createDirectory()
	{
		$app = Factory::getApplication();
		$data = $app->input->getArray();

		if(!isset($data['path'], $data['name'], $data['scope']))
		{
			$app->close();
		}

		JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
		echo QuantummanagerFileSystemLocal::createDirectory($data['path'], $data['scope'], $data['name']);

		$app->close();
	}

	public function getFiles()
	{
		$app = Factory::getApplication();
		$data = $app->input->getArray();

		if(!isset($data['path'], $data['scope']))
		{
			$app->close();
		}

		JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
		echo QuantummanagerFileSystemLocal::getFiles($data['path'], $data['scope']);

		$app->close();
	}

	public function getMetaFile()
	{
		$app = Factory::getApplication();
		$data = $app->input->getArray();

		if(!isset($data['path'], $data['scope']))
		{
			$app->close();
		}

		$name = '';

		if(isset($data['name']))
		{
			$name = $data['name'];
		}

		JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
		echo QuantummanagerFileSystemLocal::getMetaFile($data['path'], $data['scope'], $name);

		$app->close();
	}

	public function watermark()
	{
		$app = Factory::getApplication();
		$data = $app->input->getArray();

		if(!isset($data['path'], $data['scope']))
		{
			$app->close();
		}

		$list = '';

		if(isset($data['list']))
		{
			$list = json_decode($data['list']);
		}

		JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
		echo QuantummanagerFileSystemLocal::setWatermark($data['path'], $data['scope'], $list);

		$app->close();
	}

	public function paste()
	{
		$app = Factory::getApplication();
		$data = $app->input->getArray();

		if(!isset(
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

		$pathFrom = $data['pathFrom'];
		$scopeFrom = $data['scopeFrom'];
		$pathTo = $data['pathTo'];
		$scopeTo = $data['scopeTo'];
		$cut = (int)$data['cut'];
		$list = json_decode($data['list'], JSON_OBJECT_AS_ARRAY);

		if(count($list) === 0)
		{
			$app->close();
		}

		JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
		echo QuantummanagerFileSystemLocal::paste($pathFrom, $scopeFrom, $pathTo, $scopeTo, $cut, $list);

		$app->close();
	}

	public function delete()
	{
		$app = Factory::getApplication();
		$data = $app->input->getArray();

		if(!isset($data['path'], $data['list'], $data['scope']))
		{
			$app->close();
		}

		$path = $data['path'];
		$scope = $data['scope'];
		$list = json_decode($data['list']);

		JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
		echo QuantummanagerFileSystemLocal::delete($path, $scope, $list);

		$app->close();

	}

	public function getParsePath()
	{
		try {
			$app = Factory::getApplication();
			$data = $app->input->getArray();

			if(!isset($data['path'], $data['scope']))
			{
				$app->close();
			}

			$path = $data['path'];
			$scope = $data['scope'];
			$host = '';

			if(isset($data['host']))
			{
				$host = $data['host'];
			}

			JLoader::register('QuantummanagerHelper', JPATH_ROOT . '/administrator/components/com_quantummanager/helpers/quantummanager.php');
			echo json_encode([
				'path' => QuantummanagerHelper::preparePath($path, $host, $scope, true)
			]);

			$app->close();
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}

	public function renameFile()
	{
		try {
			$app = Factory::getApplication();
			$data = $app->input->getArray();
			$file = '';

			if(!isset($data['path'], $data['scope'], $data['file'], $data['name']))
			{
				$app->close();
			}

			if(empty($data['path']) || empty($data['scope']) || empty($data['file']) || empty($data['name']))
			{
				$app->close();
			}

			$path = $data['path'];
			$scope = $data['scope'];
			$file = $data['file'];
			$name = $data['name'];

			JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
			echo QuantummanagerFileSystemLocal::renameFile($path, $scope, $file, $name);

			$app->close();
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}

	public function renameDirectory()
	{
		try {
			$app = Factory::getApplication();
			$data = $app->input->getArray();
			$file = '';

			if(!isset($data['path'], $data['scope'], $data['oldName'], $data['name']))
			{
				$app->close();
			}

			if(empty($data['path']) || empty($data['scope']) || empty($data['oldName']) || empty($data['name']))
			{
				$app->close();
			}

			$path = $data['path'];
			$scope = $data['scope'];
			$oldName = $data['oldName'];
			$name = $data['name'];

			JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
			echo QuantummanagerFileSystemLocal::renameDirectory($path, $scope, $oldName, $name);

			$app->close();
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}

	public function generatePreviewImage()
	{
		try {
			$app = Factory::getApplication();
			$data = $app->input->getArray();
			$file = '';

			if(!isset($data['path'], $data['scope']))
			{
				$app->close();
			}

			$path = $data['path'];
			$scope = $data['scope'];

			if(isset($data['file']))
			{
				$file = $data['file'];
			}

			JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
			QuantummanagerFileSystemLocal::generatePreviewImage($path, $scope, $file);

			$app->close();
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}

}
