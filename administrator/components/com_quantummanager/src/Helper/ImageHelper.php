<?php namespace Joomla\Component\QuantumManager\Administrator\Helper;

/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

defined('_JEXEC') or die;

use Exception;
use JLoader;
use Joomla\CMS\Factory;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Helper\MediaHelper;
use Joomla\Libraries\JInterventionimage\Manager;

use function defined;
use function exif_read_data;
use function function_exists;
use function is_array;
use function json_decode;
use function pathinfo;
use function in_array;
use function mb_strtolower;
use function error_reporting;
use function explode;
use function array_pop;
use function implode;
use function file_exists;
use function getimagesize;
use function is_null;
use function round;
use function extension_loaded;
use function substr;
use function mb_strpos;
use function mb_strlen;
use function str_replace;

class ImageHelper
{

	private array $exifs = [];

	public function afterUpload(string $path_source, string $file, array $options = []): void
	{
		$info = pathinfo($file);

		if (isset($info['extension']) && (!in_array(mb_strtolower($info['extension']), ['jpg', 'jpeg', 'png', 'webp'])))
		{
			return;
		}

		$defaultOptions = [
			'original'      => 1,
			'rotateExif'    => 0,
			'resize'        => 1,
			'overlay'       => 1,
			'foldersResize' => 1,
		];

		foreach ($options as $key => $value)
		{
			$defaultOptions[$key] = $value;
		}

		$this->saveExif($file);

		if (QuantummanagerHelper::getParamsComponentValue('original', 0) && (int) $defaultOptions['original'])
		{
			$this->originalSave($file);
		}

		if (QuantummanagerHelper::getParamsComponentValue('resize', 0) && (int) $defaultOptions['resize'])
		{
			$this->bestFit($file);
		}

		if ((int) $defaultOptions['foldersResize'])
		{
			$this->foldersResize($path_source, $file);
		}

		if ((int) QuantummanagerHelper::getParamsComponentValue('overlay', 0) === 1 && (int) $defaultOptions['overlay'])
		{
			$this->resizeWatermark($file);
		}

		if ((int) QuantummanagerHelper::getParamsComponentValue('rotateexif', 0) === 1 && (int) $defaultOptions['rotateExif'])
		{
			$this->rotateExif($file);
		}

		$this->otherFilters($file);
		$this->writeExif($file);
		$this->reloadCache($file);

	}

	public function saveExif(string $file): void
	{
		if (!empty($this->exifs))
		{
			return;
		}

		$exifSave = (int) QuantummanagerHelper::getParamsComponentValue('exifsave', 0);

		if ($exifSave)
		{
			$error_reporting = error_reporting();
			error_reporting($error_reporting & ~E_DEPRECATED);

			JLoader::register('JPel', JPATH_LIBRARIES . DIRECTORY_SEPARATOR . 'jpel' . DIRECTORY_SEPARATOR . 'jpel.php');
			$fi = \JPel::instance($file);
			if ($fi)
			{
				$this->exifs = $fi->getExif();
			}
		}

	}

