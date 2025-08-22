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
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\Component\QuantumManager\Administrator\Filesystem\LocalFilesystem;
use Joomla\Component\QuantumManager\Administrator\Helper\QuantummanagerHelper;

/**
 * Class QuantumuploadController
 */
class QuantumuploadController extends BaseController
{


	public function upload()
	{
		$app  = Factory::getApplication();
		$data = $app->input->getArray();

		if (!isset($data['path'], $data['scope']))
		{
			$app->close();
		}

		echo LocalFilesystem::upload();

		QuantummanagerHelper::setHeadersNoCache();
		$app->close();
	}

}
