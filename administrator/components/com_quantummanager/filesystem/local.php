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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Joomla\Filesystem\Path;

JLoader::register('QuantummanagerHelper', JPATH_SITE . '/administrator/components/com_quantummanager/helpers/quantummanager.php');
JLoader::register('QuantummanagerHelperImage', JPATH_ROOT . '/administrator/components/com_quantummanager/helpers/image.php');

class QuantummanagerFileSystemLocal
{

	/**
	 * @param $path
	 * @param $scope
	 * @param $name
	 *
	 * @return false|string
	 *
	 * @throws Exception
	 * @since version
	 */
	public static function createDirectory($path, $scope, $name)
	{

		$actions = QuantummanagerHelper::getActions();
		if (!$actions->get('core.create'))
		{
			return json_encode(['fail']);
		}

		$path = JPATH_SITE . DIRECTORY_SEPARATOR . QuantummanagerHelper::preparePath($path, false, $scope);
		$lang = Factory::getLanguage();

		if (file_exists($path))
		{
			if (!(int) QuantummanagerHelper::getParamsComponentValue('translit', 0))
			{
				$nameForSafe = preg_replace('#[\-]{2,}#isu', '-', str_replace(' ', '-', $name));
				$nameForSafe = File::makeSafe($lang->transliterate($nameForSafe), ['#^\.#', '#\040#']);
			}
			else
			{
				$nameForSafe = $name;
			}

			Folder::create($path . DIRECTORY_SEPARATOR . $nameForSafe);

			return json_encode(['ok']);
		}

		return json_encode(['fail']);

	}


	/**
	 * @param           $path
	 * @param           $root
	 * @param   string  $scopeSource
	 *
	 * @return false|string
	 *
	 * @throws Exception
	 * @since version
	 */
	public static function getScopesDirectories($path, $root, $scopeSource = 'all')
	{
		$scopes = QuantummanagerHelper::getAllScope();

		if ($scopeSource === 'all')
		{
			foreach ($scopes as $scope)
			{
				$path     = $scope->path;
				$path     = DIRECTORY_SEPARATOR . QuantummanagerHelper::preparePath($path);
				$pathArr  = explode(DIRECTORY_SEPARATOR, $path);
				$pathCurr = '';

				if (!file_exists(JPATH_ROOT . DIRECTORY_SEPARATOR . $path))
				{
					//создаем папку для области, если ее нет
					foreach ($pathArr as $iValue)
					{
						$pathCurr .= DIRECTORY_SEPARATOR . $iValue;
						if (!file_exists(JPATH_ROOT . DIRECTORY_SEPARATOR . $pathCurr))
						{
							Folder::create(JPATH_ROOT . DIRECTORY_SEPARATOR . $pathCurr);
						}
					}
				}

				$directories[] = static::showdir(JPATH_ROOT . DIRECTORY_SEPARATOR . $path, $root, $scope->title, $scope->id, true, true);
			}
		}
		else
		{
			foreach ($scopes as $scope)
			{
				if ($scope->id === $scopeSource)
				{
					$path          = $scope->path;
					$path          = JPATH_ROOT . DIRECTORY_SEPARATOR . QuantummanagerHelper::preparePath($path);
					$directories[] = static::showdir($path, $root, $scope->title, $scope->id, true, true);
					break;
				}
			}
		}

		return json_encode([
			'directories' => $directories,
		], false, 1000);
	}

	/**
	 * @param $path
	 * @param $root
	 *
	 * @return string
	 *
	 * @since version
	 */
	public static function getDirectories($path, $root)
	{
		$path        = JPATH_ROOT . DIRECTORY_SEPARATOR . QuantummanagerHelper::preparePath($path);
		$directories = static::showdir($path, $root, '', true, true);

		return json_encode([
			'directories' => $directories
		]);
	}


