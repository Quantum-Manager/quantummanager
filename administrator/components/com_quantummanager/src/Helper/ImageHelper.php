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
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Helper\MediaHelper;

/**
 * Class ImageHelper
 */
class ImageHelper
{


	private $exifs = [];

	/**
	 * @param          $path_source
	 * @param          $file
	 * @param   array  $options
	 *
	 * @return bool
	 */
	public function afterUpload($path_source, $file, $options = [])
	{
		$info = pathinfo($file);

		if (isset($info['extension']) && (!in_array(mb_strtolower($info['extension']), ['jpg', 'jpeg', 'png', 'webp'])))
		{
			return false;
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


	/**
	 * @param $file
	 */
	public function resizeWatermark($file)
	{
		try
		{

			$fileWatermark = JPATH_SITE . DIRECTORY_SEPARATOR . QuantummanagerHelper::getParamsComponentValue('overlayfile');
			$position      = QuantummanagerHelper::getParamsComponentValue('overlaypos', 'bottom-right');
			$padding       = QuantummanagerHelper::getParamsComponentValue('overlaypadding', 10);

			if (QuantummanagerHelper::isJoomla4())
			{
				$fileWatermark = MediaHelper::getCleanMediaFieldValue($fileWatermark);
			}
			if (file_exists($file) && file_exists($fileWatermark))
			{

				JLoader::register('JInterventionimage', JPATH_LIBRARIES . DIRECTORY_SEPARATOR . 'jinterventionimage' . DIRECTORY_SEPARATOR . 'jinterventionimage.php');
				$manager = JInterventionimage::getInstance(['driver' => $this->getNameDriver()]);
				$image   = $manager->make($file);

				$managerForWatermark = JInterventionimage::getInstance(['driver' => $this->getNameDriver()]);
				$watermark           = $managerForWatermark->make($fileWatermark);

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
					$watermark->resize((int) $logoWidthMax, (int) $logoHeightMax, function ($constraint) {
						$constraint->aspectRatio();
						$constraint->upsize();
					});
				}

				if ($logoWidth > $imageWidth && $logoHeight > $imageHeight)
				{
					return false;
				}

				$image->insert($watermark, $position, $padding, $padding);
				$image->save($file);

			}


		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}

	}


	/**
	 * @param         $file
	 * @param   null  $widthFit
	 * @param   null  $heightFit
	 */
	public function bestFit($file, $widthFit = null, $heightFit = null)
	{
		JLoader::register('JInterventionimage', JPATH_LIBRARIES . DIRECTORY_SEPARATOR . 'jinterventionimage' . DIRECTORY_SEPARATOR . 'jinterventionimage.php');
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


		$manager = JInterventionimage::getInstance(['driver' => $this->getNameDriver()]);
		$manager
			->make($file)
			->resize($newWidth, $newHeight, function ($constraint) {
				$constraint->aspectRatio();
				$constraint->upsize();
			})
			->save($file);

	}


	/**
	 * @param         $file
	 * @param   null  $widthFit
	 * @param   null  $heightFit
	 */
	public function fit($file, $widthFit = null, $heightFit = null)
	{
		JLoader::register('JInterventionimage', JPATH_LIBRARIES . DIRECTORY_SEPARATOR . 'jinterventionimage' . DIRECTORY_SEPARATOR . 'jinterventionimage.php');
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
			$maxHeight = (int) QuantummanagerHelper::getParamsComponentValue('rezizemaxwidth', 1920);
		}
		else
		{
			$maxHeight = (int) $heightFit;
		}

		$manager = JInterventionimage::getInstance(['driver' => $this->getNameDriver()]);
		$manager
			->make($file)
			->fit($maxWidth, $maxHeight, function ($constraint) {
				$constraint->aspectRatio();
			})
			->save($file);

	}


	public function foldersResize($path_source, $file)
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


