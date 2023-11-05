<?php namespace Joomla\Component\QuantumManager\Administrator\Controller;

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
use Joomla\Component\QuantumManager\Administrator\Filesystem\LocalFilesystem;
use Joomla\Component\QuantumManager\Administrator\Helper\QuantummanagerHelper;
use Joomla\Filesystem\Folder;

/**
 * Class QuantumcropperjsController
 */
class QuantumcropperjsController extends QuantummanagerController
{

	public function getImageForCrop()
	{
		$app = Factory::getApplication();
		$data = $app->input->getArray();

		if(!isset($data['path'],  $data['scope'], $data['file']))
		{
			$app->close();
		}

		echo LocalFilesystem::getImageForCrop($data['path'],  $data['scope'], $data['file']);

		QuantummanagerHelper::setHeadersNoCache();
		$app->close();
	}


	public function save()
	{

		$app = Factory::getApplication();

		echo LocalFilesystem::converterSave();

		QuantummanagerHelper::setHeadersNoCache();
		$app->close();
	}

}