	/**
	 * @param           $dir
	 * @param   string  $root
	 * @param   string  $scope
	 * @param   bool    $folderOnly
	 * @param   bool    $showRoot
	 * @param   int     $level
	 * @param   string  $ef
	 *
	 * @return array|string
	 */
	protected static function showdir
	(
		$dir,
		$root = '',
		$scopeTitle = '',
		$scopeId = '',
		$folderOnly = false,
		$showRoot = false,
		$level = 0,  // do not use!!!
		$ef = ''     // do not use!!!
	)
	{

		$html = '';
		if ((int) $level == 0)
		{
			$dir = realpath($dir);
			$ef  = ($showRoot ? realpath($dir . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR : $dir . DIRECTORY_SEPARATOR);
		}

		if (!file_exists($dir))
		{
			return '';
		}

		if ($showRoot && (int) $level == 0)
		{
			$subdir = static::showdir($dir, $root, $scopeTitle, $scopeId, $folderOnly, $showRoot, $level + 1, $ef);

			return [
				//'path' => QuantummanagerHelper::getFolderRoot(),
				'path'     => $root,
				'title'    => $scopeTitle,
				'scopeid'  => $scopeId,
				'subpath'  => $subdir,
				'is_empty' => (int) static::dirIisEmpty($dir)
			];
		}

		$list = @scandir($dir);
		if (is_array($list))
		{
			$list = array_diff($list, ['.', '..']);
			if ($list)
			{
				$folders = [];

				foreach ($list as $name)
				{
					if (is_dir($dir . DIRECTORY_SEPARATOR . $name))
					{

						$folders[] = [
							'path'     => $name,
							'subpath'  => static::showdir($dir . DIRECTORY_SEPARATOR . $name, $root, $scopeTitle, $scopeId, $folderOnly, $showRoot, $level + 1, $ef),
							'is_empty' => (int) static::dirIisEmpty($dir . DIRECTORY_SEPARATOR . $name)
						];
					}
					else
					{
						$files[] = $name;
					}
				}

				//sort($folders);
				return $folders;

			}
		}

		return [];

	}


	/**
	 * @param        $dir
	 * @param   int  $level
	 *
	 * @return array|int
	 *
	 * @since version
	 */
	protected static function getSizeDirectory($dir, $level = 0)
	{
		$directories      = Folder::folders($dir);
		$files            = Folder::files($dir, '');
		$size             = 0;
		$sizeCurrent      = 0;
		$filesCount       = count($files);
		$directoriesCount = count($directories);

		foreach ($files as $file)
		{
			$size        += filesize($dir . DIRECTORY_SEPARATOR . $file);
			$sizeCurrent += filesize($dir . DIRECTORY_SEPARATOR . $file);
		}

		if ($level === -1)
		{
			return [
				'size'             => $size,
				'directoriesCount' => count($directories),
				'filesCount'       => count($files)
			];
		}

		foreach ($directories as $directory)
		{
			$search           = static::getSizeDirectory($dir . DIRECTORY_SEPARATOR . $directory, $level + 1);
			$size             += $search['size'];
			$filesCount       += $search['filesCount'];
			$directoriesCount += $search['directoriesCount'];
		}

		if ($level === 0)
		{
			return [
				'size'                    => $size,
				'sizeCurrent'             => $sizeCurrent,
				'directoriesCount'        => $directoriesCount,
				'directoriesCountCurrent' => count($directories),
				'filesCount'              => $filesCount,
				'filesCountCurrent'       => count($files),
			];
		}
		else
		{
			return [
				'size'             => $size,
				'directoriesCount' => $directoriesCount,
				'filesCount'       => $filesCount
			];
		}
	}


	/**
	 * @throws Exception
	 */
	public static function upload()
	{
		try
		{

			$actions = QuantummanagerHelper::getActions();
			if (!$actions->get('core.create'))
			{
				return json_encode(['fail']);
			}

			$output         = [];
			$app            = Factory::getApplication();
			$data           = $app->input->getArray();
			$file           = $app->input->files->get('file', null, 'raw');
			$contentcheck   = (int) QuantummanagerHelper::getParamsComponentValue('contentcheck', 1);
			$optionsForSafe = [
				'forbidden_extensions' => QuantummanagerHelper::$forbiddenExtensions,
			];

			if (!$contentcheck)
			{
				$optionsForSafe['php_ext_content_extensions'] = ['null'];
			}


			if($file === null || !isset($file['name']))
			{
				$output['error'] = Text::_('COM_QUANTUMMANAGER_ERROR_FILE_NOTFOUND');

				return json_encode($output);
			}

			if (!QuantummanagerHelper::isSafeFile($file, $optionsForSafe))
			{
				$output['error'] = Text::_('COM_QUANTUMMANAGER_ERROR_PARTIAL_UPLOAD');

				return json_encode($output);
			}

			if ($file['error'] == 4)
			{
				$output['error'] = Text::_('COM_QUANTUMMANAGER_ERROR_PARTIAL_UPLOAD');

				return json_encode($output);
			}

			if ($file['error'])
			{

				switch ($file['error'])
				{
					case 1:
						$output['error'] = Text::_('COM_QUANTUMMANAGER_ERROR_FILE_TO_LARGE_THAN_PHP_INI_ALLOWS');
						break;

					case 2:
						$output['error'] = Text::_('COM_QUANTUMMANAGER_ERROR_FILE_TO_LARGE_THAN_HTML_FORM_ALLOWS');
						break;

					case 3:
						$output['error'] = Text::_('COM_QUANTUMMANAGER_ERROR_PARTIAL_UPLOAD');
				}

			}
			else
			{
				$lang      = Factory::getLanguage();
				$nameSplit = explode('.', $file['name']);
				$nameExs   = mb_strtolower(array_pop($nameSplit));

				if (!(int) QuantummanagerHelper::getParamsComponentValue('translit', 0))
				{
					$nameForSafe = preg_replace('#[\-]{2,}#isu', '-', str_replace(' ', '-', implode('_', $nameSplit)));
					$nameForSafe = File::makeSafe($lang->transliterate($nameForSafe), ['#^\.#', '#\040#']);
				}
				else
				{
					$nameForSafe = implode('.', $nameSplit);
				}

				$maxSizeFileName = (int) QuantummanagerHelper::getParamsComponentValue('maxsizefilename', 63);

				if (mb_strlen($nameForSafe) > $maxSizeFileName && $maxSizeFileName > 0)
				{
					$nameSafe = mb_substr($nameForSafe, 0, $maxSizeFileName) . '_p' . mt_rand(11111, 99999);
				}
				else
				{
					$nameSafe = $nameForSafe . ((int) QuantummanagerHelper::getParamsComponentValue('postfix', 0) ? ('_p' . mt_rand(11111, 99999)) : '');
				}

				if ((int) QuantummanagerHelper::getParamsComponentValue('hashname', 0))
				{
					$nameSafe = md5($nameSafe);
				}

				$uploadedFileName = $nameSafe . '.' . $nameExs;
				$exs              = explode(',', 'jpg,jpeg,png,gif,webp');
				$type             = preg_replace("/\/.*?$/isu", '', $file['type']);
				$data['name']     = isset($data['name']) ? $data['name'] : '';
				$path_source      = QuantummanagerHelper::preparePathRoot($data['path'], $data['scope']);
				$path             = JPATH_ROOT . DIRECTORY_SEPARATOR . QuantummanagerHelper::preparePath($data['path'], false, $data['scope']);

				if (!QuantummanagerHelper::checkFile($file['name'], $file['type']))
				{
					$output['error'] = Text::_('COM_QUANTUMMANAGER_ERROR_UPLOAD_ACCESS') . ': ' . (empty($file['type']) ? Text::_('COM_QUANTUMMANAGER_EMPTY_MIMETYPE') : $file['type']);

					return json_encode($output);
				}

				if (!file_exists($path))
				{
					Folder::create($path);
				}

				if (File::upload($file['tmp_name'], $path . DIRECTORY_SEPARATOR . $uploadedFileName))
				{

					QuantummanagerHelper::filterFile($path . DIRECTORY_SEPARATOR . $uploadedFileName);

					$output['name'] = $uploadedFileName;

					if ($type === 'image')
					{
						$image = new QuantummanagerHelperImage;
						$image->afterUpload($path_source, $path . DIRECTORY_SEPARATOR . $uploadedFileName, ['rotateExif' => 1]);
					}

					Factory::getApplication()->triggerEvent('onQuantumManagerAfterUpload', [
						$path . DIRECTORY_SEPARATOR . $uploadedFileName,
						$path,
						$uploadedFileName,
						$data['scope'],
						$data['path']
					]);
				}

			}


			return json_encode($output);
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}


	/**
	 * @param $path
	 * @param $file
	 *
	 * @return string
	 *
	 * @since version
	 */
	public static function getMetaFile($path, $scope, $file)
	{
		$sourcePath = $path;
		$path       = QuantummanagerHelper::preparePath($path, false, $scope);

		$extended  = (int) QuantummanagerHelper::getParamsComponentValue('metafileextended', 0);
		$showPath  = (int) QuantummanagerHelper::getParamsComponentValue('metafileshowpath', 0);
		$directory = JPATH_ROOT . DIRECTORY_SEPARATOR . $path;
		$filePath  = $directory . DIRECTORY_SEPARATOR . $file;
		$meta      = [];

		if (file_exists($filePath))
		{

			if (is_file($filePath))
			{

				$splitFile = explode('.', $file);
				$exs       = mb_strtolower(array_pop($splitFile));

				$meta = [
					'preview' => [
						'link' => 'index.php?' . http_build_query([
								'option' => 'com_quantummanager',
								'task'   => 'quantumviewfiles.generatePreviewImage',
								'file'   => $file,
								'scope'  => $scope,
								'path'   => $sourcePath,
								'v'      => mt_rand(111111, 999999),
							]),
						'name' => implode('.', $splitFile) . '.' . $exs
					],
					'global'  => [],
					'find'    => [],
				];


				/*$globalInfo[] = [
					'key' => Text::_('COM_QUANTUMMANAGER_METAINFO_FILENAME'),
					'value' => implode('.', $splitFile) . '.' . $exs,
				];*/

				/*$globalInfo[] = [
					'key' => Text::_('COM_QUANTUMMANAGER_METAINFO_EXS'),
					'value' => $exs,
				];*/

				$stat = stat($filePath);

				if ($stat !== false)
				{
					if (isset($stat['mtime']))
					{
						$globalInfo[] = [
							'key'   => Text::_('COM_QUANTUMMANAGER_METAINFO_FILEDATETIME'),
							'value' => date(Text::_('DATE_FORMAT_LC5'), $stat['mtime'])
						];
					}

					if (isset($stat['size']) && (int) $stat['size'] > 0)
					{
						$globalInfo[] = [
							'key'   => Text::_('COM_QUANTUMMANAGER_METAINFO_FILESIZE'),
							'value' => QuantummanagerHelper::formatFileSize((int) $stat['size'])
						];
					}

				}

				if (in_array($exs, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
				{

					list($width, $height, $type, $attr) = @getimagesize($filePath);

					if ($width > 0 && $height > 0)
					{
						$globalInfo[] = [
							'key'   => Text::_('COM_QUANTUMMANAGER_METAINFO_RESOLUTION'),
							'value' => $width . ' x ' . $height
						];
					}

				}


				if (in_array($exs, ['jpg', 'jpeg']))
				{

					try
					{
						if (function_exists('exif_read_data'))
						{
							$tmp = @exif_read_data($filePath);
							if (is_array($tmp))
							{
								foreach ($tmp as $key => $section)
								{
									if (is_array($section))
									{
										foreach ($section as $name => $val)
										{
											$meta['find'][] = [
												'key'   => $key . '.' . $name,
												'value' => $val
											];
										}
									}
									elseif (!in_array(mb_strtolower($key), [
										'filename',
										'filedatetime',
										'filesize',
										'filetype',
										'mimetype',
									]))
									{
										$meta['find'][] = [
											'key'   => $key,
											'value' => $section,
										];
									}
								}
							}
						}
					}
					catch (Exception $e)
					{
						echo $e->getMessage();
					}

				}

				$meta['global'] = array_merge($meta['global'], $globalInfo);

				if ($showPath)
				{
					$meta['global'] = array_merge($meta['global'], [
						[
							'key'   => '',
							'value' => JPATH_SITE . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $file
						]
					]);
				}

			}
			else
			{

				$splitDirectory = explode(DIRECTORY_SEPARATOR, $directory);
				$directoryName  = array_pop($splitDirectory);


				$meta = [
					'preview' => [
						'link' => 'index.php?' . http_build_query([
								'option' => 'com_quantummanager',
								'task'   => 'quantumviewfiles.generatePreviewImage',
								'file'   => $file,
								'scope'  => $scope,
								'path'   => $sourcePath,
								'v'      => mt_rand(111111, 999999),
							]),
						'name' => $showPath ? (DIRECTORY_SEPARATOR . $path) : $directoryName
					],
					'global'  => [],
					'find'    => [],
				];


				if ($extended)
				{
					$size           = static::getSizeDirectory($directory);
					$meta['global'] = [
						[
							'key'   => Text::_('COM_QUANTUMMANAGER_METAINFO_COUNTDORECTORIES'),
							'value' => $size['directoriesCount']
						],
						[
							'key'   => Text::_('COM_QUANTUMMANAGER_METAINFO_COUNTDORECTORIES_CURRENT'),
							'value' => $size['directoriesCountCurrent']
						],
						[
							'key'   => Text::_('COM_QUANTUMMANAGER_METAINFO_COUNTFILES'),
							'value' => $size['filesCount']
						],
						[
							'key'   => Text::_('COM_QUANTUMMANAGER_METAINFO_COUNTFILES_CURRENT'),
							'value' => $size['filesCountCurrent']
						],
						[
							'key'   => Text::_('COM_QUANTUMMANAGER_METAINFO_FILESSIZE'),
							'value' => QuantummanagerHelper::formatFileSize($size['size'])
						],
						[
							'key'   => Text::_('COM_QUANTUMMANAGER_METAINFO_FILESSIZE_CURRENT'),
							'value' => QuantummanagerHelper::formatFileSize($size['sizeCurrent'])
						]
					];
				}
				else
				{
					$size           = static::getSizeDirectory($directory, -1);
					$meta['global'] = [
						[
							'key'   => Text::_('COM_QUANTUMMANAGER_METAINFO_DIRECTORYNAME'),
							'value' => $directoryName
						],
						[
							'key'   => Text::_('COM_QUANTUMMANAGER_METAINFO_COUNTDORECTORIES_CURRENT'),
							'value' => $size['directoriesCount']
						],
						[
							'key'   => Text::_('COM_QUANTUMMANAGER_METAINFO_COUNTFILES_CURRENT'),
							'value' => $size['filesCount']
						],
						[
							'key'   => Text::_('COM_QUANTUMMANAGER_METAINFO_FILESSIZE_CURRENT'),
							'value' => QuantummanagerHelper::formatFileSize($size['size'])
						]
					];
				}

				if ($showPath)
				{
					$meta['global'] = array_merge($meta['global'], [
						[
							'key'   => '',
							'value' => JPATH_SITE . DIRECTORY_SEPARATOR . $path
						]
					]);
				}


			}

		}


		if (defined('JSON_INVALID_UTF8_IGNORE'))
		{
			return json_encode($meta, JSON_INVALID_UTF8_IGNORE);
		}

		return json_encode($meta, 1048576);


	}


	/**
	 * @param $path
	 *
	 * @return string
	 */
	public static function getFiles($path, $scopeName)
	{
		try
		{

			JLoader::register('JInterventionimage', JPATH_LIBRARIES . DIRECTORY_SEPARATOR . 'jinterventionimage' . DIRECTORY_SEPARATOR . 'jinterventionimage.php');
			$path = QuantummanagerHelper::preparePath($path, false, $scopeName);

			$directory = JPATH_ROOT . DIRECTORY_SEPARATOR . $path;

			if (!file_exists($directory))
			{
				return json_encode([
					'error'   => '0',
					'message' => 'folder not create',
				]);
			}


			$filesOutput = [];
			$files       = Folder::files($directory);
			$directories = Folder::folders($directory);
			$manager     = JInterventionimage::getInstance();

			//создаем кеш для файлов
			if (!file_exists(JPATH_ROOT . DIRECTORY_SEPARATOR . 'cache/com_quantummanager'))
			{
				Folder::create(JPATH_ROOT . DIRECTORY_SEPARATOR . 'cache/com_quantummanager');
			}

			foreach ($files as $file)
			{
				$fileParse = explode('.', $file);

				if (count($fileParse) === 1)
				{
					continue;
				}

				$exs      = array_pop($fileParse);
				$fileDate = filemtime($directory . DIRECTORY_SEPARATOR . $file);
				$stat     = stat($directory . DIRECTORY_SEPARATOR . $file);

				if (($stat !== false) && isset($stat['mtime']))
				{
					$fileDate = $stat['mtime'];
				}

				$fileMeta = [
					'size'        => filesize($directory . DIRECTORY_SEPARATOR . $file),
					'is_writable' => (int) is_writable($directory . DIRECTORY_SEPARATOR . $file),
					'name'        => implode('.', $fileParse),
					'exs'         => $exs,
					'file'        => $file,
					'fileP'       => '',
					'dateC'       => $fileDate,
					'dateM'       => $fileDate,
				];

				if (in_array(strtolower($exs), ['jpg', 'png', 'jpeg', 'gif', 'svg', 'webp']))
				{
					$path            = QuantummanagerHelper::preparePath($path, false, $scopeName);
					$cache_file      = static::getPreviewImageFromFile('administrator/cache/com_quantummanager/' . $path . '/' . $file);
					$cache_file_full = Path::clean(JPATH_ROOT . DIRECTORY_SEPARATOR . $cache_file);

					if (file_exists($cache_file_full))
					{
						$fileMeta['fileP'] = Uri::root(false) . $cache_file;
					}
					else
					{
						$fileMeta['fileP'] = 'index.php?option=com_quantummanager&task=quantumviewfiles.generatePreviewImage&scope=' . $scopeName . '&file=' . $file;
					}
				}

				$filesOutput[] = $fileMeta;
			}

			$directoriesOutput = [];
			foreach ($directories as $value)
			{
				$directoriesOutput[] = [
					'name'        => $value,
					'is_writable' => (int) is_writable($directory . DIRECTORY_SEPARATOR . $value),
					'is_empty'    => (int) static::dirIisEmpty($directory . DIRECTORY_SEPARATOR . $value)
				];
			}

			return json_encode([
				'files'       => $filesOutput,
				'directories' => $directoriesOutput
			]);

		}
		catch (Exception $exception)
		{
			echo $exception->getMessage();
		}
	}


	/**
	 * @param $dir
	 *
	 * @return bool
	 *
	 * @since version
	 */
	public static function dirIisEmpty($dir)
	{
		$handle = @opendir($dir);
		while (false !== ($entry = @readdir($handle)))
		{
			if ($entry !== "." && $entry !== "..")
			{
				@closedir($handle);

				return true;
			}
		}
		@closedir($handle);

		return false;
	}


	public static function duplicate($path, $scope, $list = [])
	{
		$actions = QuantummanagerHelper::getActions();

		if (!$actions->get('core.edit'))
		{
			return json_encode(['fail']);
		}

		if ($list === null)
		{
			$list = [];
		}

		$lang         = Factory::getLanguage();
		$path_compile = JPATH_SITE . DIRECTORY_SEPARATOR . QuantummanagerHelper::preparePath($path, false, $scope);

		$find_new_name = static function ($name, $count = 0, $is_file = true) use ($lang, $path_compile, &$find_new_name) {

			if ($is_file)
			{

				$nameSplit = explode('.', $name);
				$nameExs   = mb_strtolower(array_pop($nameSplit));

				$nameSplit = array_merge($nameSplit, [Text::sprintf('COM_QUANTUMMANAGER_QUANTUMVIEWFILES_DUPLICATE', (string) $count)]);

				if (!(int) QuantummanagerHelper::getParamsComponentValue('translit', 0))
				{
					$nameForSafe = preg_replace('#[\-]{2,}#isu', '-', str_replace(' ', '-', implode('_', $nameSplit)));
					$nameForSafe = File::makeSafe($lang->transliterate($nameForSafe), ['#^\.#', '#\040#']);
				}
				else
				{
					$nameForSafe = implode('.', $nameSplit);
				}

				$maxSizeFileName = (int) QuantummanagerHelper::getParamsComponentValue('maxsizefilename', 63);

				if (mb_strlen($nameForSafe) > $maxSizeFileName && $maxSizeFileName > 0)
				{
					$nameSafe = mb_substr($nameForSafe, 0, $maxSizeFileName) . '_p' . mt_rand(11111, 99999);
				}
				else
				{
					$nameSafe = $nameForSafe . ((int) QuantummanagerHelper::getParamsComponentValue('postfix', 0) ? ('_p' . mt_rand(11111, 99999)) : '');
				}

				if ((int) QuantummanagerHelper::getParamsComponentValue('hashname', 0))
				{
					$nameSafe = md5($nameSafe);
				}

				$nameSafe .= '.' . $nameExs;
			}
			else
			{
				$name .= Text::sprintf('COM_QUANTUMMANAGER_QUANTUMVIEWFILES_DUPLICATE', (string) $count);

				if (!(int) QuantummanagerHelper::getParamsComponentValue('translit', 0))
				{
					$nameSafe = File::makeSafe($lang->transliterate($name), ['#^\.#', '#\040#']);
				}
				else
				{
					$nameSafe = $name;
				}
			}

			$check_file = $path_compile . DIRECTORY_SEPARATOR . $nameSafe;

			if (!file_exists($check_file))
			{
				return $nameSafe;
			}

			return $find_new_name($name, ($count + 1), $is_file);
		};


		if (file_exists($path_compile))
		{
			foreach ($list as $file)
			{
				$file_source = $path_compile . DIRECTORY_SEPARATOR . $file;

				if (!file_exists($file_source))
				{
					continue;
				}


				if (is_file($file_source))
				{
					$file_new = $find_new_name($file, 0,true);

					if ($file_source !== $file_new)
					{
						File::copy($file_source, $path_compile . DIRECTORY_SEPARATOR . $file_new);
					}

				}
				else
				{
					$file_new = $find_new_name($file, 0, false);

					if ($file_source !== $file_new)
					{
						Folder::copy($file_source, $path_compile . DIRECTORY_SEPARATOR . $file_new);
					}
				}
			}

			return json_encode(['ok']);
		}

		return json_encode(['fail']);

	}


	/**
	 * @param          $pathFrom
	 * @param          $scopeFrom
	 * @param          $pathTo
	 * @param          $scopeTo
	 * @param   int    $cut
	 * @param   array  $list
	 *
	 * @return false|string
	 * @throws Exception
	 */
	public static function paste($pathFrom, $scopeFrom, $pathTo, $scopeTo, $cut = 0, $list = [])
	{
		$actions = QuantummanagerHelper::getActions();
		if (!$actions->get('core.edit'))
		{
			return json_encode(['fail']);
		}

		if ($list === null)
		{
			$list = [];
		}

		$pathFromCompile = JPATH_SITE . DIRECTORY_SEPARATOR . QuantummanagerHelper::preparePath($pathFrom, false, $scopeFrom);
		$pathToCompile   = JPATH_SITE . DIRECTORY_SEPARATOR . QuantummanagerHelper::preparePath($pathTo, false, $scopeTo);

		if (file_exists($pathFromCompile) && file_exists($pathToCompile))
		{
			foreach ($list as $file)
			{
				if (file_exists($pathFromCompile . DIRECTORY_SEPARATOR . $file) && !file_exists($pathToCompile . DIRECTORY_SEPARATOR . $file))
				{
					if (is_file($pathFromCompile . DIRECTORY_SEPARATOR . $file))
					{
						if ($cut)
						{
							File::move($pathFromCompile . DIRECTORY_SEPARATOR . $file, $pathToCompile . DIRECTORY_SEPARATOR . $file);
						}
						else
						{
							File::copy($pathFromCompile . DIRECTORY_SEPARATOR . $file, $pathToCompile . DIRECTORY_SEPARATOR . $file);
						}
					}
					else
					{
						if ($cut)
						{
							Folder::move($pathFromCompile . DIRECTORY_SEPARATOR . $file, $pathToCompile . DIRECTORY_SEPARATOR . $file);
						}
						else
						{
							$rand            = 'copy_' . mt_rand(111111111, 999999999);
							$cache           = JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'com_quantummanager';
							$cache_copy      = JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'com_quantummanager' . DIRECTORY_SEPARATOR . 'copy';
							$cache_copy_copy = JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'com_quantummanager' . DIRECTORY_SEPARATOR . 'copy' . DIRECTORY_SEPARATOR . $rand;

							if (!file_exists($cache))
							{
								Folder::create($cache);
							}

							if (!file_exists($cache_copy))
							{
								Folder::create($cache_copy);
							}

							if (!file_exists($cache_copy_copy))
							{
								Folder::create($cache_copy_copy);
							}

							Folder::copy($pathFromCompile . DIRECTORY_SEPARATOR . $file, $cache_copy_copy, '', true);
							Folder::copy($cache_copy_copy, $pathToCompile . DIRECTORY_SEPARATOR . $file);
							Folder::delete($cache_copy_copy);

						}
					}

				}
			}

			return json_encode(['ok']);
		}

		return json_encode(['fail']);

	}


	/**
	 * @param   string  $path
	 * @param           $scope
	 * @param   array   $list
	 *
	 * @return false|string
	 *
	 * @throws Exception
	 * @since version
	 */
	public static function delete($scope, $path = '', $list = [])
	{

		$actions = QuantummanagerHelper::getActions();
		if (!$actions->get('core.delete'))
		{
			return json_encode(['fail']);
		}

		if ($list === null)
		{
			$list = [];
		}

		$path = JPATH_SITE . DIRECTORY_SEPARATOR . QuantummanagerHelper::preparePath($path, false, $scope);

		if (file_exists($path))
		{

			foreach ($list as $file)
			{

				if (file_exists($path . DIRECTORY_SEPARATOR . $file))
				{

					if (is_file($path . DIRECTORY_SEPARATOR . $file))
					{
						File::delete($path . DIRECTORY_SEPARATOR . $file);
					}
					else
					{
						Folder::delete($path . DIRECTORY_SEPARATOR . $file);
					}
				}

			}

			return json_encode(['ok']);
		}

		return json_encode(['fail']);
	}


	/**
	 * @return string
	 * @throws Exception
	 */
	public static function converterSave()
	{

		$actions = QuantummanagerHelper::getActions();
		if (!$actions->get('core.edit'))
		{
			return json_encode(['fail']);
		}

		$output = [];
		$app    = Factory::getApplication();
		$data   = $app->input->getArray();
		$files  = $app->input->files->getArray();
		foreach ($files as $file)
		{

			if ($file['error'] == 4)
			{
				continue;
			}

			if ($file['error'])
			{

				switch ($file['error'])
				{
					case 1:
						$output['error'] = Text::_('COM_QUANTUMMANAGER_ERROR_FILE_TO_LARGE_THAN_PHP_INI_ALLOWS');
						break;

					case 2:
						$output['error'] = Text::_('COM_QUANTUMMANAGER_ERROR_FILE_TO_LARGE_THAN_HTML_FORM_ALLOWS');
						break;

					case 3:
						$output['error'] = Text::_('COM_QUANTUMMANAGER_ERROR_PARTIAL_UPLOAD');
				}

			}
			else
			{
				$lang             = Factory::getLanguage();
				$exsSplit         = explode('.', $data['exs']);
				$nameSplit        = $data['name'];
				$nameExs          = array_pop($exsSplit);
				$nameSafe         = File::makeSafe($lang->transliterate($nameSplit), ['#^\.#', '#\040#']);
				$uploadedFileName = $nameSafe . '.' . $nameExs;
				$exs              = explode(',', 'jpg,jpeg,png,gif,webp');

				if (in_array($nameExs, QuantummanagerHelper::$forbiddenExtensions))
				{
					$output['error'] = Text::_('COM_QUANTUMMANAGER_ERROR_PARTIAL_UPLOAD');

					return $output['error'];
				}

				$type         = preg_replace("/\/.*?$/isu", '', $file['type']);
				$data['name'] = isset($data['name']) ? $data['name'] : '';
				$path_source  = QuantummanagerHelper::preparePath($data['path'], $data['scope']);
				$path         = JPATH_ROOT . DIRECTORY_SEPARATOR . QuantummanagerHelper::preparePath($data['path'], false, $data['scope']);

				if (!QuantummanagerHelper::checkFile($nameSplit . '.' . $nameExs, $file['type']))
				{
					$output['error'] = Text::_('COM_QUANTUMMANAGER_ERROR_UPLOAD_ACCESS') . ': ' . (empty($file['type']) ? Text::_('COM_QUANTUMMANAGER_EMPTY_MIMETYPE') : $file['type']);

					return json_encode($output);
				}

				if (!file_exists($path))
				{
					Folder::create($path);
				}

				$image = new QuantummanagerHelperImage;

				if (isset($data['source']) && !empty($data['source']))
				{
					$data['source'] = urldecode($data['source']);
					if (file_exists(JPATH_ROOT . DIRECTORY_SEPARATOR . $data['source']))
					{
						$image->saveExif(JPATH_ROOT . DIRECTORY_SEPARATOR . $data['source']);
					}
				}

				if (File::upload($file['tmp_name'], $path . DIRECTORY_SEPARATOR . $uploadedFileName))
				{
					QuantummanagerHelper::filterFile($path . DIRECTORY_SEPARATOR . $uploadedFileName);
					$output['name'] = $uploadedFileName;
					$originalresize = (int) QuantummanagerHelper::getParamsComponentValue('originalresize', 0);

					if ($originalresize)
					{
						if (file_exists($path . DIRECTORY_SEPARATOR . '_original' . DIRECTORY_SEPARATOR . $uploadedFileName))
						{
							File::delete($path . DIRECTORY_SEPARATOR . '_original' . DIRECTORY_SEPARATOR . $uploadedFileName);
							File::copy(
								$path . DIRECTORY_SEPARATOR . $uploadedFileName,
								$path . DIRECTORY_SEPARATOR . '_original' . DIRECTORY_SEPARATOR . $uploadedFileName
							);
						}
					}

					$image->afterUpload($path_source, $path . DIRECTORY_SEPARATOR . $uploadedFileName);

				}

			}
		}

		return json_encode($output);
	}


	/**
	 * @param $path_source
	 * @param $scope
	 * @param $file
	 * @param $id
	 *
	 * @return false|string
	 * @throws Exception
	 */
	public static function downloadFileUnsplash($path_source, $scope, $file, $id)
	{

		$output = [];
		if (preg_match('#^https://images.unsplash.com/.*?#', $file))
		{

			@ini_set('memory_limit', '256M');

			$lang        = Factory::getLanguage();
			$path_source = QuantummanagerHelper::preparePathRoot($path_source, $scope);
			$path        = QuantummanagerHelper::preparePath($path_source, false, $scope);

			$fileContent = file_get_contents($file);
			$filePath    = JPATH_ROOT . DIRECTORY_SEPARATOR . $path;
			$id          = File::makeSafe($lang->transliterate($id), ['#^\.#', '#\040#']);
			$fileName    = $id . '.jpg';
			file_put_contents($filePath . DIRECTORY_SEPARATOR . $fileName, $fileContent);

			$image = new QuantummanagerHelperImage;
			$image->afterUpload($path_source, $filePath . DIRECTORY_SEPARATOR . $fileName);

			$output['name'] = $fileName;

		}

		return json_encode($output);

	}


	/**
	 * @param $path
	 * @param $file
	 * @param $id
	 *
	 * @return false|string
	 *
	 * @throws Exception
	 * @since version
	 */
	public static function downloadFilePixabay($path_source, $scope, $file, $id)
	{

		$output = [];
		if (preg_match('#^https://pixabay.com/.*?#', $file))
		{

			@ini_set('memory_limit', '256M');

			$lang        = Factory::getLanguage();
			$path_source = QuantummanagerHelper::preparePathRoot($path_source, $scope);
			$path        = QuantummanagerHelper::preparePath($path_source, false, $scope);
			$fileSplit   = explode('.', $file);
			$exs         = array_pop($fileSplit);
			$fileContent = file_get_contents($file);
			$filePath    = JPATH_ROOT . DIRECTORY_SEPARATOR . $path;
			$id          = File::makeSafe($lang->transliterate($id), ['#^\.#', '#\040#']);
			$fileName    = $id . '.' . $exs;
			file_put_contents($filePath . DIRECTORY_SEPARATOR . $fileName, $fileContent);

			$image = new QuantummanagerHelperImage;
			$image->afterUpload($path_source, $filePath . DIRECTORY_SEPARATOR . $fileName);

			$output['name'] = $fileName;

		}

		return json_encode($output);

	}


	/**
	 * @param $path
	 * @param $file
	 * @param $id
	 *
	 * @return false|string
	 *
	 * @throws Exception
	 * @since version
	 */
	public static function downloadFilePexels($path_source, $scope, $file, $id)
	{

		$output = [];
		if (preg_match('#^https://images.pexels.com/.*?#', $file))
		{

			@ini_set('memory_limit', '256M');

			$lang        = Factory::getLanguage();
			$path_source = QuantummanagerHelper::preparePathRoot($path_source, $scope);
			$path        = QuantummanagerHelper::preparePath($path_source, false, $scope);
			$fileClean   = preg_replace("#\?.*?$#isu", '', $file);
			$fileSplit   = explode('.', $fileClean);
			$exs         = array_pop($fileSplit);

			if ($exs === 'jpeg')
			{
				$exs = 'jpg';
			}

			$fileContent = file_get_contents($file);
			$filePath    = JPATH_ROOT . DIRECTORY_SEPARATOR . $path;
			$id          = File::makeSafe($lang->transliterate($id), ['#^\.#', '#\040#']);
			$fileName    = $id . '.' . $exs;

			file_put_contents($filePath . DIRECTORY_SEPARATOR . $fileName, $fileContent);

			$image = new QuantummanagerHelperImage;
			$image->afterUpload($path_source, $filePath . DIRECTORY_SEPARATOR . $fileName);

			$output['name'] = $fileName;

		}

		return json_encode($output);

	}


	public static function getPreviewImageFromFile($file)
	{
		$splitFile = explode('.', $file);
		$exs       = mb_strtolower(array_pop($splitFile));

		if ($exs === 'webp')
		{
			return implode('.', $splitFile) . '.jpg';
		}

		return $file;
	}

	/**
	 * @param $path
	 * @param $file
	 *
	 *
	 * @throws Exception
	 * @since version
	 */
	public static function generatePreviewImage($path, $scope, $file)
	{
		$app            = Factory::getApplication();
		$splitFile      = explode('.', $file);
		$exs            = mb_strtolower(array_pop($splitFile));
		$mediaIconsPath = 'media/com_quantummanager/images/icons/';
		$siteUrl        = Uri::root();
		$path           = QuantummanagerHelper::preparePath($path, false, $scope);

		if (empty($file))
		{
			$prefix = QuantummanagerHelper::isJoomla4() ? 'j4-' : '';

			if (static::dirIisEmpty(JPATH_ROOT . DIRECTORY_SEPARATOR . $path))
			{
				$app->redirect($siteUrl . $mediaIconsPath . $prefix . 'folder.svg');
			}
			else
			{
				$app->redirect($siteUrl . $mediaIconsPath . $prefix . 'folder-empty.svg');
			}
		}


		if (in_array($exs, ['jpg', 'jpeg', 'png', 'gif']))
		{

			JLoader::register('JInterventionimage', JPATH_LIBRARIES . DIRECTORY_SEPARATOR . 'jinterventionimage' . DIRECTORY_SEPARATOR . 'jinterventionimage.php');
			$directory   = JPATH_ROOT . DIRECTORY_SEPARATOR . $path;
			$manager     = JInterventionimage::getInstance();
			$cacheSource = JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator/cache/com_quantummanager';
			$cache       = $cacheSource;
			$pathArr     = explode('/', $path);

			foreach ($pathArr as $iValue)
			{
				$cache .= DIRECTORY_SEPARATOR . $iValue;
				if (!file_exists($cache))
				{
					Folder::create($cache);
				}
			}


			if (!file_exists($cache . DIRECTORY_SEPARATOR . $file))
			{
				$fileSize = filesize($directory . DIRECTORY_SEPARATOR . $file);
				if ($fileSize !== 0 && ($fileSize < ((QuantummanagerHelper::getMemoryLimit() * 1024 * 1024) / 2)))
				{
					$manager->make($directory . DIRECTORY_SEPARATOR . $file)->resize(null, 320, static function ($constraint) {
						$constraint->aspectRatio();
					})->save($cache . DIRECTORY_SEPARATOR . $file);

					$app->redirect($siteUrl . 'administrator/cache/com_quantummanager' . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $file . '?v=' . mt_rand(111111, 999999));
				}

			}
			else
			{
				$app->redirect($siteUrl . 'administrator/cache/com_quantummanager' . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $file . '?v=' . mt_rand(111111, 999999));
			}

		}

		if ($exs === 'webp')
		{

			JLoader::register('JInterventionimage', JPATH_LIBRARIES . DIRECTORY_SEPARATOR . 'jinterventionimage' . DIRECTORY_SEPARATOR . 'jinterventionimage.php');
			$directory   = JPATH_ROOT . DIRECTORY_SEPARATOR . $path;
			$manager     = JInterventionimage::getInstance();
			$cacheSource = JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator/cache/com_quantummanager';
			$cache       = $cacheSource;
			$pathArr     = explode('/', $path);
			$newFile     = implode('.', $splitFile) . '.jpg';

			foreach ($pathArr as $iValue)
			{
				$cache .= DIRECTORY_SEPARATOR . $iValue;
				if (!file_exists($cache))
				{
					Folder::create($cache);
				}
			}

			if (!file_exists($cache . DIRECTORY_SEPARATOR . $newFile))
			{
				$manager->make($directory . DIRECTORY_SEPARATOR . $file)->resize(null, 320, static function ($constraint) {
					$constraint->aspectRatio();
				})->encode('jpg')->save($cache . DIRECTORY_SEPARATOR . $newFile);
			}

			$app->redirect($siteUrl . 'administrator/cache/com_quantummanager' . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $newFile . '?v=' . mt_rand(111111, 999999));
		}

		if ($exs === 'svg')
		{
			$path = QuantummanagerHelper::preparePath($path, false, $scope);
			$app->redirect($siteUrl . $path . DIRECTORY_SEPARATOR . $file . '?=' . mt_rand(111111, 999999));
		}

		$mapFileColors = include implode(DIRECTORY_SEPARATOR, [JPATH_ROOT, 'administrator', 'components', 'com_quantummanager', 'layouts', 'mapfilescolors.php']);
		$colors        = $mapFileColors['default'];

		if (isset($mapFileColors[$exs]))
		{
			$colors = $mapFileColors[$exs];
		}

		$svg = '<?xml version="1.0" encoding="iso-8859-1"?>' . file_get_contents(JPATH_ROOT . DIRECTORY_SEPARATOR . '/media/com_quantummanager/images/icons/file.svg');
		$svg = str_replace(array('data-fill-m=""', 'data-fill-t=""'), array('fill="' . $colors[0] . '"', 'fill="' . $colors[1] . '"'), $svg);
		$svg = str_replace('</g>', "<text x=\"150\" y=\"200\" fill=\"#FFFFFF\" style=\"font-size: 80px;text-anchor: middle;font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;\">" . $exs . "</text></g>", $svg);

		header('Content-type: image/svg+xml');
		echo $svg;
		$app->close();
	}

	/**
	 * @param $path
	 * @param $scope
	 * @param $list
	 * @param $previewTitle
	 *
	 * @return false|string
	 * @throws Exception
	 */
	public static function createPreview($path, $scope, $list, $previewTitle)
	{

		$path   = QuantummanagerHelper::preparePath($path, false, $scope);
		$image  = new QuantummanagerHelperImage;
		$output = [];

		foreach ($list as $file)
		{
			$pathFileFrom = JPATH_ROOT . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $file;

			$info = pathinfo($pathFileFrom);

			if (isset($info['extension']) && (!in_array(mb_strtolower($info['extension']), ['jpg', 'jpeg', 'png', 'webp'])))
			{
				continue;
			}

			//получаем превью
			$previewlist   = QuantummanagerHelper::getParamsComponentValue('previewslist', []);
			$previewSelect = [];
			foreach ($previewlist as $preview)
			{
				if ($preview->label === $previewTitle)
				{
					$previewSelect = (array) $preview;
				}
			}

			if (count($previewSelect) === 0 || (int) $previewSelect['width'] === 0 || (int) $previewSelect['height'] === 0)
			{
				return json_encode([]);
			}

			$splitName = explode('.', $file);
			$exs       = array_pop($splitName);
			$fileName  = '';

			//создаем папку, если нет название файла другой
			if ((int) QuantummanagerHelper::getParamsComponentValue('previewsfolder', 1))
			{
				$pathFileTo = JPATH_ROOT . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . '_thumb';
				Folder::create($pathFileTo);
				$fileName   = implode('.', $splitName) . '_' . (int) $previewSelect['width'] . '_' . (int) $previewSelect['height'] . '.' . $exs;
				$pathFileTo .= DIRECTORY_SEPARATOR . $fileName;
			}
			else
			{
				$pathFileTo = JPATH_ROOT . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR;
				$fileName   = 'thumb_' . implode('.', $splitName) . '_' . (int) $previewSelect['width'] . '_' . (int) $previewSelect['height'] . '.' . $exs;
				$pathFileTo .= $fileName;
			}

			$output[] = ['name' => $fileName];

			//копируем файл
			if (File::copy($pathFileFrom, $pathFileTo))
			{
				if ($previewSelect['algorithm'] === 'fit')
				{
					$image->saveExif($pathFileTo);
					$image->fit($pathFileTo, (int) $previewSelect['width'], (int) $previewSelect['height']);
					$image->writeExif($pathFileTo);
				}

				if ($previewSelect['algorithm'] === 'resize')
				{
					$image->saveExif($pathFileTo);
					$image->fit($pathFileTo, (int) $previewSelect['width'], (int) $previewSelect['height']);
					$image->writeExif($pathFileTo);
				}
			}

		}

		return json_encode($output);

	}

	/**
	 * @param           $path
	 * @param           $file
	 * @param   string  $name
	 *
	 * @return string
	 *
	 * @throws Exception
	 * @since version
	 */
	public static function renameFile($path, $scope, $file, $name = '')
	{
		$path      = QuantummanagerHelper::preparePath($path, false, $scope);
		$app       = Factory::getApplication();
		$splitFile = explode('.', $file);
		$exs       = mb_strtolower(array_pop($splitFile));
		$output    = [
			'status' => 'fail'
		];

		$lang = Factory::getLanguage();

		if (!(int) QuantummanagerHelper::getParamsComponentValue('translit', 0))
		{
			$nameSafe = File::makeSafe($lang->transliterate($name), ['#^\.#', '#\040#']);
		}
		else
		{
			$nameSafe = $name;
		}

		if (!in_array($exs, QuantummanagerHelper::$forbiddenExtensions) && file_exists(JPATH_ROOT . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $file))
		{
			if (rename(JPATH_ROOT . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $file, JPATH_ROOT . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $nameSafe . '.' . $exs))
			{
				$output = [
					'status' => 'ok'
				];
			}
		}
		else
		{
			$output = [
				'status' => 'fail'
			];
		}

		return json_encode($output);
	}


	/**
	 * @param           $path
	 * @param           $file
	 * @param   string  $name
	 *
	 * @return string
	 *
	 * @throws Exception
	 * @since version
	 */
	public static function renameDirectory($path, $scope, $oldName, $name = '')
	{
		$path   = QuantummanagerHelper::preparePath($path, false, $scope);
		$app    = Factory::getApplication();
		$output = [
			'status' => 'fail'
		];

		$lang = Factory::getLanguage();

		if (!(int) QuantummanagerHelper::getParamsComponentValue('translit', 0))
		{
			$nameSafe = File::makeSafe($lang->transliterate($name), ['#^\.#', '#\040#']);
		}
		else
		{
			$nameSafe = $name;
		}

		if (rename(JPATH_ROOT . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $oldName, JPATH_ROOT . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $nameSafe))
		{
			$output = [
				'status' => 'ok'
			];
		}

		return json_encode($output);
	}


	/**
	 * @param $path
	 * @param $file
	 *
	 * @return false|string
	 *
	 * @throws Exception
	 * @since version
	 */
	public static function getImageForCrop($path, $scope, $file)
	{
		$path           = QuantummanagerHelper::preparePath($path, false, $scope);
		$originalresize = (int) QuantummanagerHelper::getParamsComponentValue('originalresize', 0);
		$output         = [];

		if ($originalresize)
		{
			if (file_exists(JPATH_ROOT . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . '_original' . DIRECTORY_SEPARATOR . $file))
			{
				$output['path'] = $path . '/_original/' . $file;
			}
			else
			{
				$output['path'] = $path . '/' . $file;
			}
		}
		else
		{
			$output['path'] = $path . '/' . $file;
		}

		return json_encode($output);
	}


	/**
	 * @param $path
	 * @param $scope
	 * @param $list
	 *
	 *
	 * @throws Exception
	 * @since version
	 */
	public static function setWatermark($path, $scope, $list)
	{

		$path  = QuantummanagerHelper::preparePath($path, false, $scope);
		$image = new QuantummanagerHelperImage;

		foreach ($list as $file)
		{
			$pathFile = JPATH_ROOT . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $file;

			$info = pathinfo($pathFile);

			if (isset($info['extension']) && (!in_array(mb_strtolower($info['extension']), ['jpg', 'jpeg', 'png', 'webp'])))
			{
				continue;
			}

			$image->resizeWatermark($pathFile);
			$image->reloadCache($pathFile);
		}

	}


}