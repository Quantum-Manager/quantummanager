<?php

namespace Joomla\Component\QuantumManager\Site\Dispatcher;

/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

use Joomla\CMS\Dispatcher\ComponentDispatcher;

defined('_JEXEC') or die;

class Dispatcher extends ComponentDispatcher
{

	protected function loadLanguage()
	{
		$this->app->getLanguage()->load($this->option, JPATH_BASE) ||
		$this->app->getLanguage()->load($this->option, JPATH_COMPONENT);
		$this->app->getLanguage()->load($this->option, JPATH_ADMINISTRATOR);
	}

}
