<?php
/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

defined('_JEXEC') or die;

/**
 * Quantummanager script file.
 *
 * @package     A package name
 * @since       1.0
 */
class Com_QuantummanagerInstallerScript
{
	/**
	 * Constructor
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 */
	public function __construct(JAdapterInstance $adapter) {}

	/**
	 * Called before any type of action
	 *
	 * @param   string  $route  Which action is happening (install|uninstall|discover_install|update)
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function preflight($route, JAdapterInstance $adapter) {
		if (!(version_compare(PHP_VERSION, '7.1.0') >= 0))
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_QUANTUMMANAGER_WRONG_PHP'), 'error');
			return false;
		}

		jimport('joomla.version');
		$jversion = new JVersion();
		if (!$jversion->isCompatible('3.7'))
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_QUANTUMMANAGER_WRONG_JOOMLA'), 'error');
			return false;
		}
	}

	/**
	 * Called after any type of action
	 *
	 * @param string           $route   Which action is happening (install|uninstall|discover_install|update)
	 * @param JAdapterInstance $adapter The object responsible for running this script
	 *
	 * @return void True on success
	 */
	public function postflight($route, JAdapterInstance $adapter) {}

	/**
	 * Called on installation
	 *
	 * @param JAdapterInstance $adapter The object responsible for running this script
	 *
	 * @return void True on success
	 */
	public function install(JAdapterInstance $adapter) {}

	/**
	 * Called on update
	 *
	 * @param JAdapterInstance $adapter The object responsible for running this script
	 *
	 * @return void True on success
	 */
	public function update(JAdapterInstance $adapter) {}

	/**
	 * Called on uninstallation
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 */
	public function uninstall(JAdapterInstance $adapter) {}
}
