<?php
/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

defined('_JEXEC') or die;

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

		// Show the sidebar
		$this->helper = new QuantummanagerHelper;

		// Display it all
		return parent::display($tpl);
	}

}
