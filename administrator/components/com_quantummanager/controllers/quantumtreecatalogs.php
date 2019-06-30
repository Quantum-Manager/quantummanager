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
class QuantummanagerControllerQuantumtreecatalogs extends AdminController
{


	public function getDirectories()
	{
		$app = Factory::getApplication();
		$data = $app->input->getArray();

		if(!isset($data['path']))
		{
			$app->close();
		}

		$path = $data['path'];
		$rootSplit = explode('/',  $data['root']);

		if(isset($rootSplit[0]))
		{
			$root = $rootSplit[0];
		}
		else
		{
			$root = '';
		}

		JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
		echo QuantummanagerFileSystemLocal::getDirectories($path, $root);

		$app->close();
	}

}
