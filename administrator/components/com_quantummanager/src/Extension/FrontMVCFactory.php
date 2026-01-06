<?php

namespace Joomla\Component\QuantumManager\Administrator\Extension;

/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Factory\MVCFactory;
use Joomla\Component\QuantumManager\Administrator\Helper\QuantummanagerHelper;
use Joomla\Input\Input;

final class FrontMVCFactory extends MVCFactory
{
	public function createController($name, $prefix, array $config, CMSApplicationInterface $app, Input $input)
	{
		if (!(int) QuantummanagerHelper::getParamsComponentValue('front', 0))
		{
			throw new \InvalidArgumentException(Text::sprintf('JLIB_APPLICATION_ERROR_NOT_ACCESS'));
		}

		if (Factory::getApplication()->getIdentity()->id === 0)
		{
			throw new \InvalidArgumentException(Text::sprintf('JLIB_APPLICATION_ERROR_NOT_ACCESS'));
		}

		$controller = parent::createController($name, $prefix, $config, $app, $input);

		if (!$controller)
		{
			$controller = parent::createController($name, 'Administrator', $config, $app, $input);
		}

		return $controller;
	}

}
