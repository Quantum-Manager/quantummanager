<?php namespace Joomla\Component\QuantumManager\Administrator\Controller;

/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\Component\QuantumManager\Administrator\Filesystem\LocalFilesystem;
use Joomla\Component\QuantumManager\Administrator\Helper\QuantummanagerHelper;

class QuantumtreecatalogsController extends BaseController
{

	public function getDirectories(): void
	{
		$data = $this->app->input->getArray();

		if (!isset($data['path']))
		{
			$this->app->close();
		}

		$path      = $data['path'];
		$scope     = isset($data['scope']) ? $data['scope'] : '';
		$rootSplit = explode('/', $data['root']);

		if (isset($rootSplit[0]))
		{
			$root = $rootSplit[0];
		}
		else
		{
			$root = '';
		}

		if (empty($scope))
		{
			$scope = 'all';
		}

		echo LocalFilesystem::getScopesDirectories($path, $root, $scope);

		QuantummanagerHelper::setHeadersNoCache();
		$this->app->close();
	}

}
