<?php
/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;


/**
 * Quantummanager Controller.
 *
 * @package  quantummanager
 * @since    1.0
 */
class QuantummanagerController extends BaseController
{


	/**
	 * Method overridden to make bridges for the front
	 *
	 * @param $prefix
	 * @param $config
	 *
	 * @return JControllerLegacy|mixed
	 *
	 * @throws Exception
	 * @since versio
	 *
	 */
	public static function getInstance($prefix, $config = array())
	{
		if (is_object(self::$instance))
		{
			return self::$instance;
		}

		// проверяем на включенность параметра
		JLoader::register('QuantummanagerHelper', JPATH_ADMINISTRATOR . '/components/com_quantummanager/helpers/quantummanager.php');

		if (!(int) QuantummanagerHelper::getParamsComponentValue('front', 0))
		{
			throw new \InvalidArgumentException(Text::sprintf('JLIB_APPLICATION_ERROR_NOT_ACCESS'));
		}

		// проверяем что пользователь авторизован
		if (Factory::getUser()->id === 0)
		{
			throw new \InvalidArgumentException(Text::sprintf('JLIB_APPLICATION_ERROR_NOT_ACCESS'));
		}

		// грузим языки
		Factory::getLanguage()->load('com_quantummanager', JPATH_ADMINISTRATOR);

		$input = Factory::getApplication()->input;
		Factory::getApplication()->input->set('view', 'quantummanager');

		// Get the environment configuration.
		$format  = $input->getWord('format');
		$command = $input->get('task', 'display');

		// Check for array format.
		$filter = InputFilter::getInstance();

		if (is_array($command))
		{
			$command = $filter->clean(array_pop(array_keys($command)), 'cmd');
		}
		else
		{
			$command = $filter->clean($command, 'cmd');
		}

		// Check for a controller.task command.
		if (strpos($command, '.') !== false)
		{
			// Explode the controller.task command.
			list ($type, $task) = explode('.', $command);

			// Define the controller filename and path.
			$file       = self::createFileName('controller', array('name' => $type, 'format' => $format));
			$path       = JPATH_ROOT . '/administrator/components/com_quantummanager/controllers/' . $file;
			$backuppath = JPATH_ROOT . '/administrator/components/com_quantummanager/controller/' . $file;

			// Reset the task without the controller context.
			$input->set('task', $task);
		}
		else
		{
			// Base controller.
			$type = null;

			// Define the controller filename and path.
			$file       = self::createFileName('controller', array('name' => 'controller', 'format' => $format));
			$path       = JPATH_ROOT . '/administrator/components/com_quantummanager/' . $file;
			$backupfile = self::createFileName('controller', array('name' => 'controller'));
			$backuppath = JPATH_ROOT . '/administrator/components/com_quantummanager/' . $backupfile;
		}

		// Get the controller class name.
		$class = ucfirst($prefix) . 'Controller' . ucfirst($type);

		// Include the class if not present.
		if (!class_exists($class))
		{
			// If the controller file path exists, include it.
			if (file_exists($path))
			{
				require_once $path;
			}
			elseif (isset($backuppath) && file_exists($backuppath))
			{
				require_once $backuppath;
			}
			else
			{
				throw new \InvalidArgumentException(\JText::sprintf('JLIB_APPLICATION_ERROR_INVALID_CONTROLLER', $type, $format));
			}
		}

		// Instantiate the class.
		if (!class_exists($class))
		{
			throw new \InvalidArgumentException(\JText::sprintf('JLIB_APPLICATION_ERROR_INVALID_CONTROLLER_CLASS', $class));
		}

		// Instantiate the class, store it to the static container, and return it
		return self::$instance = new $class($config);
	}

}
