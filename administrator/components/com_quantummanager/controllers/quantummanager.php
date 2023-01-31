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

JLoader::register('QuantummanagerController', JPATH_ADMINISTRATOR . '/components/com_quantummanager/controller.php');

/**
 * Class QuantummanagerControllerQuantummanager
 */
class QuantummanagerControllerQuantummanager extends QuantummanagerController
{


	public function hideJedReview()
	{
		$app = Factory::getApplication();
		JLoader::register('QuantummanagerHelper', JPATH_ROOT . '/administrator/components/com_quantummanager/helpers/quantummanager.php');

		if((int)QuantummanagerHelper::getParamsComponentValue('reivew', 1))
		{
			QuantummanagerHelper::setComponentsParams('review', "0");
		}

		$app->close();
	}

}