	/**
	 * @param         $file
	 * @param   null  $widthFit
	 * @param   null  $heightFit
	 */
	public function resize($file, $widthFit = null, $heightFit = null)
	{
		JLoader::register('JInterventionimage', JPATH_LIBRARIES . DIRECTORY_SEPARATOR . 'jinterventionimage' . DIRECTORY_SEPARATOR . 'jinterventionimage.php');
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
			$maxHeight = (int) QuantummanagerHelper::getParamsComponentValue('rezizemaxwidth', 1920);
		}
		else
		{
			$maxHeight = (int) $heightFit;
		}

		$manager = JInterventionimage::getInstance(['driver' => $this->getNameDriver()]);
		$manager
			->make($file)
			->resize($maxWidth, $maxHeight, function ($constraint) {
				$constraint->aspectRatio();
			})
			->resizeCanvas($maxWidth, $maxHeight)
			->save($file);

	}


	/**
	 * @param $fileSource
	 */
	public function rotateExif($fileSource)
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
					JLoader::register('JInterventionimage', JPATH_LIBRARIES . DIRECTORY_SEPARATOR . 'jinterventionimage' . DIRECTORY_SEPARATOR . 'jinterventionimage.php');
					$manager = JInterventionimage::getInstance(['driver' => $this->getNameDriver()]);
					$manager
						->make($fileSource)
						->rotate($angle)
						->save($fileSource);
				}
			}
		}

	}


	/**
	 * @param $fileSource
	 */
	public function originalSave($fileSource)
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


	/**
	 * @param $file
	 *
	 * @return bool
	 *
	 * @since version
	 */
	public function otherFilters($file)
	{
		try
		{

			$info = pathinfo($file);
			if (isset($info['extension']) && (!in_array(mb_strtolower($info['extension']), ['jpg', 'jpeg', 'png', 'webp'])))
			{
				return false;
			}

			JLoader::register('JInterventionimage', JPATH_LIBRARIES . DIRECTORY_SEPARATOR . 'jinterventionimage' . DIRECTORY_SEPARATOR . 'jinterventionimage.php');

			$input   = \Joomla\CMS\Factory::getApplication()->input;
			$filters = $input->getString('filters', '');
			if (!empty($filters))
			{
				$filters = json_decode($filters, JSON_OBJECT_AS_ARRAY);
				if (is_array($filters))
				{
					$manager = JInterventionimage::getInstance(['driver' => $this->getNameDriver()]);
					$manager = $manager->make($file);


					if (isset($filters['compression']))
					{
						if ((int) $filters['compression'] > 0)
						{
							if (in_array($info['extension'], ['jpg', 'jpeg']))
							{
								$manager = $manager->encode('jpg', (int) $filters['compression']);
								$manager->save($file, (int) $filters['compression']);

								$manager = JInterventionimage::getInstance(['driver' => $this->getNameDriver()]);
								$manager = $manager->make($file);
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


	/**
	 * @param $file
	 */
	public function reloadCache($file)
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


	/**
	 *
	 * @return string
	 *
	 * @since version
	 */
	public function getNameDriver()
	{
		if (extension_loaded('imagick'))
		{
			return 'imagick';
		}

		return 'gd';
	}


	/**
	 * @param $file
	 */
	public function saveExif($file)
	{
		if (!empty($this->exifs))
		{
			return;
		}

		$exifSave = (int) QuantummanagerHelper::getParamsComponentValue('exifsave', 0);
		if ($exifSave)
		{
			JLoader::register('JPel', JPATH_LIBRARIES . DIRECTORY_SEPARATOR . 'jpel' . DIRECTORY_SEPARATOR . 'jpel.php');
			$fi = JPel::instance($file);
			if ($fi)
			{
				$this->exifs = $fi->getExif();
			}
		}

	}


	/**
	 * @param $file
	 */
	public function writeExif($file)
	{
		if (empty($this->exifs))
		{
			return;
		}

		$exifSave = (int) QuantummanagerHelper::getParamsComponentValue('exifsave', 0);
		if ($exifSave)
		{
			JLoader::register('JPel', JPATH_LIBRARIES . DIRECTORY_SEPARATOR . 'jpel' . DIRECTORY_SEPARATOR . 'jpel.php');
			$fi = JPel::instance($file);
			if ($fi)
			{
				$fi->setExif($this->exifs);
				$fi->save($file);
				$this->exifs = [];
			}
		}

	}

}