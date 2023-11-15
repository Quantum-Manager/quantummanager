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
use Joomla\Component\QuantumManager\Administrator\Helper\QuantummanagerHelper;

/**
 * Class DisplayController
 */
class QuantummanagerController extends BaseController
{

	public function hideJedReview()
	{
		$app = Factory::getApplication();

		if ((int) QuantummanagerHelper::getParamsComponentValue('reivew', 1))
		{
			QuantummanagerHelper::setComponentsParams('review', "0");
		}

		$app->close();
	}

}
