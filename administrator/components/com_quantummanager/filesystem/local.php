<?php defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
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
	 * @return string
	 */
	public static function getDirectories($path)
	{
		JLoader::register('QuantummanagerHelper', JPATH_SITE . '/administrator/components/com_quantummanager/helpers/quantummanager.php');
		$path = QuantummanagerHelper::preparePath($path);
		$directories = [];
		$directories = self::showdir($path, true, true);

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
			$subdir = self::showdir($dir, $folderOnly, $showRoot, $level + 1, $ef);
			return [
				'path' => QuantummanagerHelper::getFolderRoot(),
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
								'subpath' => self::showdir($dir . DIRECTORY_SEPARATOR . $name, $folderOnly, $showRoot, $level + 1, $ef)
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
	 * @return string
	 */
	public static function getFiles($path)
	{
		try {

			JLoader::register('JInterventionimage', JPATH_LIBRARIES . DIRECTORY_SEPARATOR . 'jinterventionimage' . DIRECTORY_SEPARATOR . 'jinterventionimage.php');
			$path = QuantummanagerHelper::preparePath($path);
			$directory = JPATH_ROOT . DIRECTORY_SEPARATOR . $path;
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

			foreach ($files as $file) {
				$tmpExs = explode('.', $file);

				if (!isset($tmpExs[1])) {
					continue;
				}

				//генерация кеша для картинок
				if(in_array(strtolower($tmpExs[1]), ['jpg', 'png', 'jpeg', 'gif']))
				{
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
				}

				$filesOutput[] = $file;
			}

			return json_encode([
				'files' => $filesOutput,
				'directories' => $directories
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
	public function delete($path = '', $list = [])
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
	public function converterSave()
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

				if(!QuantummanagerHelper::checkFile($file['name'], $file['type']))
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

}