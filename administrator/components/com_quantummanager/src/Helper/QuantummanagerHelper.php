<?php

namespace Joomla\Component\QuantumManager\Administrator\Helper;

/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

defined('_JEXEC') or die;

use enshrined\svgSanitize\Sanitizer;
use Exception;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\WebAsset\WebAssetManager;
use Joomla\Database\DatabaseDriver;
use Joomla\Event\Event;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Joomla\Filesystem\Path;
use Joomla\Registry\Registry;
use stdClass;

class QuantummanagerHelper
{

	protected static ?Registry $cacheParams = null;

	public static array $cachePathRoot = [];

	public static string $cacheMimeType = '';

	public static string $cacheVersion = '';

	public static array $listScriptsInsert = [];

	public static array $forbiddenExtensions = [
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

	public static function checkFile(string $name, string $mimeType): bool
	{
		try
		{
			if (empty(self::$cacheMimeType))
			{
				$componentParams     = ComponentHelper::getParams('com_quantummanager');
				self::$cacheMimeType = $componentParams->get('mimetype', '');

				if (empty(self::$cacheMimeType))
				{
					self::$cacheMimeType = file_get_contents(JPATH_SITE . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, ['administrator', 'components', 'com_quantummanager', 'mimetype.txt']));
					$componentParams->set('mimetype', self::$cacheMimeType);
					$component          = new stdClass;
					$component->element = 'com_quantummanager';
					$component->params  = (string) $componentParams;

					/** @var DatabaseDriver $db */
					$db = Factory::getContainer()->get(DatabaseDriver::class);
					$db->updateObject('#__extensions', $component, ['element']);
				}

			}

			$listMimeType  = explode("\n", self::$cacheMimeType);
			$accepMimeType = [];

			foreach ($listMimeType as $value)
			{
				$type = trim($value);
				if (!preg_match('/^#.*?/', $type))
				{
					$accepMimeType[] = $type;
				}
			}

			if (!in_array($mimeType, $accepMimeType))
			{
				return false;
			}

			$nameSplit = explode('.', $name);
			if (count($nameSplit) <= 1)
			{
				return false;
			}

			$exs = mb_strtolower(array_pop($nameSplit));

			if (in_array($exs, ['php', 'php7', 'php5', 'php4', 'php3', 'php4', 'phtml', 'phps', 'sh']))
			{
				return false;
			}

			return true;
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}

	public static function filterFile($file): void
	{
		try
		{
			if (!file_exists($file))
			{
				return;
			}

			$componentParams = ComponentHelper::getParams('com_quantummanager');
			$splitFileName   = explode('.', $file);
			$exstension      = array_pop($splitFileName);

			if ($exstension === 'svg')
			{
				$filterSvg = true;

				if (
					self::isUserAdmin() && !$componentParams->get('sanitizeruploadadmin', 1)
					|| !self::isUserAdmin() && !$componentParams->get('sanitizeruploaduser', 1)
				)
				{
					$filterSvg = false;
				}

				if ($filterSvg)
				{
					$sanitizer = new Sanitizer();
					$isValid   = $sanitizer->sanitize(file_get_contents($file));

					if ($isValid)
					{
						file_put_contents($file, $isValid);
					}
					else
					{
						File::delete($file);
					}
				}
			}
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}

	public static function getActions(): Registry
	{
		$user      = Factory::getApplication()->getIdentity();
		$result    = new Registry;
		$assetName = 'com_quantummanager';
		$actions   = Access::getActionsFromFile(
			JPATH_ADMINISTRATOR . '/components/' . $assetName . '/access.xml',
			"/access/section[@name='component']/"
		);

		foreach ($actions as $action)
		{
			$result->set($action->name, $user->authorise($action->name, $assetName));
		}

		return $result;
	}

	public static function preparePathRoot(string $path, string $scopeName, bool $pathUnix = false): string
	{
		$path = trim($path);

		if (!preg_match('#^root.*#', $path))
		{
			$path = 'root/' . $path;
		}

		if (empty(static::$cachePathRoot[$scopeName]))
		{
			$scope                             = self::getScope($scopeName);
			$pathConfig                        = $scope->path;
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

		if ($pathUnix)
		{
			$path = str_replace("\\", '/', $path);
		}

		return $path;
	}

	public static function preparePath(string $path, bool $host = false, string $scopeName = '', bool $pathUnix = false): string
	{
		$path = trim($path);

		if (!preg_match('#^root.*#', $path))
		{
			$path = 'root/' . $path;
		}

		if (empty(static::$cachePathRoot[$scopeName]))
		{
			$scope                             = self::getScope($scopeName);
			$pathConfig                        = $scope->path;
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

		if (substr_count($path, '{user_id}'))
		{
			$user = Factory::getApplication()->getIdentity();
		}
		else
		{
			$user     = new stdClass();
			$user->id = 0;
		}

		if (substr_count($path, '{item_id}'))
		{
			$item_id = Factory::getApplication()->input->get('id', '0');
		}
		else
		{
			$item_id = '0';
		}

		$variables = [
			'{user_id}',
			'{item_id}',
			'{year}',
			'{month}',
			'{week_year}',
			'{week_day}',
			'{day_year}',
			'{day}',
			'{hours}',
			'{hours_24}',
			'{minutes}',
			'{second}',
			'{unix}',
		];

		$values = [
			$user->id,
			$item_id,
			date('Y'),
			date('m'),
			date('W'),
			date('w'),
			date('z'),
			date('d'),
			date('h'),
			date('H'),
			date('i'),
			date('s'),
			date('U'),
		];

		PluginHelper::importPlugin('quantummanager');
		$results = Factory::getApplication()
			->getDispatcher()
			->dispatch(
				'onQuantummanagerAddVariables',
				new Event('onQuantummanagerAddVariables', [&$params])
			);

		if (is_array($results))
		{
			foreach ($results as $result)
			{
				if (is_array($result))
				{
					foreach ($result as $item)
					{
						$variables[] = $item[0];
						$values[]    = $item[1];
					}
				}
			}
		}

		$path            = str_replace($variables, $values, $path);
		$pathConfigParse = str_replace($variables, $values, $pathConfig);

		$path            = Path::clean($path);
		$pathConfigParse = Path::clean($pathConfigParse);

		if (!preg_match('#^' . str_replace(DIRECTORY_SEPARATOR, "\\" . DIRECTORY_SEPARATOR, "\(" . Path::clean(JPATH_ROOT . DIRECTORY_SEPARATOR) . "\)?" . $pathConfigParse) . '.*?#', $path))
		{
			if (preg_match('#.*?' . str_replace(DIRECTORY_SEPARATOR, "\\" . DIRECTORY_SEPARATOR, Path::clean(JPATH_ROOT . DIRECTORY_SEPARATOR) . $pathConfigParse) . '.*?#', $path))
			{
				$path = JPATH_ROOT . DIRECTORY_SEPARATOR . $pathConfigParse . str_replace(JPATH_ROOT, '', $path);
			}
			else
			{
				$path = str_replace(JPATH_ROOT, '', $path);
			}

			$path = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $path);
		}

		$pathCurrent = str_replace(
			[
				JPATH_ROOT,
				DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR
			],
			[
				'',
				DIRECTORY_SEPARATOR
			],
			$path
		);

		$folders    = explode(DIRECTORY_SEPARATOR, $pathCurrent);
		$currentTmp = JPATH_ROOT;

		foreach ($folders as $tmpFolder)
		{
			$currentTmp .= DIRECTORY_SEPARATOR . $tmpFolder;

			if (!file_exists($currentTmp))
			{
				Folder::create($currentTmp);
			}
		}

		if ($pathUnix)
		{
			$path = str_replace("\\", '/', $path);
		}

		if ($host)
		{
			$path = Uri::root() . $path;
		}

		return trim($path, DIRECTORY_SEPARATOR);
	}

	public static function getFolderRoot(): string
	{
		$componentParams = ComponentHelper::getParams('com_quantummanager');

		return $componentParams->get('path', 'images');
	}

	public static function getParamsComponentValue(string $name, mixed $default = '', bool $withProfiles = true): mixed
	{
		$profiles = static::getComponentsParams('profiles', '');
		$value    = static::getComponentsParams($name, $default);
		$groups   = Factory::getApplication()->getIdentity()->groups;

		if ($withProfiles)
		{
			if (!empty($profiles))
			{
				foreach ($profiles as $profile)
				{
					if (in_array((int) $profile->group, $groups) && ($name === $profile->config))
					{
						$value = trim($profile->value);

						if (is_array($default))
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

	public static function loadLang(): void
	{
		$lang         = Factory::getApplication()->getLanguage();
		$extension    = 'com_quantummanager';
		$base_dir     = JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator';
		$language_tag = $lang->getTag();
		$lang->load($extension, $base_dir, $language_tag, true);
	}

	public static function formatFileSize(string $bytes, int $decimals = 2): string
	{
		$size   = array('b', 'kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb');
		$factor = floor((strlen($bytes) - 1) / 3);

		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . " " . @$size[$factor];
	}

	public static function getScope(string $scopeName)
	{

		self::checkScopes();

		if ($scopeName === '' || $scopeName === 'null')
		{
			$scopeName = 'images';
		}

		$scopes = self::getAllScope();
		$find   = null;
		$first  = $scopes[0] ?? null;

		foreach ($scopes as $scope)
		{
			$scope = (object) $scope;
			if ($scope->id === $scopeName)
			{
				$find = $scope;
			}
		}

		if ($find === null && $first !== null)
		{
			$find = $first;
		}

		return $find;
	}

	public static function getAllScope(int $enabled = 1): object|array
	{
		self::checkScopes();

		$session          = Factory::getApplication()->getSession();
		$pathSession      = $session->get('quantummanagerroot', '');
		$pathSessionCheck = $session->get('quantummanagerrootcheck', 1);
		$scopesOutput     = [];

		if (!empty($pathSession))
		{

			$checked = true;
			if ((int) $pathSessionCheck)
			{
				if (!file_exists(JPATH_ROOT . DIRECTORY_SEPARATOR . $pathSession))
				{
					$checked = false;
				}
			}

			if ($checked)
			{
				$scopesOutput = [
					(object) [
						'title' => Text::_('COM_QUANTUMMANAGER_SCOPE_FOLDER'),
						'id'    => 'sessionroot',
						'path'  => $pathSession
					]
				];
			}

		}

		$scopes       = self::getParamsComponentValue('scopes', []);
		$scopesCustom = self::getParamsComponentValue('scopescustom', []);

		if (count((array) $scopes) === 0)
		{
			$scopes = self::getDefaultScopes();
		}

		foreach ($scopes as $scope)
		{
			$scope        = (object) $scope;
			$scope->title = Text::_('COM_QUANTUMMANAGER_SCOPE_' . mb_strtoupper($scope->id));
		}

		if (!empty($scopesCustom) && count((array) $scopesCustom) > 0)
		{
			$scopes = (object) array_merge((array) $scopes, (array) $scopesCustom);
		}

		foreach ($scopes as $scope)
		{

			$scope = (object) $scope;

			if (
				isset($scope->enable)
				&& (string) $enabled === '1'
				&& !(int) $scope->enable
			)
			{
				continue;
			}

			if (empty($scope->path))
			{
				continue;
			}

			$scopesOutput[] = $scope;
		}

		return $scopesOutput;
	}

	public static function checkScopes(): void
	{
		$scopesCustom = self::getParamsComponentValue('scopescustom', [], false);
		$scopeFail    = false;
		$lang         = Factory::getApplication()->getLanguage();

		foreach ($scopesCustom as $scope)
		{
			if (empty($scope->id))
			{
				$scopeFail = true;
				$scope->id = str_replace(' ', '', $lang->transliterate($scope->title));
			}
		}

		if ($scopeFail)
		{
			self::setComponentsParams('scopescustom', $scopesCustom);
		}

	}

	public static function getDefaultScopes(): array
	{
		return [
			(object) [
				'id'     => 'images',
				'title'  => 'Images',
				'path'   => 'images',
				'enable' => 1,
			],
			(object) [
				'id'     => 'docs',
				'title'  => 'Docs',
				'path'   => 'docs',
				'enable' => 0,
			],
			(object) [
				'id'     => 'music',
				'title'  => 'Music',
				'path'   => 'music',
				'enable' => 0,
			],
			(object) [
				'id'     => 'videos',
				'title'  => 'Videos',
				'path'   => 'videos',
				'enable' => 0,
			],
		];
	}

	public static function getComponentsParams(string $name, mixed $default = null)
	{
		if (static::$cacheParams !== null)
		{
			return static::$cacheParams->get($name, $default);
		}

		$params = ComponentHelper::getParams('com_quantummanager');

		PluginHelper::importPlugin('quantummanager');
		Factory::getApplication()
			->getDispatcher()
			->dispatch(
				'onQuantumManagerConfiguration',
				new Event('onQuantumManagerConfiguration', [&$params])
			);

		static::$cacheParams = $params;

		return static::$cacheParams->get($name, $default);
	}

	public static function setComponentsParams(string $name, mixed $value)
	{
		$params = ComponentHelper::getParams('com_quantummanager');
		$params->set($name, $value);

		$componentid = ComponentHelper::getComponent('com_quantummanager')->id;
		$table       = Table::getInstance('extension');
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

	public static function cleanCache(?string $group = null, int $client_id = 0): void
	{
		$conf = Factory::getApplication()->getConfig();

		$options = [
			'defaultgroup' => !is_null($group) ? $group : Factory::getApplication()->input->get('option'),
			'cachebase'    => $client_id ? JPATH_ADMINISTRATOR . '/cache' : $conf->get('cache_path', JPATH_SITE . '/cache')
		];

		$cache = Factory::getContainer()
			->get(CacheControllerFactoryInterface::class)
			->createCacheController('callback', $options);
		$cache->clean();
	}

	public static function isSafeFile(array $file, array $options = []): bool
	{
		$defaultOptions = [
			'null_byte'                  => true,
			'forbidden_extensions'       => ['php', 'phps', 'pht', 'phtml', 'php3', 'php4', 'php5', 'php6', 'php7', 'inc', 'pl', 'cgi', 'fcgi', 'java', 'jar', 'py',],
			'php_tag_in_content'         => true,
			'shorttag_in_content'        => true,
			'shorttag_extensions'        => ['inc', 'phps', 'class', 'php3', 'php4', 'php5', 'txt', 'dat', 'tpl', 'tmpl',],
			'fobidden_ext_in_content'    => true,
			'php_ext_content_extensions' => ['zip', 'rar', 'tar', 'gz', 'tgz', 'bz2', 'tbz', 'jpa'],
		];

		$options     = array_replace($defaultOptions, $options);
		$descriptors = $file;

		if (isset($file['name']) && isset($file['tmp_name']))
		{
			$descriptors = self::decodeFileData(
				[
					$file['name'],
					$file['type'],
					$file['tmp_name'],
					$file['error'],
					$file['size'],
				]
			);
		}

		if (isset($descriptors['name']))
		{
			$descriptors = [$descriptors];
		}

		foreach ($descriptors as $fileDescriptor)
		{
			if (!isset($fileDescriptor['name']))
			{
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
				$tempNames = [$tempNames];
			}

			if (!is_array($intendedNames))
			{
				$intendedNames = [$intendedNames];
			}

			$len = count($tempNames);

			for ($i = 0; $i < $len; $i++)
			{
				$tempName     = array_shift($tempNames);
				$intendedName = array_shift($intendedNames);

				if ($options['null_byte'])
				{
					if (strstr($intendedName, "\x00"))
					{
						return false;
					}
				}

				if (!empty($options['forbidden_extensions']))
				{
					$explodedName = explode('.', $intendedName);
					$explodedName = array_reverse($explodedName);
					array_pop($explodedName);
					$explodedName = array_map('strtolower', $explodedName);

					foreach ($options['forbidden_extensions'] as $ext)
					{
						if (in_array($ext, $explodedName))
						{
							return false;
						}
					}
				}

				if (
					$options['php_tag_in_content']
					|| $options['shorttag_in_content']
					|| ($options['fobidden_ext_in_content'] && !empty($options['forbidden_extensions']))
				)
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
									$suspiciousExtensions = ['inc', 'phps', 'class', 'php3', 'php4', 'txt', 'dat', 'tpl', 'tmpl',];
								}

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
									$suspiciousExtensions = ['zip', 'rar', 'tar', 'gz', 'tgz', 'bz2', 'tbz', 'jpa',];
								}


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
									foreach ($options['forbidden_extensions'] as $ext)
									{
										if (strstr($data, '.' . $ext))
										{
											return false;
										}
									}
								}
							}

							$data = substr($data, -10);
						}

						fclose($fp);
					}
				}
			}
		}

		return true;
	}

	protected static function decodeFileData(array $data): array
	{
		$result = [];

		if (is_array($data[0]))
		{
			foreach ($data[0] as $k => $v)
			{
				$result[$k] = self::decodeFileData([$data[0][$k], $data[1][$k], $data[2][$k], $data[3][$k], $data[4][$k]]);
			}

			return $result;
		}

		return ['name' => $data[0], 'type' => $data[1], 'tmp_name' => $data[2], 'error' => $data[3], 'size' => $data[4]];
	}

	public static function escapeJsonString(string $value): string
	{
		$escapers     = ["\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c"];
		$replacements = ["\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b"];

		return str_replace($escapers, $replacements, $value);
	}

	public static function getMemoryLimit(): int
	{
		$memory_limit = ini_get('memory_limit');

		if ((string) $memory_limit === '-1')
		{
			$memory_limit = '32M';
		}

		if (preg_match('/^(\d+)(.)$/', $memory_limit, $matches))
		{
			if ($matches[2] === 'M')
			{
				$memory_limit = $matches[1] * 1024 * 1024;
			}
			else if ($matches[2] === 'K')
			{
				$memory_limit = $matches[1] * 1024;
			}
		}

		return (int) $memory_limit;
	}

	public static function isUserAdmin(): bool
	{
		$groups = Factory::getApplication()->getIdentity()->groups;

		if (in_array('2', $groups) || in_array('8', $groups))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function scriptInsertOnPage(string $name, string $script): void
	{
		if (!in_array($name, self::$listScriptsInsert))
		{
			/** @var WebAssetManager $wa */
			$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
			$wa->addInlineScript($script);
			self::$listScriptsInsert[] = $name;
		}
	}

	public static function getVersion(): string
	{
		if (!empty(self::$cacheVersion))
		{
			return self::$cacheVersion;
		}

		/** @var DatabaseDriver $db */
		$db                 = Factory::getContainer()->get(DatabaseDriver::class);
		$query              = $db->getQuery(true)
			->select('manifest_cache')
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('element') . ' = ' . $db->quote('com_quantummanager'));
		self::$cacheVersion = (new Registry($db->setQuery($query)->loadResult()))->get('version');

		return self::$cacheVersion;
	}

	public static function setHeadersNoCache(): void
	{
		$app = Factory::getApplication();
		$app->setHeader('Cache-Control', 'no-store');
		$app->sendHeaders();
	}

	public static function fileUploadMaxSize(): float|int
	{
		static $max_size = -1;

		if ($max_size < 0)
		{
			$post_max_size = static::parseSize(ini_get('post_max_size'));
			if ($post_max_size > 0)
			{
				$max_size = $post_max_size;
			}

			$upload_max = static::parseSize(ini_get('upload_max_filesize'));
			if ($upload_max > 0 && $upload_max < $max_size)
			{
				$max_size = $upload_max;
			}
		}

		return $max_size;
	}

	public static function parseSize($size): float
	{
		$unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
		$size = preg_replace('/[^0-9\.]/', '', $size);

		if ($unit)
		{
			return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
		}
		else
		{
			return round($size);
		}
	}

	public static function prepareFileExs(string $exs): string
	{
		$regex = ['#[^A-Za-z0-9]#'];

		return mb_strtolower(preg_replace($regex, '', $exs));
	}

	public static function prepareFileName(string $name): string
	{
		$lang = Factory::getApplication()->getLanguage();

		if (!(int) QuantummanagerHelper::getParamsComponentValue('translit', 0))
		{
			$nameForSafe = preg_replace('#[\-]{2,}#isu', '-', str_replace(' ', '-', $name));
			$nameForSafe = File::makeSafe($lang->transliterate($nameForSafe), ['#^\.#', '#\040#']);
		}
		else
		{
			$nameForSafe = preg_replace("#[\"\'\%\<\>]#", '', strip_tags($name));
		}

		return $nameForSafe;
	}

}
