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
use Joomla\CMS\Uri\Uri;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;

class QuantummanagerFileSystemLocal
{

	/**
	 * @param $path
	 * @param $name
	 * @return string
	 */
	public static function createDirectory($path, $name)
	{
		JLoader::register('QuantummanagerHelper', JPATH_SITE . '/administrator/components/com_quantummanager/helpers/quantummanager.php');

		$actions = QuantummanagerHelper::getActions();
		if (!$actions->get('core.create'))
		{
			return json_encode(['fail']);
		}

		$path = JPATH_SITE . DIRECTORY_SEPARATOR . QuantummanagerHelper::preparePath($path);
		$lang = Factory::getLanguage();

		if(file_exists($path))
		{
			Folder::create($path . DIRECTORY_SEPARATOR . File::makeSafe($lang->transliterate($name), ['#^\.#', '#\040#']));
			return json_encode(['ok']);
		} else {
			return json_encode(['fail']);
		}

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
		JLoader::register('QuantummanagerHelper', JPATH_SITE . '/administrator/components/com_quantummanager/helpers/quantummanager.php');
		$path = JPATH_ROOT . DIRECTORY_SEPARATOR . QuantummanagerHelper::preparePath($path);
		$directories = [];
		$directories = self::showdir($path, $root,true, true);

		return json_encode([
			'directories' => $directories
		]);
	}


