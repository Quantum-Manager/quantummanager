<?php namespace Joomla\Component\QuantumManager\Administrator\Helper;

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

/**
 * Quantummanager helper.
 *
 * @package     A package name
 * @since       1.0
 */
class QuantummanagerLibsHelper
{

	/**
	 * @var bool
	 * @since version
	 */
	private static $flagScriptHead = false;


	/**
	 *
	 *
	 * @since version
	 */
	public static function includeScriptHead()
	{
		if (!self::$flagScriptHead)
		{

			Factory::getDocument()
				->addScriptDeclaration(file_get_contents(JPATH_ROOT . '/media/com_quantummanager/js/dispatcher.js'));
			self::$flagScriptHead = true;
		}
	}


	/**
	 * @param $includes
	 */
	public static function includes($includes)
	{
		if (is_array($includes))
		{
			foreach ($includes as $static_method)
			{
				if (method_exists(QuantummanagerLibsHelper::class, $static_method))
				{
					forward_static_call(__CLASS__ . "::" . $static_method);
				}
			}
		}

		if (is_string($includes))
		{
			if (method_exists(QuantummanagerLibsHelper::class, $includes))
			{
				forward_static_call(__CLASS__ . "::" . $includes);
			}
		}
	}


	public static function core()
	{
		HTMLHelper::_('stylesheet', 'com_quantummanager/main.css', [
			'version'  => filemtime(__FILE__),
			'relative' => true
		]);

		HTMLHelper::_('script', 'com_quantummanager/main.js', [
			'version'  => filemtime(__FILE__),
			'relative' => true
		]);

		static::theme();
	}


	public static function theme()
	{

		HTMLHelper::_('stylesheet', 'com_quantummanager/joomla.css', [
			'version'  => filemtime(__FILE__),
			'relative' => true
		]);

		HTMLHelper::_('stylesheet', 'com_quantummanager/joomla-front.css', [
			'version'  => filemtime(__FILE__),
			'relative' => true
		]);

	}


	public static function utils()
	{
		HTMLHelper::_('script', 'com_quantummanager/utils.js', [
			'version'  => filemtime(__FILE__),
			'relative' => true
		]);
	}


	public static function alert()
	{
		HTMLHelper::_('script', 'com_quantummanager/jsalert.min.js', [
			'version'  => filemtime(__FILE__),
			'relative' => true
		]);
	}


	public static function contextmenu()
	{
		HTMLHelper::_('stylesheet', 'com_quantummanager/contextual.css', [
			'version'  => filemtime(__FILE__),
			'relative' => true
		]);

		HTMLHelper::_('script', 'com_quantummanager/contentual.js', [
			'version'  => filemtime(__FILE__),
			'relative' => true
		]);
	}


	public static function clipboard()
	{
		HTMLHelper::_('script', 'com_quantummanager/clipboard.min.js', [
			'version'  => filemtime(__FILE__),
			'relative' => true
		]);
	}


	public static function notify()
	{
		HTMLHelper::_('stylesheet', 'com_quantummanager/notify.css', [
			'version'  => filemtime(__FILE__),
			'relative' => true
		]);

		HTMLHelper::_('script', 'com_quantummanager/notify.js', [
			'version'  => filemtime(__FILE__),
			'relative' => true
		]);
	}


	public static function lazyload()
	{
		HTMLHelper::_('script', 'com_quantummanager/lazyload.js', [
			'version'  => filemtime(__FILE__),
			'relative' => true
		]);
	}


	public static function dragSelect()
	{
		HTMLHelper::_('script', 'com_quantummanager/ds.min.js', [
			'version'  => filemtime(__FILE__),
			'relative' => true
		]);
	}


	public static function dynamicGrid()
	{
		HTMLHelper::_('script', 'com_quantummanager/masonry.min.js', [
			'version'  => filemtime(__FILE__),
			'relative' => true
		]);
	}


	public static function split()
	{
		HTMLHelper::_('script', 'com_quantummanager/split.min.js', [
			'version'  => filemtime(__FILE__),
			'relative' => true
		]);
	}


	public static function imageEditor()
	{
		HTMLHelper::_('stylesheet', 'com_quantummanager/cropperjs.min.css', [
			'version'  => filemtime(__FILE__),
			'relative' => true
		]);

		HTMLHelper::_('script', 'com_quantummanager/cropperjs.min.js', [
			'version'  => filemtime(__FILE__),
			'relative' => true
		]);
	}

}
