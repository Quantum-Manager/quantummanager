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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\QuantumManager\Administrator\Helper\QuantummanagerHelper;

/**
 * HtmlView.
 *
 * @package  quantummanager
 * @since    1.0
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * Quantummanager helper
	 *
	 * @var    QuantummanagerHelper
	 * @since  1.0
	 */
	protected $helper;

	/**
	 * The sidebar to show
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $sidebar = '';

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @see     fetch()
	 * @since   1.0
	 */
	public function display($tpl = null)
	{

		// Show the toolbar
		$this->toolbar();

		// Show the sidebar
		$this->helper = new QuantummanagerHelper;

		// Display it all
		return parent::display($tpl);
	}

	/**
	 * Displays a toolbar for a specific page.
	 *
	 * @return  void.
	 *
	 * @since   1.0
	 */
	private function toolbar()
	{
		// Options button.
		if (QuantummanagerHelper::isJoomla4())
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
		else
		{

			ToolBarHelper::title(Text::_('COM_QUANTUMMANAGER'), '');

			// Options button.
			if (Factory::getUser()->authorise('core.admin', 'com_quantummanager'))
			{
				ToolBarHelper::preferences('com_quantummanager');
			}

			$toolbar = Toolbar::getInstance('toolbar');
			$url     = Uri::root(true) . '/administrator/index.php?option=com_quantummanager&layout=window&tmpl=component';
			$button  = '<a href="' . $url . '" class="btn btn-small" target="_blank">'
				. '<span class="icon-new-tab" aria-hidden="true"></span>'
				. Text::_('COM_QUANTUMMANAGER_CLEAN_WINDOW') . '</a>';
			$toolbar->appendButton('Custom', $button, 'generate');
		}
	}
}
