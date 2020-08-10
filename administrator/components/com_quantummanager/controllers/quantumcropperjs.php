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
 * Class QuantummanagerControllerQuantumcropperjs
 */
class QuantummanagerControllerQuantumcropperjs extends AdminController
{

	public function getImageForCrop()
	{
		$app = Factory::getApplication();
		$data = $app->input->getArray();

		if(!isset($data['path'],  $data['scope'], $data['file']))
		{
			$app->close();
		}

		JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
		echo QuantummanagerFileSystemLocal::getImageForCrop($data['path'],  $data['scope'], $data['file']);

		$app->close();
	}


	public function save()
	{

		$app = Factory::getApplication();

		JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
		echo QuantummanagerFileSystemLocal::converterSave();

		$app->close();
	}

}