<?php
/**
 * @package    quantummanager
 *
 * @author     Cymbal <cymbal@delo-design.ru>
 * @copyright  Copyright (C) 2019 "Delo Design". All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://delo-design.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;

/**
 * Quantummanager view.
 *
 * @package  quantummanager
 * @since    1.0
 */
class QuantummanagerViewQuantummanager extends HtmlView
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
		/*$this->helper->addSubmenu('quantummanager');
		$this->sidebar = JHtmlSidebar::render();*/

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
		JToolBarHelper::title(Text::_('COM_QUANTUMMANAGER'), '');

		// Options button.
		if (Factory::getUser()->authorise('core.admin', 'com_quantummanager'))
		{
			JToolBarHelper::preferences('com_quantummanager');
		}

		JToolBarHelper::link('/administrator/index.php?option=com_quantummanager&layout=window&tmpl=component', Text::_('COM_QUANTUMMANAGER_CLEAN_WINDOW'));
	}
}
