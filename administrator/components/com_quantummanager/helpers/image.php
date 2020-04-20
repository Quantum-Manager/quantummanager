<?php
/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;

/**
 * Class QuantummanagerHelperImage
 */
class QuantummanagerHelperImage
{

	public $paramsComponent;

	public function __construct()
	{
		$this->paramsComponent = ComponentHelper::getParams('com_quantummanager');
	}

	/**
	 * @param $file
	 * @param array $options
	 * @return bool
	 */
	public function afterUpload($file, $options = [])
	{
		$info = pathinfo($file);

		if(isset($info['extension']) && (!in_array(mb_strtolower($info['extension']), ['jpg', 'jpeg', 'png', 'webp'])))
		{
			return false;
		}

		$defaultOptions = [
			'original' => 1,
			'resize' => 1,
			'overlay' => 1,
		];

		foreach ($options as $key => $value)
		{
			$defaultOptions[$key] = $value;
		}

		if($this->paramsComponent->get('original', 0) && (int)$defaultOptions['original'])
		{
			$this->originalSave($file);
		}

		if($this->paramsComponent->get('resize', 0) && (int)$defaultOptions['resize'])
		{
			$this->resizeFit($file);
		}

		if((int)$this->paramsComponent->get('overlay', 0) === 1 && (int)$defaultOptions['overlay'])
		{
			$this->resizeWatermark($file);
		}

		$this->otherFilters($file);
		$this->reloadCache($file);

	}

	/**
	 * @param $file
	 */
	public function resizeWatermark($file)
	{
		try
		{

			$fileWatermark = JPATH_SITE . DIRECTORY_SEPARATOR . $this->paramsComponent->get('overlayfile');
			$position = $this->paramsComponent->get('overlaypos', 'bottom-right');
			$padding = $this->paramsComponent->get('overlaypadding', 10);
			$percent = $this->paramsComponent->get('overlaypadding', 10);

			if(file_exists($file) && file_exists($fileWatermark))
			{
				JLoader::register('JInterventionimage', JPATH_LIBRARIES . DIRECTORY_SEPARATOR . 'jinterventionimage' . DIRECTORY_SEPARATOR . 'jinterventionimage.php');
				$manager = JInterventionimage::getInstance(['driver' => $this->getNameDriver()]);
				$image = $manager->make($file);

				$managerForWatermark = JInterventionimage::getInstance(['driver' => $this->getNameDriver()]);
				$watermark = $managerForWatermark->make($fileWatermark);

				$logoWidth = $watermark->width();
				$logoHeight = $watermark->height();
				$imageWidth = $image->width();
				$imageHeight = $image->height();

				if((int)$this->paramsComponent->get('overlaypercent', 0))
				{
					//сжимаем водяной знак по процентному соотношению от изображения на который накладывается
					$precent = (double)$this->paramsComponent->get('overlaypercentvalue', 10);
					$logoWidthMax = $imageWidth / 100 * $precent;
					$logoHeightMax = $imageHeight / 100 * $precent;
					$watermark->resize((int)$logoWidthMax, (int)$logoHeightMax, function ($constraint) {
						$constraint->aspectRatio();
						$constraint->upsize();
					});
				}

				if($logoWidth > $imageWidth && $logoHeight > $imageHeight)
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
	 * @param $file
	 */
	public function resizeFit($file)
	{
		JLoader::register('JInterventionimage', JPATH_LIBRARIES . DIRECTORY_SEPARATOR . 'jinterventionimage' . DIRECTORY_SEPARATOR . 'jinterventionimage.php');
		list($width, $height, $type, $attr) = getimagesize($file);
		$newWidth = $width;
		$newHeight = $height;
		$maxWidth = (int)$this->paramsComponent->get('rezizemaxwidth', 1920);
		$maxHeight = (int)$this->paramsComponent->get('rezizemaxheight', 1280);
		$ratio = $width / $height;

		if($width > $maxWidth)
		{
			$newWidth = $maxWidth;
			$newHeight = round($newWidth / $ratio);
		}

		if($newHeight > $maxHeight)
		{
			$newHeight = $maxHeight;
			$newWidth = round($newHeight * $ratio);
		}

		$manager = JInterventionimage::getInstance(['driver' => $this->getNameDriver()]);
		$manager->make($file)
			->resize($newWidth, $newHeight, function ($constraint) {
			$constraint->aspectRatio();
			$constraint->upsize();
		})
			->save($file);
	}

	/**
	 * @param $fileSource
	 */
	public function originalSave($fileSource)
	{
		try
		{
			$path = explode(DIRECTORY_SEPARATOR, $fileSource);
			$file = array_pop($path);
			$pathSave = implode(DIRECTORY_SEPARATOR, $path) . DIRECTORY_SEPARATOR . '_original';

			if (!file_exists($pathSave))
			{
				Folder::create($pathSave);
			}

			if(!file_exists($pathSave . DIRECTORY_SEPARATOR . $file))
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
			if(isset($info['extension']) && (!in_array(mb_strtolower($info['extension']), ['jpg', 'jpeg', 'png'])))
			{
				return false;
			}

			JLoader::register('JInterventionimage', JPATH_LIBRARIES . DIRECTORY_SEPARATOR . 'jinterventionimage' . DIRECTORY_SEPARATOR . 'jinterventionimage.php');

			$input = \Joomla\CMS\Factory::getApplication()->input;
			$filters = $input->getString('filters', '');
			if(!empty($filters))
			{
				$filters = json_decode($filters, JSON_OBJECT_AS_ARRAY);
				if(is_array($filters))
				{
					$manager = JInterventionimage::getInstance(['driver' => $this->getNameDriver()]);
					$manager = $manager->make($file);

					if(isset($filters['compression']))
					{
						if((int)$filters['compression'] > 0)
						{
							if(in_array($info['extension'], ['jpg', 'jpeg']))
							{
								$manager = $manager->encode('jpg', (int)$filters['compression']);
							}

							if($info['extension'] === 'png')
							{
								$manager = $manager->encode('png', (int)$filters['compression']);
							}
						}
					}

					if(isset($filters['sharpen']))
					{
						if((int)$filters['sharpen'] > 0)
						{
							$manager = $manager->sharpen((int)$filters['sharpen']);
						}
					}

					if(isset($filters['brightness']))
					{
						if((int)$filters['brightness'] !== 0)
						{
							$manager = $manager->brightness((int)$filters['brightness']);
						}
					}

					if(isset($filters['blur']))
					{
						if((int)$filters['blur'] > 0)
						{
							$manager = $manager->blur((int)$filters['blur']);
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
			$cacheSource =  JPATH_ROOT . DIRECTORY_SEPARATOR . 'cache/com_quantummanager';
			$cache = $cacheSource . DIRECTORY_SEPARATOR . str_replace(JPATH_SITE . DIRECTORY_SEPARATOR,'', $file);
			if(file_exists($cache))
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


}