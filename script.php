<?php
/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;
use Joomla\Component\QuantumManager\Administrator\Helper\QuantummanagerHelper;

defined('_JEXEC') or die;

/**
 * Quantummanager script file.
 *
 * @package     A package name
 * @since       1.0
 */
class com_quantummanagerInstallerScript
{

	/**
	 * Minimum PHP version required to install the extension.
	 *
	 * @var  string
	 *
	 * @since  0.0.1
	 */
	protected $minimumPhp = '7.1';

	/**
	 * Minimum Joomla version required to install the extension.
	 *
	 * @var  string
	 *
	 * @since  0.0.1
	 */
	protected $minimumJoomla = '3.9.0';

	/**
	 * @var string
	 */
	protected $helpURL = 'https://norrnext.com/docs/joomla-extensions/quantum-manager';


	/**
	 * Extensions for php
	 * @var array
	 */
	protected $extensions = [
		'fileinfo',
		'curl',
		'mbstring'
	];


	/**
	 * Called before any type of action
	 *
	 * @param   string            $route    Which action is happening (install|uninstall|discover_install|update)
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function preflight($route, $adapter)
	{
		$app = Factory::getApplication();

		if (!(version_compare(PHP_VERSION, '7.1.0') >= 0))
		{
			$app->enqueueMessage(Text::sprintf('COM_QUANTUMMANAGER_ERROR_COMPATIBLE_PHP', $this->minimumPhp),
				'error');

			return false;
		}

		$jversion = new Version();
		if (!$jversion->isCompatible('3.7'))
		{
			$app->enqueueMessage(Text::sprintf('COM_QUANTUMMANAGER_ERROR_COMPATIBLE_PHP', $this->minimumJoomla),
				'error');

			return false;
		}

		//Check extension
		$extensionsNotLoaded = [];
		foreach ($this->extensions as $extension)
		{
			if (!extension_loaded($extension))
			{
				$extensionsNotLoaded[] = $extension;
			}
		}

		if (count($extensionsNotLoaded))
		{
			$app->enqueueMessage(Text::sprintf('COM_QUANTUMMANAGER_ERROR_EXTENSIONS', implode(',', $extensionsNotLoaded)),
				'error');

			return false;
		}

	}

	/**
	 * This method is called after a component is updated.
	 *
	 * @param   \stdClass  $parent  - Parent object calling object.
	 *
	 * @return void
	 */
	public function update($parent)
	{
		QuantummanagerHelper::setComponentsParams('helpURL', $this->helpURL);
	}

	/**
	 * Called after any type of action
	 *
	 * @param   string            $route    Which action is happening (install|uninstall|discover_install|update)
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function postflight($route, $adapter)
	{
		if ($route === 'install')
		{
			QuantummanagerHelper::setComponentsParams('helpURL', $this->helpURL);
		}
	}

}
