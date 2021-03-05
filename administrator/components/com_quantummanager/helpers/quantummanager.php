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
use Joomla\CMS\Filter\InputFilter;
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
	public static $cachePathRoot = [];

	/**
	 * @var string
	 * @since version
	 */
	public static $cacheMimeType = '';


    /**
     * @var null
     */
	public static $cacheVersion = null;


    /**
     * @var array
     */
	public static $listScriptsInsert = [];


	/**
	 * @var array
	 * @since version
	 */
	public static $forbiddenExtensions = [
		'php',
		'phps',
		'pht',
		'phtml',
		'php3',
		'php4',
		'php5',
		'php6',
		'php7',
		'inc',
		'pl',
		'cgi',
		'fcgi',
		'java',
		'jar',
		'py',
		'htaccess'
	];


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

			if(in_array($exs, ['php', 'php7', 'php5', 'php4', 'php3', 'php4', 'phtml', 'phps', 'sh']))
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
     * @param $scopeName
     * @param bool $pathUnix
     * @return string|string[]
     * @throws Exception
     */
	public static function preparePathRoot($path, $scopeName, $pathUnix = false)
    {
        $session = Factory::getSession();
        $path = trim($path);
        $componentParams = ComponentHelper::getParams('com_quantummanager');
        $pathConfig = '';

        if(empty(static::$cachePathRoot[$scopeName]))
        {
            $scope = self::getScope($scopeName);
            $pathConfig = $scope->path;
            static::$cachePathRoot[$scopeName] = $pathConfig;
        }
        else
        {
            $pathConfig = static::$cachePathRoot[$scopeName];
        }


        $path = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $path);
        $path = preg_replace('#' . str_replace('\\', '\\\\', JPATH_ROOT) . "\/root?#", $pathConfig, $path);
        $path = preg_replace('#^root?#', $pathConfig, $path);
        $path = str_replace('..' . DIRECTORY_SEPARATOR, '', $path);

        $path = Path::clean($path);

        if($pathUnix)
        {
            $path = str_replace("\\",'/', $path);
        }

        return $path;
    }


	/**
	 * @param $path
	 * @param bool $host
	 * @param string $scopeName
	 * @param bool $pathUnix
	 *
	 * @return string
	 *
	 * @throws Exception
	 * @since version
	 */
	public static function preparePath($path, $host = false, $scopeName = '', $pathUnix = false)
	{
		$session = Factory::getSession();
		$path = trim($path);
		$componentParams = ComponentHelper::getParams('com_quantummanager');
		$pathConfig = '';

		if(empty(static::$cachePathRoot[$scopeName]))
		{
			$scope = self::getScope($scopeName);
			$pathConfig = $scope->path;
			static::$cachePathRoot[$scopeName] = $pathConfig;
		}
		else
		{
			$pathConfig = static::$cachePathRoot[$scopeName];
		}


		$path = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $path);
		$path = preg_replace('#' . str_replace('\\', '\\\\', JPATH_ROOT) . "\/root?#", $pathConfig, $path);
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

		if($pathUnix)
		{
			$path = str_replace("\\",'/', $path);
		}

		if($host)
		{
			$path = Uri::root() . $path;
		}

		return trim($path,DIRECTORY_SEPARATOR);
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
	 * @param bool $withProfiles
	 *
	 * @return mixed|string
	 *
	 * @since version
	 */
	public static function getParamsComponentValue($name, $default = '', $withProfiles = true)
	{
		$componentParams = ComponentHelper::getParams('com_quantummanager');
		$profiles = $componentParams->get('profiles', '');
		$value = $componentParams->get($name, $default);
		$groups = Factory::getUser()->groups;

		if($withProfiles)
		{
			if(!empty($profiles))
			{
				foreach ($profiles as $key => $profile)
				{
					if(in_array((int)$profile->group, $groups) && ($name === $profile->config))
					{
						$value = trim($profile->value);

						if(is_array($default))
						{
							$value = json_decode($value, true);
						}

						break;
					}
				}
			}
		}

		return $value;
	}


	public static function loadLang()
	{
		$lang = Factory::getLanguage();
		$extension = 'com_quantummanager';
		$base_dir = JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator';
		$language_tag = $lang->getTag();
		$lang->load($extension, $base_dir, $language_tag, true);
	}


	/**
	 * @param $bytes
	 * @param int $decimals
	 *
	 * @return string
	 *
	 * @since version
	 */
	public static function formatFileSize($bytes, $decimals = 2)
	{
		$size = array('b','kb','Mb','Gb','Tb','Pb','Eb','Zb','Yb');
		$factor = floor((strlen($bytes) - 1) / 3);
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . " " . @$size[$factor];
	}


	/**
	 * @param $scopeName
	 *
	 *
	 * @throws Exception
	 * @since version
	 */
	public static function getScope($scopeName)
	{

		self::checkScopes();

		if($scopeName === '' || $scopeName === 'null')
		{
			$scopeName = 'images';
		}

		$scopes = self::getAllScope();

		foreach ($scopes as $scope)
		{
			$scope = (object)$scope;
			if($scope->id === $scopeName)
			{
				return $scope;
			}
		}
	}


	/**
	 * @param int $enabled
	 *
	 * @return array|object
	 *
	 * @since version
	 */
	public static function getAllScope($enabled = 1)
	{
		self::checkScopes();

		$session = Factory::getSession();
		$pathSession = $session->get('quantummanagerroot', '');
		$pathSessionCheck = $session->get('quantummanagerrootcheck', 1);
		$scopesOutput = [];

		if(!empty($pathSession))
		{

		    $checked = true;
		    if((int)$pathSessionCheck)
            {
                if(!file_exists(JPATH_ROOT . DIRECTORY_SEPARATOR . $pathSession))
                {
                    $checked = false;
                }
            }

			if($checked)
			{
				$scopesOutput = [
					(object)[
						'title' => Text::_('COM_QUANTUMMANAGER_SCOPE_FOLDER'),
						'id' => 'sessionroot',
						'path' => $pathSession
					]
				];
			}

		}

		$scopes = self::getParamsComponentValue('scopes', []);
		$scopesCustom = self::getParamsComponentValue('scopescustom', []);

		if(count((array)$scopes) === 0)
		{
			$scopes = self::getDefaultScopes();
		}

		foreach ($scopes as $scope)
		{
			$scope->title = Text::_('COM_QUANTUMMANAGER_SCOPE_' . mb_strtoupper($scope->id));
		}

		if (!empty($scopesCustom) && count((array)$scopesCustom) > 0)
		{
			$scopes = (object)array_merge((array)$scopes, (array)$scopesCustom);
		}

		foreach ($scopes as $scope)
		{

			$scope = (object)$scope;

			if (isset($scope->enable))
			{
				if((string)$enabled === '1')
				{
					if (!(int)$scope->enable)
					{
						continue;
					}
				}

			}

			if (empty($scope->path))
			{
				continue;
			}

			$scopesOutput[] = $scope;
		}


		return $scopesOutput;
	}


	public static function checkScopes()
	{
		$scopesCustom = self::getParamsComponentValue('scopescustom', [], false);
		$scopeFail = false;
		$lang = Factory::getLanguage();

		foreach ($scopesCustom as $scope)
		{
			if(empty($scope->id))
			{
				$scopeFail = true;
				$scope->id = str_replace(' ', '', $lang->transliterate($scope->title));
			}
		}

		if($scopeFail)
		{
			self::setComponentsParams('scopescustom', $scopesCustom);
		}

	}



	/**
	 *
	 * @return array
	 *
	 * @since version
	 */
	public static function getDefaultScopes()
	{
		return [
			(object)[
				'id' => 'images',
				'title' => 'Images',
				'path' => 'images',
				'enable' => 1,
			],
			(object)[
				'id' => 'docs',
				'title' => 'Docs',
				'path' => 'docs',
				'enable' => 0,
			],
			(object)[
				'id' => 'music',
				'title' => 'Music',
				'path' => 'music',
				'enable' => 0,
			],
			(object)[
				'id' => 'videos',
				'title' => 'Videos',
				'path' => 'videos',
				'enable' => 0,
			],
		];
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


	/**
	 * Checks an uploaded for suspicious naming and potential PHP contents which could indicate a hacking attempt.
	 *
	 * The options you can define are:
	 * null_byte                   Prevent files with a null byte in their name (buffer overflow attack)
	 * forbidden_extensions        Do not allow these strings anywhere in the file's extension
	 * php_tag_in_content          Do not allow `<?php` tag in content
	 * shorttag_in_content         Do not allow short tag `<?` in content
	 * shorttag_extensions         Which file extensions to scan for short tags in content
	 * fobidden_ext_in_content     Do not allow forbidden_extensions anywhere in content
	 * php_ext_content_extensions  Which file extensions to scan for .php in content
	 *
	 * This code is an adaptation and improvement of Admin Tools' UploadShield feature,
	 * relicensed and contributed by its author.
	 *
	 * @param   array  $file     An uploaded file descriptor
	 * @param   array  $options  The scanner options (see the code for details)
	 *
	 * @return  boolean  True of the file is safe
	 *
	 * @since   3.4
	 */
	public static function isSafeFile($file, $options = array())
	{
		$defaultOptions = array(

			// Null byte in file name
			'null_byte'                  => true,

			// Forbidden string in extension (e.g. php matched .php, .xxx.php, .php.xxx and so on)
			'forbidden_extensions'       => array(
				'php', 'phps', 'pht', 'phtml', 'php3', 'php4', 'php5', 'php6', 'php7', 'inc', 'pl', 'cgi', 'fcgi', 'java', 'jar', 'py',
			),

			// <?php tag in file contents
			'php_tag_in_content'         => true,

			// <? tag in file contents
			'shorttag_in_content'        => true,

			// Which file extensions to scan for short tags
			'shorttag_extensions'        => array(
				'inc', 'phps', 'class', 'php3', 'php4', 'php5', 'txt', 'dat', 'tpl', 'tmpl',
			),

			// Forbidden extensions anywhere in the content
			'fobidden_ext_in_content'    => true,

			// Which file extensions to scan for .php in the content
			'php_ext_content_extensions' => array('zip', 'rar', 'tar', 'gz', 'tgz', 'bz2', 'tbz', 'jpa'),
		);

		$options = array_replace($defaultOptions, $options);

		// Make sure we can scan nested file descriptors
		$descriptors = $file;

		if (isset($file['name']) && isset($file['tmp_name']))
		{
			$descriptors = self::decodeFileData(
				array(
					$file['name'],
					$file['type'],
					$file['tmp_name'],
					$file['error'],
					$file['size'],
				)
			);
		}

		// Handle non-nested descriptors (single files)
		if (isset($descriptors['name']))
		{
			$descriptors = array($descriptors);
		}

		// Scan all descriptors detected
		foreach ($descriptors as $fileDescriptor)
		{
			if (!isset($fileDescriptor['name']))
			{
				// This is a nested descriptor. We have to recurse.
				if (!self::isSafeFile($fileDescriptor, $options))
				{
					return false;
				}

				continue;
			}

			$tempNames     = $fileDescriptor['tmp_name'];
			$intendedNames = $fileDescriptor['name'];

			if (!is_array($tempNames))
			{
				$tempNames = array($tempNames);
			}

			if (!is_array($intendedNames))
			{
				$intendedNames = array($intendedNames);
			}

			$len = count($tempNames);

			for ($i = 0; $i < $len; $i++)
			{
				$tempName     = array_shift($tempNames);
				$intendedName = array_shift($intendedNames);

				// 1. Null byte check
				if ($options['null_byte'])
				{
					if (strstr($intendedName, "\x00"))
					{
						return false;
					}
				}

				// 2. PHP-in-extension check (.php, .php.xxx[.yyy[.zzz[...]]], .xxx[.yyy[.zzz[...]]].php)
				if (!empty($options['forbidden_extensions']))
				{
					$explodedName = explode('.', $intendedName);
					$explodedName =	array_reverse($explodedName);
					array_pop($explodedName);
					$explodedName = array_map('strtolower', $explodedName);

					/*
					 * DO NOT USE array_intersect HERE! array_intersect expects the two arrays to
					 * be set, i.e. they should have unique values.
					 */
					foreach ($options['forbidden_extensions'] as $ext)
					{
						if (in_array($ext, $explodedName))
						{
							return false;
						}
					}
				}

				// 3. File contents scanner (PHP tag in file contents)
				if ($options['php_tag_in_content']
					|| $options['shorttag_in_content']
					|| ($options['fobidden_ext_in_content'] && !empty($options['forbidden_extensions'])))
				{
					$fp = @fopen($tempName, 'r');

					if ($fp !== false)
					{
						$data = '';

						while (!feof($fp))
						{
							$data .= @fread($fp, 131072);

							if ($options['php_tag_in_content'] && stristr($data, '<?php'))
							{
								return false;
							}

							if ($options['shorttag_in_content'])
							{
								$suspiciousExtensions = $options['shorttag_extensions'];

								if (empty($suspiciousExtensions))
								{
									$suspiciousExtensions = array(
										'inc', 'phps', 'class', 'php3', 'php4', 'txt', 'dat', 'tpl', 'tmpl',
									);
								}

								/*
								 * DO NOT USE array_intersect HERE! array_intersect expects the two arrays to
								 * be set, i.e. they should have unique values.
								 */
								$collide = false;

								foreach ($suspiciousExtensions as $ext)
								{
									if (in_array($ext, $explodedName))
									{
										$collide = true;

										break;
									}
								}

								if ($collide)
								{
									// These are suspicious text files which may have the short tag (<?) in them
									if (strstr($data, '<?'))
									{
										return false;
									}
								}
							}

							if ($options['fobidden_ext_in_content'] && !empty($options['forbidden_extensions']))
							{
								$suspiciousExtensions = $options['php_ext_content_extensions'];

								if (empty($suspiciousExtensions))
								{
									$suspiciousExtensions = array(
										'zip', 'rar', 'tar', 'gz', 'tgz', 'bz2', 'tbz', 'jpa',
									);
								}

								/*
								 * DO NOT USE array_intersect HERE! array_intersect expects the two arrays to
								 * be set, i.e. they should have unique values.
								 */
								$collide = false;

								foreach ($suspiciousExtensions as $ext)
								{
									if (in_array($ext, $explodedName))
									{
										$collide = true;

										break;
									}
								}

								if ($collide)
								{
									/*
									 * These are suspicious text files which may have an executable
									 * file extension in them
									 */
									foreach ($options['forbidden_extensions'] as $ext)
									{
										if (strstr($data, '.' . $ext))
										{
											return false;
										}
									}
								}
							}

							/*
							 * This makes sure that we don't accidentally skip a <?php tag if it's across
							 * a read boundary, even on multibyte strings
							 */
							$data = substr($data, -10);
						}

						fclose($fp);
					}
				}
			}
		}

		return true;
	}


	/**
	 * Method to decode a file data array.
	 *
	 * @param   array  $data  The data array to decode.
	 *
	 * @return  array
	 *
	 * @since   3.4
	 */
	protected static function decodeFileData(array $data)
	{
		$result = array();

		if (is_array($data[0]))
		{
			foreach ($data[0] as $k => $v)
			{
				$result[$k] = self::decodeFileData(array($data[0][$k], $data[1][$k], $data[2][$k], $data[3][$k], $data[4][$k]));
			}

			return $result;
		}

		return array('name' => $data[0], 'type' => $data[1], 'tmp_name' => $data[2], 'error' => $data[3], 'size' => $data[4]);
	}


	/**
	 * @param $value
	 *
	 * @return mixed
	 *
	 * @since version
	 */
	public static function escapeJsonString($value)
	{
		$escapers = array("\\",     "/",   "\"",  "\n",  "\r",  "\t", "\x08", "\x0c");
		$replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t",  "\\f",  "\\b");
		return str_replace($escapers, $replacements, $value);
	}


    /**
     * @return int
     */
	public static function getMemoryLimit()
    {
        $memory_limit = ini_get('memory_limit');

        if((string)$memory_limit === '-1')
        {
            $memory_limit = '32M';
        }

        if (preg_match('/^(\d+)(.)$/', $memory_limit, $matches))
        {
            if ($matches[2] === 'M')
            {
                $memory_limit = $matches[1] * 1024 * 1024; // nnnM -> nnn MB
            }
            else if ($matches[2] === 'K')
            {
                $memory_limit = $matches[1] * 1024; // nnnK -> nnn KB
            }
        }

        return (int)$memory_limit;
    }


    /**
     * @return bool
     */
    public static function isUserAdmin()
    {
        $groups = Factory::getUser()->groups;
        if(in_array('2', $groups) || in_array('8', $groups))
        {
            return true;
        }
        else
        {
            return false;
        }
    }


    public static function scriptInsertOnPage($name, $script)
    {
        if(!in_array($name, self::$listScriptsInsert))
        {
            Factory::getDocument()->addScriptDeclaration($script);
            self::$listScriptsInsert[] = $name;
        }
    }


    public static function getVersion()
    {
        if (!is_null(self::$cacheVersion))
        {
            return self::$cacheVersion;
        }

        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('manifest_cache')
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('element') . ' = ' . $db->quote('com_quantummanager'));
        self::$cacheVersion = (new Registry($db->setQuery($query)->loadResult()))->get('version');
        return self::$cacheVersion;
    }


}