	public function originalSave(string $fileSource): void
	{
		try
		{
			$path     = explode(DIRECTORY_SEPARATOR, $fileSource);
			$file     = array_pop($path);
			$pathSave = implode(DIRECTORY_SEPARATOR, $path) . DIRECTORY_SEPARATOR . '_original';

			if (!file_exists($pathSave))
			{
				Folder::create($pathSave);
			}

			if (!file_exists($pathSave . DIRECTORY_SEPARATOR . $file))
			{
				File::copy($fileSource, $pathSave . DIRECTORY_SEPARATOR . $file);
			}

		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}

	public function bestFit(string $file, $widthFit = null, $heightFit = null): void
	{
		list($width, $height, $type, $attr) = getimagesize($file);
		$newWidth  = $width;
		$newHeight = $height;

		if (is_null($widthFit))
		{
			$maxWidth = (int) QuantummanagerHelper::getParamsComponentValue('rezizemaxwidth', 1920);
		}
		else
		{
			$maxWidth = (int) $widthFit;
		}

		if (is_null($heightFit))
		{
			$maxHeight = (int) QuantummanagerHelper::getParamsComponentValue('rezizemaxheight', 1280);
		}
		else
		{
			$maxHeight = (int) $heightFit;
		}

		$ratio = $width / $height;

		if ($width > $maxWidth)
		{
			$newWidth  = $maxWidth;
			$newHeight = round($newWidth / $ratio);
		}

		if ($newHeight > $maxHeight)
		{
			$newHeight = $maxHeight;
			$newWidth  = round($newHeight * $ratio);
		}


		$manager = Manager::getInstance(['driver' => $this->getNameDriver()]);
		$manager
			->read($file)
			->resizeDown($newWidth, $newHeight)
			->save($file);

	}

	public function getNameDriver(): string
	{
		if (extension_loaded('imagick'))
		{
			return 'imagick';
		}

		return 'gd';
	}

	public function foldersResize(string $path_source, string $file): void
	{
		$folders_rules = QuantummanagerHelper::getParamsComponentValue('resizefolders', []);
		foreach ($folders_rules as $folder_rule)
		{
			if (in_array(substr($folder_rule->folder, 0, 1), ['/', '\\']))
			{
				$folder_rule->folder = substr($folder_rule->folder, 1);
			}

			$pos = mb_strpos($path_source, $folder_rule->folder);


			if ($pos !== false)
			{
				if ($pos === 0)
				{
					$more   = mb_strlen(str_replace($path_source, '', $folder_rule->folder));
					$resize = false;

					if ($more > 0)
					{
						if ((int) $folder_rule->subfolder)
						{
							$resize = true;
						}
					}
					else
					{
						$resize = true;
					}


					if ($resize)
					{

						if ($folder_rule->algorithm === 'fit')
						{
							$this->fit($file, (int) $folder_rule->maxwidth, (int) $folder_rule->maxheight);
						}


						if ($folder_rule->algorithm === 'bestfit')
						{
							$this->bestFit($file, (int) $folder_rule->maxwidth, (int) $folder_rule->maxheight);
						}


						if ($folder_rule->algorithm === 'resize')
						{
							$this->resize($file, (int) $folder_rule->maxwidth, (int) $folder_rule->maxheight);
						}
					}
				}
			}
		}
	}

	public function fit(string $file, $widthFit = null, $heightFit = null): void
	{
		if (is_null($widthFit))
		{
			$maxWidth = (int) QuantummanagerHelper::getParamsComponentValue('rezizemaxwidth', 1920);
		}
		else
		{
			$maxWidth = (int) $widthFit;
		}

		if (is_null($heightFit))
		{
			$maxHeight = (int) QuantummanagerHelper::getParamsComponentValue('rezizemaxwidth', 1920);
		}
		else
		{
			$maxHeight = (int) $heightFit;
		}

		$manager = Manager::getInstance(['driver' => $this->getNameDriver()]);
		$manager
			->read($file)
			->cover($maxWidth, $maxHeight)
			->save($file);
	}

	public function resize(string $file, $widthFit = null, $heightFit = null): void
	{

		if (is_null($widthFit))
		{
			$maxWidth = (int) QuantummanagerHelper::getParamsComponentValue('rezizemaxwidth', 1920);
		}
		else
		{
			$maxWidth = (int) $widthFit;
		}

		if (is_null($heightFit))
		{
			$maxHeight = (int) QuantummanagerHelper::getParamsComponentValue('rezizemaxwidth', 1920);
		}
		else
		{
			$maxHeight = (int) $heightFit;
		}

		$manager = Manager::getInstance(['driver' => $this->getNameDriver()]);
		$manager
			->read($file)
			->resize($maxWidth, $maxHeight)
			->resizeCanvas($maxWidth, $maxHeight)
			->save($file);
	}

	public function resizeWatermark(string $file): void
	{
		try
		{

			$fileWatermark = JPATH_SITE . DIRECTORY_SEPARATOR . QuantummanagerHelper::getParamsComponentValue('overlayfile');
			$fileWatermark = MediaHelper::getCleanMediaFieldValue($fileWatermark);
			$position      = QuantummanagerHelper::getParamsComponentValue('overlaypos', 'bottom-right');
			$padding       = QuantummanagerHelper::getParamsComponentValue('overlaypadding', 10);

			if (file_exists($file) && file_exists($fileWatermark))
			{

				$manager = Manager::getInstance(['driver' => $this->getNameDriver()]);
				$image   = $manager->read($file);

				$managerForWatermark = Manager::getInstance(['driver' => $this->getNameDriver()]);
				$watermark           = $managerForWatermark->read($fileWatermark);

				$logoWidth   = $watermark->width();
				$logoHeight  = $watermark->height();
				$imageWidth  = $image->width();
				$imageHeight = $image->height();

				if ((int) QuantummanagerHelper::getParamsComponentValue('overlaypercent', 0))
				{
					//сжимаем водяной знак по процентному соотношению от изображения на который накладывается
					$precent       = (double) QuantummanagerHelper::getParamsComponentValue('overlaypercentvalue', 10);
					$logoWidthMax  = $imageWidth / 100 * $precent;
					$logoHeightMax = $imageHeight / 100 * $precent;
					$watermark->scale((int) $logoWidthMax, (int) $logoHeightMax);
				}

				if ($logoWidth > $imageWidth && $logoHeight > $imageHeight)
				{
					return;
				}

				$image->place($watermark, $position, $padding, $padding);
				$image->save($file);

			}

		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}

	}

	public function rotateExif(string $fileSource): void
	{
		if (function_exists('exif_read_data'))
		{
			$exif = @exif_read_data($fileSource);

			if (!empty($exif['Orientation']))
			{
				$exif['Orientation'] = (int) $exif['Orientation'];
				$rotated             = false;
				$angle               = 0;

				switch ($exif['Orientation'])
				{
					case 3:
						$angle   = 180;
						$rotated = true;
						break;

					case 6:
						$angle   = -90;
						$rotated = true;
						break;

					case 8:
						$angle   = 90;
						$rotated = true;
						break;
				}

				if ($rotated)
				{
					$manager = Manager::getInstance(['driver' => $this->getNameDriver()]);
					$manager
						->read($fileSource)
						->rotate($angle)
						->save($fileSource);
				}
			}
		}
	}

	public function otherFilters(string $file): void
	{
		try
		{

			$info = pathinfo($file);

			if (isset($info['extension']) && (!in_array(mb_strtolower($info['extension']), ['jpg', 'jpeg', 'png', 'webp'])))
			{
				return;
			}

			$input   = Factory::getApplication()->getInput();
			$filters = $input->getString('filters', '');
			if (!empty($filters))
			{
				$filters = json_decode($filters, JSON_OBJECT_AS_ARRAY);
				if (is_array($filters))
				{
					$manager = Manager::getInstance(['driver' => $this->getNameDriver()]);
					$manager = $manager->read($file);

					if (isset($filters['compression']))
					{
						if ((int) $filters['compression'] > 0)
						{
							if (in_array($info['extension'], ['jpg', 'jpeg']))
							{
								$manager = $manager->toJpeg((int) $filters['compression']);
								$manager->save($file);

								$manager = Manager::getInstance(['driver' => $this->getNameDriver()]);
								$manager = $manager->read($file);
							}
						}
					}

					if (isset($filters['sharpen']))
					{
						if ((int) $filters['sharpen'] > 0)
						{
							$manager = $manager->sharpen((int) $filters['sharpen']);
						}
					}

					if (isset($filters['brightness']))
					{
						if ((int) $filters['brightness'] !== 0)
						{
							$manager = $manager->brightness((int) $filters['brightness']);
						}
					}

					if (isset($filters['blur']))
					{
						if ((int) $filters['blur'] > 0)
						{
							$manager = $manager->blur((int) $filters['blur']);
						}
					}

					$manager->save($file);
				}
			}
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}

	public function writeExif(string $file): void
	{
		if (empty($this->exifs))
		{
			return;
		}

		$exifSave = (int) QuantummanagerHelper::getParamsComponentValue('exifsave', 0);
		if ($exifSave)
		{
			$error_reporting = error_reporting();
			error_reporting($error_reporting & ~E_DEPRECATED);

			JLoader::register('JPel', JPATH_LIBRARIES . DIRECTORY_SEPARATOR . 'jpel' . DIRECTORY_SEPARATOR . 'jpel.php');
			$fi = \JPel::instance($file);
			if ($fi)
			{
				$fi->setExif($this->exifs);
				$fi->save($file);
				$this->exifs = [];
			}
		}

	}

	public function reloadCache(string $file): void
	{
		try
		{
			$cacheSource = JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator/cache/com_quantummanager';
			$cache       = $cacheSource . DIRECTORY_SEPARATOR . str_replace(JPATH_SITE . DIRECTORY_SEPARATOR, '', $file);
			if (file_exists($cache))
			{
				File::delete($cache);
			}
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}

}