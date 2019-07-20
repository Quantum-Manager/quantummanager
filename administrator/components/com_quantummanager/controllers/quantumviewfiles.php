<?php
/**
 * @package    quantummanager
 *
 * @author     Cymbal <cymbal@delo-design.ru>
 * @copyright  Copyright (C) 2019 "Delo Design". All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://delo-design.ru
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

		if(!isset($data['path']) || !isset($data['name']))
		{
			$app->close();
		}

		JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
		echo QuantummanagerFileSystemLocal::createDirectory($data['path'], $data['name']);

		$app->close();
	}


	public function getFiles()
	{
		$app = Factory::getApplication();
		$data = $app->input->getArray();

		if(!isset($data['path']))
		{
			$app->close();
		}

		JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
		echo QuantummanagerFileSystemLocal::getFiles($data['path']);

		$app->close();
	}


	public function getMetaFile()
	{
		$app = Factory::getApplication();
		$data = $app->input->getArray();

		if(!isset($data['path']))
		{
			$app->close();
		}

		$name = '';

		if(isset($data['name']))
		{
			$name = $data['name'];
		}

		JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
		echo QuantummanagerFileSystemLocal::getMetaFile($data['path'], $name);

		$app->close();
	}


	public function delete()
	{
		$app = Factory::getApplication();
		$data = $app->input->getArray();

		if(!isset($data['path']) || !isset($data['list']))
		{
			$app->close();
		}

		$path = $data['path'];
		$list = json_decode($data['list']);

		JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
		echo QuantummanagerFileSystemLocal::delete($path, $list);

		$app->close();

	}


	public function getParsePath()
	{
		try {
			$app = Factory::getApplication();
			$data = $app->input->getArray();

			if(!isset($data['path']))
			{
				$app->close();
			}

			$path = $data['path'];
			$host = '';

			if(isset($data['host']))
			{
				$host = $data['host'];
			}

			JLoader::register('QuantummanagerHelper', JPATH_ROOT . '/administrator/components/com_quantummanager/helpers/quantummanager.php');
			echo json_encode([
				'path' => QuantummanagerHelper::preparePath($path, $host)
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

			if(!isset($data['path']) && !isset($data['file']) && !isset($data['name']))
			{
				$app->close();
			}

			if(empty($data['path']) || empty($data['file']) || empty($data['name']))
			{
				$app->close();
			}

			$path = $data['path'];
			$file = $data['file'];
			$name = $data['name'];

			JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
			echo QuantummanagerFileSystemLocal::renameFile($path, $file, $name);

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

			if(!isset($data['path']))
			{
				$app->close();
			}

			$path = $data['path'];

			if(isset($data['file']))
			{
				$file = $data['file'];
			}

			JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
			QuantummanagerFileSystemLocal::generatePreviewImage($path, $file);

			$app->close();
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}

}
