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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Filesystem\Folder;

/**
 * Quantummanager helper.
 *
 * @package     A package name
 * @since       1.0
 */
class QuantummanagerHelper
{

	/**
	 * @param $name
	 * @param $mimeType
	 * @return bool
	 */
	public static function checkFile($name, $mimeType)
	{
		try {
			$componentParams = ComponentHelper::getParams('com_quantummanager');
			$listMimeType = explode("\n", $componentParams->get('mimetype'. file_get_contents(JPATH_SITE . implode(DIRECTORY_SEPARATOR, 'administrator', 'components', 'com_quantummanager', 'mimetype.txt'))));
			$accepMimeType = [];

			foreach ($listMimeType as $value) {
				$type = trim($value);
				if(!preg_match("/^#.*?/", $type))
				{
					$accepMimeType[] = $type;
				}
			}

			if(!in_array($mimeType, $accepMimeType))
			{
				return false;
			}


			$nameSplit = explode('.', $name);
			if(count($nameSplit) <= 1)
			{
				return false;
			}

			$exs = mb_strtolower(array_pop($nameSplit));

			if(in_array($exs, ['php', 'php7', 'php5', 'php4', 'php3', 'php4', 'phtml', 'phps', 'sh', 'exe']))
			{
				return false;
			}

			return true;
		}
		catch (Exception $e) {
			echo $e->getMessage();
		}
	}


	/**
	 * @param $file
	 */
	public static function filterFile($file)
	{
		try {
			if (file_exists($file)) {
				file_put_contents(
					$file,
					preg_replace(['/<(\?|\%)\=?(php)?/', '/(\%|\?)>/'], ['', ''], file_get_contents($file))
				);

			}
		}
		catch (Exception $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * @return JObject
	 */
	public static function getActions()
	{
		$user = JFactory::getUser();
		$result = new JObject;
		$assetName = 'com_quantummanager';
		$actions = JAccess::getActions($assetName);
		foreach ( $actions as $action )
		{
			$result->set( $action->name, $user->authorise( $action->name, $assetName ) );
		}
		return $result;
	}

	public static function buildPath($path)
	{

	}

	/**
	 * @param string $path
	 * @return string
	 */
	public static function preparePath($path)
	{
		$path = trim($path);

		if(substr_count($path, '{user_id}'))
		{
			$user = Factory::getUser();
		}
		else
		{
			$user = new stdClass();
			$user->id = 0;
		}

		if(substr_count($path, '{item_id}'))
		{
			$item_id = Factory::getApplication()->input->get('id', '0');
		}
		else
		{
			$item_id = '0';
		}

		$path = str_replace([
			'{user_id}',
			'{item_id}',
			'{year}',
			'{month}',
			'{day}',
			'{hours}',
			'{minutes}',
			'{second}',
			'{unix}',
		], [
			$user->id,
			$item_id,
			date('Y'),
			date('m'),
			date('d'),
			date('h'),
			date('i'),
			date('s'),
			date('U'),
		], $path);

		$path = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $path);
		$path = preg_replace("#" . JPATH_SITE . "\/root(\/)?#", '', $path);
		$path = preg_replace("#^root(\/)?#", '', $path);
		$path = str_replace('..' . DIRECTORY_SEPARATOR, '', $path);

		$folders = explode(DIRECTORY_SEPARATOR, $path);
		$currentTmp = '';

		foreach ($folders as $tmpFolder)
		{
			$currentTmp .= DIRECTORY_SEPARATOR . $tmpFolder;
			if(!file_exists(JPATH_ROOT . $currentTmp))
			{
				Folder::create(JPATH_ROOT . $currentTmp);
			}
		}

		return $path;
	}


	/**
	 * @return mixed|string
	 */
	public static function getFolderRoot()
	{
		$componentParams = ComponentHelper::getParams('com_quantummanager');
		$folderRoot = $componentParams->get('path', 'images');

		if($folderRoot === 'root') {
			$folderRoot = 'root';
		}

		return $folderRoot;
	}


}
