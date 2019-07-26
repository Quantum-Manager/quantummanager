<?php
/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Cache\Cache;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;
use Joomla\Filesystem\Folder;
use Joomla\Filesystem\Path;
use Joomla\Registry\Registry;

/**
 * Quantummanager helper.
 *
 * @package     A package name
 * @since       1.0
 */
class QuantummanagerHelper
{

	/**
	 * @var string
	 * @since version
	 */
	public static $cachePathRoot = '';

	/**
	 * @var string
	 * @since version
	 */
	public static $cacheMimeType = '';

	/**
	 * @param $name
	 * @param $mimeType
	 * @return bool
	 */
	public static function checkFile($name, $mimeType)
	{
		try {

			if(empty(self::$cacheMimeType))
			{
				$componentParams = ComponentHelper::getParams('com_quantummanager');
				self::$cacheMimeType = $componentParams->get('mimetype');

				if(empty(self::$cacheMimeType) || self::$cacheMimeType === null)
				{
					self::$cacheMimeType = file_get_contents(JPATH_SITE . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, ['administrator', 'components', 'com_quantummanager', 'mimetype.txt']));
					$componentParams->set('mimetype', self::$cacheMimeType);
					$component = new stdClass();
					$component->element = 'com_quantummanager';
					$component->params = (string) $componentParams;
					Factory::getDbo()->updateObject('#__extensions', $component, ['element']);
				}

			}

			$listMimeType = explode("\n", self::$cacheMimeType);
			$accepMimeType = [];

			foreach ($listMimeType as $value) {
				$type = trim($value);
				if(!preg_match('/^#.*?/', $type))
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
			//TODO доработать фильтрацию

			/*if (file_exists($file)) {
				file_put_contents(
					$file,
					preg_replace(['/<(\?|\%)\=?(php)?/', '/(\%|\?)>/'], ['', ''], file_get_contents($file))
				);
			}*/
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

	/**
	 * @param $path
	 * @param bool $host
	 *
	 * @return mixed|null|string|string[]
	 *
	 * @since version
	 * @throws Exception
	 */
	public static function preparePath($path, $host = false)
	{
		$session = Factory::getSession();
		$path = trim($path);
		$componentParams = ComponentHelper::getParams('com_quantummanager');
		$pathConfig = '';

		if(empty(static::$cachePathRoot))
		{
			$pathConfig = static::getParamsComponentValue('path', 'images');
			$pathSession = $session->get('quantummanagerroot', '');
			static::$cachePathRoot = $pathConfig;

			if(!empty($pathSession))
			{
				$pathConfig = $pathSession;
				static::$cachePathRoot = $pathSession;
			}

		}
		else
		{
			$pathConfig = static::$cachePathRoot;
		}

		$path = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $path);
		$path = preg_replace('#' . JPATH_ROOT . "\/root?#", $pathConfig, $path);
		$path = preg_replace('#^root?#', $pathConfig, $path);
		$path = str_replace('..' . DIRECTORY_SEPARATOR, '', $path);

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

		$pathConfigParse = str_replace([
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
		], $pathConfig);

		$path = Path::clean($path);
		$pathConfigParse = Path::clean($pathConfigParse);

		//если пытаются выйти за пределы папки, то не даем этого сделать
		if(!preg_match('#^' . str_replace(DIRECTORY_SEPARATOR, "\\" . DIRECTORY_SEPARATOR, "\("  . Path::clean(JPATH_ROOT  . DIRECTORY_SEPARATOR) . "\)?" . $pathConfigParse) . '.*?#', $path))
		{
			if(preg_match('#.*?' . str_replace(DIRECTORY_SEPARATOR, "\\" . DIRECTORY_SEPARATOR, Path::clean(JPATH_ROOT  . DIRECTORY_SEPARATOR) . $pathConfigParse) . '.*?#', $path))
			{
				$path = JPATH_ROOT . DIRECTORY_SEPARATOR . $pathConfigParse . str_replace(JPATH_ROOT, '', $path);
			}
			else
			{
				$path = str_replace(JPATH_ROOT, '', $path);
			}

			$path = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $path);
		}

		$pathCurrent = str_replace([
			JPATH_ROOT,
			DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR
		], [ '', DIRECTORY_SEPARATOR ], $path);

		$folders = explode(DIRECTORY_SEPARATOR, $pathConfigParse);
		$currentTmp = '';

		foreach ($folders as $tmpFolder)
		{
			$currentTmp .= DIRECTORY_SEPARATOR . $tmpFolder;
			if(!file_exists(JPATH_ROOT . $currentTmp))
			{
				Folder::create(JPATH_ROOT . $currentTmp);
			}
		}

		if($host)
		{
			$path = Uri::root() . $path;
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


	/**
	 * @param $name
	 * @param string $default
	 *
	 * @return mixed
	 *
	 * @since version
	 */
	public static function getParamsComponentValue($name, $default = '')
	{
		$componentParams = ComponentHelper::getParams('com_quantummanager');
		$profiles = $componentParams->get('profiles', '');
		$value = $componentParams->get($name, $default);
		$groups = Factory::getUser()->groups;

		if(!empty($profiles))
		{
			foreach ($profiles as $key => $profile)
			{
				if(in_array((int)$profile->group, $groups) && ($name === $profile->config))
				{
					$value = trim($profile->value);
					break;
				}
			}
		}

		return $value;
	}


	public static function loadLang()
	{
		$lang = Factory::getLanguage();
		$extension = 'com_quantummanager';
		$base_dir = JPATH_ROOT . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, ['administrator', 'components', 'com_quantummanager']);
		$language_tag = $lang->getTag();
		$lang->load($extension, $base_dir, $language_tag, true);
	}


	/**
	 * @param $size
	 *
	 * @return string
	 *
	 * @since version
	 */
	public static function formatFileSize($size) {
		$a = [ 'B', 'KB', 'MB', 'GB', 'TB', 'PB' ];
		$pos = 0;

		while ($size >= 1024)
		{
			$size /= 1024;
			$pos++;
		}

		return round($size,2). ' ' . $a[ $pos];
	}


	/**
	 * @param $name
	 * @param $value
	 *
	 *
	 * @since version
	 */
	public static function setComponentsParams($name, $value)
	{
		$params = ComponentHelper::getParams('com_quantummanager');
		$params->set($name, $value);

		$componentid = ComponentHelper::getComponent('com_quantummanager')->id;
		$table = Table::getInstance('extension');
		$table->load($componentid);
		$table->bind(['params' => $params->toString()]);

		if (!$table->check())
		{
			echo $table->getError();
			return false;
		}

		if (!$table->store())
		{
			echo $table->getError();
			return false;
		}

		self::cleanCache('_system', 0);
		self::cleanCache('_system', 1);

	}


	/**
	 * Clean the cache
	 *
	 * @param   string   $group      The cache group
	 * @param   integer  $client_id  The ID of the client
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public static function cleanCache($group = null, $client_id = 0)
	{
		$conf = Factory::getConfig();

		$options = [
			'defaultgroup' => !is_null($group) ? $group : Factory::getApplication()->input->get('option'),
			'cachebase' => $client_id ? JPATH_ADMINISTRATOR . '/cache' : $conf->get('cache_path', JPATH_SITE . '/cache')
		];

		$cache = Cache::getInstance('callback', $options);
		$cache->clean();
	}


}