	/**
	 * @param $dir
	 * @param bool $folderOnly
	 * @param bool $showRoot
	 * @param int $level
	 * @param string $ef
	 * @return array|string
	 */
	protected static function showdir
	(
		$dir,
		$root = '',
		$folderOnly = false,
		$showRoot = false,
		$level = 0,  // do not use!!!
		$ef = ''     // do not use!!!
	)
	{

		$html = '';
		if ((int)$level == 0)
		{
			$dir = realpath($dir);
			$ef = ($showRoot ? realpath($dir . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR : $dir . DIRECTORY_SEPARATOR);
		}

		if (!file_exists($dir)) {
			return '';
		}

		if ($showRoot && (int)$level == 0)
		{
			JLoader::register('QuantummanagerHelper', JPATH_SITE . '/administrator/components/com_quantummanager/helpers/quantummanager.php');
			$subdir = self::showdir($dir, $root, $folderOnly, $showRoot, $level + 1, $ef);
			return [
				//'path' => QuantummanagerHelper::getFolderRoot(),
				'path' => $root,
				'subpath' => $subdir
			];
		}
		else
		{
			$list = scandir($dir);
			if (is_array($list))
			{
				$list = array_diff($list, ['.', '..']);
				if ($list)
				{
					$folders = [];

					foreach ($list as $name)
					{
						if (is_dir($dir . DIRECTORY_SEPARATOR . $name)) {

							if($name === 'com_quantummanager') {
								continue;
							}

							$folders[] = [
								'path' => $name,
								'subpath' => self::showdir($dir . DIRECTORY_SEPARATOR . $name, $root, $folderOnly, $showRoot, $level + 1, $ef)
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
		}

		return [];

	}


	/**
	 * @throws Exception
	 */
	public static function upload()
	{
		try {
			JLoader::register('QuantummanagerHelper', JPATH_SITE . '/administrator/components/com_quantummanager/helpers/quantummanager.php');

			$actions = QuantummanagerHelper::getActions();
			if (!$actions->get('core.create')) {
				return json_encode(['fail']);
			}

			$output = [];
			$app = Factory::getApplication();
			$data = $app->input->getArray();
			$files = $app->input->files->getArray();
			foreach ($files as $file) {

				if ($file['error'] == 4) {
					continue;
				}

				if ($file['error']) {

					switch ($file['error']) {
						case 1:
							$output["error"] = Text::_('COM_QUANTUMMANAGER_FILE_TO_LARGE_THAN_PHP_INI_ALLOWS');
							break;

						case 2:
							$output["error"] = Text::_('COM_QUANTUMMANAGER_FILE_TO_LARGE_THAN_HTML_FORM_ALLOWS');
							break;

						case 3:
							$output["error"] = Text::_('COM_QUANTUMMANAGER_ERROR_PARTIAL_UPLOAD');
					}

				} else {
					$componentParams = ComponentHelper::getParams('com_quantummanager');
					$lang = Factory::getLanguage();
					$nameSplit = explode('.', $file['name']);
					$nameExs = mb_strtolower(array_pop($nameSplit));
					$nameSafe = File::makeSafe($lang->transliterate(implode('-', $nameSplit)), ['#^\.#', '#\040#']) . ((int)$componentParams->get('postfix', 0) ? ('_p' . rand(11111, 99999)) : '');
					$uploadedFileName = $nameSafe . '.' . $nameExs;
					$exs = explode(',', 'jpg,jpeg,png,gif');
					$type = preg_replace("/\/.*?$/isu", '', $file['type']);
					$data['name'] = isset($data['name']) ? $data['name'] : '';
					$path = JPATH_ROOT . DIRECTORY_SEPARATOR . QuantummanagerHelper::preparePath($data['path']);

					if (!QuantummanagerHelper::checkFile($file['name'], $file['type'])) {
						$output["error"] = Text::_('COM_QUANTUMMANAGER_ERROR_UPLOAD_ACCESS') . ': ' . (empty($file['type']) ? Text::_('COM_QUANTUMMANAGER_EMPTY_MIMETYPE') : $file['type']);
						return json_encode($output);
					}

					if (!file_exists($path)) {
						Folder::create($path);
					}

					if (File::upload($file['tmp_name'], $path . DIRECTORY_SEPARATOR . $uploadedFileName)) {

						QuantummanagerHelper::filterFile($path . DIRECTORY_SEPARATOR . $uploadedFileName);

						$output["name"] = $uploadedFileName;

						if ($type === "image") {
							JLoader::register('QuantummanagerHelperImage', JPATH_ROOT . '/administrator/components/com_quantummanager/helpers/image.php');
							$image = new QuantummanagerHelperImage;
							$image->afterUpload($path . DIRECTORY_SEPARATOR . $uploadedFileName);
						}

					}

				}
			}

			return json_encode($output);
		}
		catch (Exception $e) {
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
	public static function getMetaFile($path, $file)
	{
		$sourcePath = $path;
		$path = QuantummanagerHelper::preparePath($path);
		$directory = JPATH_ROOT . DIRECTORY_SEPARATOR . $path;
		$filePath = $directory . DIRECTORY_SEPARATOR . $file;
		$meta = [];

		if(file_exists($filePath))
		{

			if(is_file($filePath))
			{
				$meta = [
					'preview' => [
						'link' => 'index.php?' . http_build_query([
								'option' => 'com_quantummanager',
								'task' => 'quantumviewfiles.generatePreviewImage',
								'file' => $file,
								'path' => $sourcePath,
								'v' => rand(111111, 999999),
							])
					],
					'global' => [],
					'find' => [],
				];


				$splitFile = explode('.', $file);
				$exs = mb_strtolower(array_pop($splitFile));

				$globalInfo[] = [
					'key' => Text::_('COM_QUANTUMMANAGER_FILE_METAINFO_FILENAME'),
					'value' => implode('.', $splitFile),
				];

				$globalInfo[] = [
					'key' => Text::_('COM_QUANTUMMANAGER_FILE_METAINFO_EXS'),
					'value' => $exs,
				];

				$stat = stat($filePath);

				if ($stat !== false) {
					if (isset($stat['mtime']))
					{
						$globalInfo[] = [
							'key' => Text::_('COM_QUANTUMMANAGER_FILE_METAINFO_FILEDATETIME'),
							'value' => date(Text::_('DATE_FORMAT_LC5'), $stat['mtime'])
						];
					}

					if (isset($stat['size']))
					{
						$globalInfo[] = [
							'key' => Text::_('COM_QUANTUMMANAGER_FILE_METAINFO_FILESIZE'),
							'value' => QuantummanagerHelper::formatFileSize((int)$stat['size'])
						];
					}

				}

				if (in_array($exs, ['jpg', 'jpeg', 'png', 'gif']))
				{
					list($width, $height, $type, $attr) = getimagesize($filePath);

					$globalInfo[] = [
						'key' => Text::_('COM_QUANTUMMANAGER_FILE_METAINFO_RESOLUTION'),
						'value' => $width . ' x ' . $height
					];
				}



				if (in_array($exs, ['jpg', 'jpeg']))
				{

					try
					{
						$tmp = exif_read_data($filePath);
						foreach ($tmp as $key => $section)
						{
							if (is_array($section)) {
								foreach ($section as $name => $val)
								{
									$meta['find'][] = [
										'key' => $key . '.' . $name,
										'value' => $val
									];
								}
							}
							else
								{

								if (!in_array(mb_strtolower($key), [
									'filename',
									'filedatetime',
									'filesize',
									'filetype',
									'mimetype',
								]))
								{
									$meta['find'][] = [
										'key' => $key,
										'value' => $section,
									];
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

			}
			else
			{

				$meta = [
					'preview' => [
						'link' => 'index.php?' . http_build_query([
								'option' => 'com_quantummanager',
								'task' => 'quantumviewfiles.generatePreviewImage',
								'file' => $file,
								'path' => $sourcePath,
								'v' => rand(111111, 999999),
							])
					],
					'global' => [],
					'find' => [],
				];

				$splitDirectory = explode(DIRECTORY_SEPARATOR, $directory);
				$directoryName = array_pop($splitDirectory);
				$files = Folder::files($directory, '');
				$directories = Folder::folders($directory);
				$size = 0;

				foreach($files as $file)
				{
					$size += filesize($directory . DIRECTORY_SEPARATOR . $file);
				}

				$meta['global'] = [
					[
						'key' => Text::_('COM_QUANTUMMANAGER_FILE_METAINFO_DIRECTORYNAME'),
						'value' => $directoryName
					],
					[
						'key' => Text::_('COM_QUANTUMMANAGER_FILE_METAINFO_COUNTDORECTORIES'),
						'value' => count($directories)
					],
					[
						'key' => Text::_('COM_QUANTUMMANAGER_FILE_METAINFO_COUNTFILES'),
						'value' => count($files)
					],
					[
						'key' => Text::_('COM_QUANTUMMANAGER_FILE_METAINFO_FILESSIZE'),
						'value' => QuantummanagerHelper::formatFileSize($size)
					]
				];


			}

		}


		if (defined('JSON_INVALID_UTF8_IGNORE'))
		{
			return json_encode($meta, JSON_INVALID_UTF8_IGNORE);
		}
		else
		{
			return json_encode($meta, 1048576);
		}


	}

	/**
	 * @param $path
	 * @return string
	 */
	public static function getFiles($path)
	{
		try {

			JLoader::register('JInterventionimage', JPATH_LIBRARIES . DIRECTORY_SEPARATOR . 'jinterventionimage' . DIRECTORY_SEPARATOR . 'jinterventionimage.php');
			$path = QuantummanagerHelper::preparePath($path);
			$directory = JPATH_ROOT . DIRECTORY_SEPARATOR . $path;

			if(!file_exists($directory))
			{
				return json_encode([
					'error' => '0',
					'message' => 'folder not create',
				]);
			}


			$filesOutput = [];
			$files = Folder::files($directory);
			$directories = Folder::folders($directory);
			$manager = JInterventionimage::getInstance();

			//создаем кеш для файлов
			if(!file_exists(JPATH_ROOT . DIRECTORY_SEPARATOR . 'images/com_quantummanager'))
			{
				Folder::create(JPATH_ROOT . DIRECTORY_SEPARATOR . 'images/com_quantummanager');
			}

			if(!file_exists(JPATH_ROOT . DIRECTORY_SEPARATOR . 'images/com_quantummanager/cache'))
			{
				Folder::create(JPATH_ROOT . DIRECTORY_SEPARATOR . 'images/com_quantummanager/cache');
			}

			foreach ($files as $file)
			{
				$fileParse = explode('.', $file);

				if (count($fileParse) === 1)
				{
					continue;
				}

				$exs = array_pop($fileParse);
				$fileDate = filemtime($directory . DIRECTORY_SEPARATOR . $file);

				$stat = stat($directory . DIRECTORY_SEPARATOR . $file);

				if ($stat !== false)
				{
					if (isset($stat['mtime']))
					{
						$fileDate = $stat['mtime'];
					}
				}

				$fileMeta = [
					'size' => filesize($directory . DIRECTORY_SEPARATOR . $file),
					'name' => implode('.', $fileParse),
					'exs' => $exs,
					'file' => $file,
					'fileP' => $file,
					'dateC' => $fileDate,
					'dateM' => $fileDate,
				];

				if(in_array(strtolower($exs), ['jpg', 'png', 'jpeg', 'gif', 'svg']))
				{
					$cacheSource =  JPATH_ROOT . DIRECTORY_SEPARATOR . 'images/com_quantummanager/cache';
					$path = QuantummanagerHelper::preparePath($path);
					$cache = $cacheSource . DIRECTORY_SEPARATOR . $path;
					//if (!file_exists($cache . DIRECTORY_SEPARATOR . $file))
					//{
						$fileMeta['fileP'] = 'index.php?option=com_quantummanager&task=quantumviewfiles.generatePreviewImage&file=' . $file;
					//}

				}

				$filesOutput[] = $fileMeta;
			}

			$directoriesOutput = [];
			foreach ($directories as $value)
			{
				if($value !== 'com_quantummanager')
				{
					$directoriesOutput[] = $value;
				}
			}

			return json_encode([
				'files' => $filesOutput,
				'directories' => $directoriesOutput
			]);

		}
		catch (Exception $exception) {
			echo $exception->getMessage();
		}
	}


	/**
	 * @param string $path
	 * @param array $list
	 * @return string
	 */
	public static function delete($path = '', $list = [])
	{
		JLoader::register('QuantummanagerHelper', JPATH_SITE . '/administrator/components/com_quantummanager/helpers/quantummanager.php');

		$actions = QuantummanagerHelper::getActions();
		if (!$actions->get('core.delete'))
		{
			return json_encode(['fail']);
		}

		if($list === null)
		{
			$list = [];
		}

		$path = JPATH_SITE . DIRECTORY_SEPARATOR . QuantummanagerHelper::preparePath($path);

		if(file_exists($path))
		{

			foreach ($list as $file)
			{

				if(file_exists($path . DIRECTORY_SEPARATOR . $file))
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
		} else {
			return json_encode(['fail']);
		}
	}


	/**
	 * @return string
	 * @throws Exception
	 */
	public static function converterSave()
	{
		JLoader::register('QuantummanagerHelper', JPATH_SITE . '/administrator/components/com_quantummanager/helpers/quantummanager.php');

		$actions = QuantummanagerHelper::getActions();
		if (!$actions->get('core.edit'))
		{
			return json_encode(['fail']);
		}

		$output = [];
		$app = Factory::getApplication();
		$data = $app->input->getArray();
		$files = $app->input->files->getArray();
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
						$output["error"] = Text::_('COM_QUANTUMMANAGER_FILE_TO_LARGE_THAN_PHP_INI_ALLOWS');
						break;

					case 2:
						$output["error"] = Text::_('COM_QUANTUMMANAGER_FILE_TO_LARGE_THAN_HTML_FORM_ALLOWS');
						break;

					case 3:
						$output["error"] = Text::_('COM_QUANTUMMANAGER_ERROR_PARTIAL_UPLOAD');
				}

			}
			else
			{
				$lang = Factory::getLanguage();
				$nameSplit = $data['name'];
				$nameExs = $data['exs'];
				$nameSafe = File::makeSafe($lang->transliterate($nameSplit), ['#^\.#', '#\040#']);
				$uploadedFileName = $nameSafe . '.' . $nameExs;
				$exs = explode(',', 'jpg,jpeg,png,gif');
				$type = preg_replace("/\/.*?$/isu", '', $file['type']);
				$data['name'] = isset($data['name']) ? $data['name'] : '';
				$path = JPATH_ROOT . DIRECTORY_SEPARATOR . QuantummanagerHelper::preparePath($data['path']);

				if(!QuantummanagerHelper::checkFile($nameSplit . '.' . $nameExs, $file['type']))
				{
					$output["error"] = Text::_('COM_QUANTUMMANAGER_ERROR_UPLOAD_ACCESS') . ': ' . (empty($file['type']) ? Text::_('COM_QUANTUMMANAGER_EMPTY_MIMETYPE') : $file['type']);
					return json_encode($output);
				}

				if (!file_exists($path))
				{
					Folder::create($path);
				}

				if (File::upload($file['tmp_name'], $path . DIRECTORY_SEPARATOR . $uploadedFileName))
				{
					QuantummanagerHelper::filterFile($path . DIRECTORY_SEPARATOR . $uploadedFileName);

					$output["name"] = $uploadedFileName;

					if($type === "image") {
						JLoader::register('QuantummanagerHelperImage', JPATH_ROOT . '/administrator/components/com_quantummanager/helpers/image.php');
						$image = new QuantummanagerHelperImage;
						$image->afterUpload($path . DIRECTORY_SEPARATOR . $uploadedFileName, ['resize' => 0]);
					}

				}

			}
		}

		return json_encode($output);
	}


	public static function downloadFileUnsplash($path, $file, $id)
	{

		if(preg_match("#^https://images.unsplash.com/.*?#", $file))
		{
			JLoader::register('QuantummanagerHelper', JPATH_SITE . '/administrator/components/com_quantummanager/helpers/quantummanager.php');
			$path = QuantummanagerHelper::preparePath($path);

			$fileContent = file_get_contents($file);
			$filePath = JPATH_ROOT . DIRECTORY_SEPARATOR . $path;
			$fileName = $id . '.jpg';
			file_put_contents($filePath . DIRECTORY_SEPARATOR . $fileName, $fileContent);

			JLoader::register('QuantummanagerHelperImage', JPATH_ROOT . '/administrator/components/com_quantummanager/helpers/image.php');
			$image = new QuantummanagerHelperImage;
			$image->afterUpload($filePath . DIRECTORY_SEPARATOR . $fileName);

		}



	}


	/**
	 * @param $path
	 * @param $file
	 *
	 *
	 * @since version
	 * @throws Exception
	 */
	public static function generatePreviewImage($path, $file)
	{
		$app = Factory::getApplication();
		$splitFile = explode('.', $file);
		$exs = mb_strtolower(array_pop($splitFile));
		$mediaIconsPath = 'media/com_quantummanager/images/icons/';
		$siteUrl = Uri::root();

		if(empty($file))
		{
			$app->redirect($siteUrl . $mediaIconsPath . 'folder.svg');
		}

		if(in_array($exs, ['jpg', 'jpeg', 'png', 'gif']))
		{
			JLoader::register('JInterventionimage', JPATH_LIBRARIES . DIRECTORY_SEPARATOR . 'jinterventionimage' . DIRECTORY_SEPARATOR . 'jinterventionimage.php');
			$path = QuantummanagerHelper::preparePath($path);
			$directory = JPATH_ROOT . DIRECTORY_SEPARATOR . $path;
			$manager = JInterventionimage::getInstance();
			$cacheSource =  JPATH_ROOT . DIRECTORY_SEPARATOR . 'images/com_quantummanager/cache';
			$cache = $cacheSource;
			$pathArr = explode('/', $path);

			for($i=0;$i<count($pathArr);$i++)
			{
				$cache .= DIRECTORY_SEPARATOR . $pathArr[$i];
				if(!file_exists($cache))
				{
					Folder::create($cache);
				}
			}

			if (!file_exists($cache . DIRECTORY_SEPARATOR . $file))
			{
				$manager->make($directory . DIRECTORY_SEPARATOR . $file)->resize(null, 320, function ($constraint) {
					$constraint->aspectRatio();
				})->save($cache . DIRECTORY_SEPARATOR . $file);
			}

			$app->redirect($siteUrl . 'images/com_quantummanager/cache' . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $file . '?=' . rand(111111, 999999));
		}

		if(in_array($exs, ['svg']))
		{
			$path = QuantummanagerHelper::preparePath($path);
			$app->redirect($siteUrl . $path . DIRECTORY_SEPARATOR . $file . '?=' . rand(111111, 999999));
		}

		if(in_array($exs, ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'mp3', 'ogg', 'flac', 'pdf', 'zip', 'txt', 'html', 'css', 'js', 'webp']))
		{
			$app->redirect( $siteUrl . $mediaIconsPath . $exs . '.svg');
		}

		$app->redirect($siteUrl . $mediaIconsPath . 'other.svg');

	}


	/**
	 * @param $path
	 * @param $file
	 * @param string $name
	 *
	 * @return string
	 *
	 * @since version
	 * @throws Exception
	 */
	public static function renameFile($path, $file, $name = '')
	{
		JLoader::register('JInterventionimage', JPATH_LIBRARIES . DIRECTORY_SEPARATOR . 'jinterventionimage' . DIRECTORY_SEPARATOR . 'jinterventionimage.php');
		$path = QuantummanagerHelper::preparePath($path);
		$app = Factory::getApplication();
		$splitFile = explode('.', $file);
		$exs = mb_strtolower(array_pop($splitFile));
		$output = [
			'status' => 'fail'
		];

		$lang = Factory::getLanguage();
		$nameSafe = File::makeSafe($lang->transliterate($name), ['#^\.#', '#\040#']);

		if(!in_array($exs, ['php', 'php7', 'php5', 'php4', 'php3', 'php4', 'phtml', 'phps', 'sh', 'exe']))
		{
			if(file_exists(JPATH_ROOT . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $file))
			{
				if(rename(JPATH_ROOT . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $file, JPATH_ROOT . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $nameSafe . '.' . $exs))
				{
					$output = [
						'status' => 'ok'
					];
				}
			}
		}

		return json_encode($output);
	}


}