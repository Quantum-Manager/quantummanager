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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;

JLoader::register('QuantummanagerController', JPATH_ADMINISTRATOR . '/components/com_quantummanager/controller.php');

/**
 * Class QuantummanagerControllerQuantumupload
 */
class QuantummanagerControllerQuantumupload extends QuantummanagerController
{


	public function upload()
	{
		$app = Factory::getApplication();
		$data = $app->input->getArray();

		if(!isset($data['path'], $data['scope']))
		{
			$app->close();
		}

		JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
		echo QuantummanagerFileSystemLocal::upload();

		QuantummanagerHelper::setHeadersNoCache();
		$app->close();
	}

}
