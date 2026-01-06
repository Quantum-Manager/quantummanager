<?php

namespace Joomla\Component\QuantumManager\Site\View\Quantummanager;

/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseView;
use Joomla\Component\QuantumManager\Administrator\Helper\QuantummanagerHelper;

class HtmlView extends BaseView
{

	protected $helper;

	public function display($tpl = null)
	{
		$this->helper = new QuantummanagerHelper;

		parent::display($tpl);
	}

}
