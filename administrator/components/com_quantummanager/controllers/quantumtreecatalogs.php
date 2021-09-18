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
 * Class QuantummanagerControllerQuantumtreecatalogs
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
		$scope = isset($data['scope']) ? $data['scope'] : '';
		$rootSplit = explode('/',  $data['root']);

		if(isset($rootSplit[0]))
		{
			$root = $rootSplit[0];
		}
		else
		{
			$root = '';
		}

		if(empty($scope))
		{
			$scope = 'all';
		}

		JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
		echo QuantummanagerFileSystemLocal::getScopesDirectories($scope, $path, $root);

		QuantummanagerHelper::setHeadersNoCache();
		$app->close();
	}

}
