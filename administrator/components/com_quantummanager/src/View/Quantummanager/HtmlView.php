<?php namespace Joomla\Component\QuantumManager\Administrator\View\Quantummanager;

/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\Component\QuantumManager\Administrator\Helper\QuantummanagerHelper;

class HtmlView extends BaseHtmlView
{
	protected $helper;

	protected string $sidebar = '';

	public function display($tpl = null)
	{
		$this->toolbar();
		$this->helper = new QuantummanagerHelper;

		return parent::display($tpl);
	}

	private function toolbar(): void
	{
		if (Factory::getUser()->authorise('core.admin', 'com_quantummanager'))
		{
			HTMLHelper::_('script', 'com_quantummanager/configuration.js', [
				'version'  => filemtime(__FILE__),
				'relative' => true
			]);
		}

		HTMLHelper::_('stylesheet', 'com_quantummanager/joomla4hidetoolbar.css', [
			'version'  => filemtime(__FILE__),
			'relative' => true
		]);

	}
}
