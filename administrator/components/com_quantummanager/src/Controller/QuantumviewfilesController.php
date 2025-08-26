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
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\Component\QuantumManager\Administrator\Filesystem\LocalFilesystem;
use Joomla\Component\QuantumManager\Administrator\Helper\QuantummanagerHelper;

class QuantumviewfilesController extends BaseController
{

	public function createDirectory(): void
	{
		$data = $this->app->input->getArray();

		if (!isset($data['path'], $data['name'], $data['scope']))
		{
			$this->app->close();
		}

		echo LocalFilesystem::createDirectory($data['path'], $data['scope'], $data['name']);

		QuantummanagerHelper::setHeadersNoCache();
		$this->app->close();
	}

	public function getFiles(): void
	{
		$data = $this->app->input->getArray();

		if (!isset($data['path'], $data['scope']))
		{
			$this->app->close();
		}

		echo LocalFilesystem::getFiles($data['path'], $data['scope']);

		QuantummanagerHelper::setHeadersNoCache();
		$this->app->close();
	}

	public function getMetaFile(): void
	{
		$data = $this->app->input->getArray();

		if (!isset($data['path'], $data['scope']))
		{
			$this->app->close();
		}

		$name = '';

		if (isset($data['name']))
		{
			$name = $data['name'];
		}

		echo LocalFilesystem::getMetaFile($data['path'], $data['scope'], $name);

		QuantummanagerHelper::setHeadersNoCache();
		$this->app->close();
	}

	public function watermark(): void
	{
		$data = $this->app->input->getArray();

		if (!isset($data['path'], $data['scope']))
		{
			$this->app->close();
		}

		$list = '';

		if (isset($data['list']))
		{
			$list = json_decode($data['list']);
		}

		LocalFilesystem::setWatermark($data['path'], $data['scope'], $list);

		QuantummanagerHelper::setHeadersNoCache();
		$this->app->close();
	}

	public function duplicate(): void
	{
		$data = $this->app->input->getArray();

		if (!isset(
			$data['path'],
			$data['scope'],
			$data['list'])
		)
		{
			$this->app->close();
		}

		$path  = $data['path'];
		$scope = $data['scope'];
		$list  = json_decode($data['list'], JSON_OBJECT_AS_ARRAY);

		if (count($list) === 0)
		{
			$this->app->close();
		}

		echo LocalFilesystem::duplicate($path, $scope, $list);

		QuantummanagerHelper::setHeadersNoCache();
		$this->app->close();
	}

	public function paste(): void
	{
		$data = $this->app->input->getArray();

		if (!isset(
			$data['pathFrom'],
			$data['scopeFrom'],
			$data['pathTo'],
			$data['scopeTo'],
			$data['cut'],
			$data['list'])
		)
		{
			$this->app->close();
		}

		$pathFrom  = $data['pathFrom'];
		$scopeFrom = $data['scopeFrom'];
		$pathTo    = $data['pathTo'];
		$scopeTo   = $data['scopeTo'];
		$cut       = (int) $data['cut'];
		$list      = json_decode($data['list'], JSON_OBJECT_AS_ARRAY);

		if (count($list) === 0)
		{
			$this->app->close();
		}

		echo LocalFilesystem::paste($pathFrom, $scopeFrom, $pathTo, $scopeTo, $cut, $list);

		QuantummanagerHelper::setHeadersNoCache();
		$this->app->close();
	}

	public function delete(): void
	{
		$data = $this->app->input->getArray();

		if (!isset($data['path'], $data['list'], $data['scope']))
		{
			$this->app->close();
		}

		$path  = $data['path'];
		$scope = $data['scope'];
		$list  = json_decode($data['list']);

		echo LocalFilesystem::delete($scope, $path, $list);

		QuantummanagerHelper::setHeadersNoCache();
		$this->app->close();

	}

	public function getParsePath(): void
	{
		try
		{
			$data = $this->app->input->getArray();

			if (!isset($data['path'], $data['scope']))
			{
				$this->app->close();
			}

			$path  = $data['path'];
			$scope = $data['scope'];
			$host  = '';

			if (isset($data['host']))
			{
				$host = $data['host'];
			}

			echo json_encode([
				'path' => QuantummanagerHelper::preparePath($path, $host, $scope, true)
			]);

			QuantummanagerHelper::setHeadersNoCache();
			$this->app->close();
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}

	public function renameFile(): void
	{
		try
		{
			$data = $this->app->input->getArray();

			if (!isset($data['path'], $data['scope'], $data['file'], $data['name']))
			{
				$this->app->close();
			}

			if (empty($data['path']) || empty($data['scope']) || empty($data['file']) || empty($data['name']))
			{
				$this->app->close();
			}

			$path  = $data['path'];
			$scope = $data['scope'];
			$file  = $data['file'];
			$name  = $data['name'];

			echo LocalFilesystem::renameFile($path, $scope, $file, $name);

			QuantummanagerHelper::setHeadersNoCache();
			$this->app->close();
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}

	public function renameDirectory(): void
	{
		try
		{
			$data      = $this->app->input->getArray();

			if (!isset($data['path'], $data['scope'], $data['oldName'], $data['name']))
			{
				$this->app->close();
			}

			if (empty($data['path']) || empty($data['scope']) || empty($data['oldName']) || empty($data['name']))
			{
				$this->app->close();
			}

			$path    = $data['path'];
			$scope   = $data['scope'];
			$oldName = $data['oldName'];
			$name    = $data['name'];

			echo LocalFilesystem::renameDirectory($path, $scope, $oldName, $name);

			QuantummanagerHelper::setHeadersNoCache();
			$this->app->close();
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}

	public function generatePreviewImage(): void
	{
		try
		{
			$data      = $this->app->input->getArray();
			$file      = '';

			if (!isset($data['path'], $data['scope']))
			{
				$this->app->close();
			}

			$path  = $data['path'];
			$scope = $data['scope'];

			if (isset($data['file']))
			{
				$file = $data['file'];
			}

			LocalFilesystem::generatePreviewImage($path, $scope, $file);

			QuantummanagerHelper::setHeadersNoCache();
			$this->app->close();
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}

	public function createPreview(): void
	{
		try
		{
			$data      = $this->app->input->getArray();

			if (!isset($data['path'], $data['scope'], $data['list'], $data['preview']))
			{
				$this->app->close();
			}

			$path    = $data['path'];
			$scope   = $data['scope'];
			$list    = json_decode($data['list'], true);
			$preview = $data['preview'];

			echo LocalFilesystem::createPreview($path, $scope, $list, $preview);

			QuantummanagerHelper::setHeadersNoCache();
			$this->app->close();
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}

}
