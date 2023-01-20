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
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Layout\FileLayout;

JLoader::register('JFormFieldQuantumbase', __DIR__ . DIRECTORY_SEPARATOR . 'quantumbase.php');

/**
 * Class JFormFieldQuantumcropperjs
 */
class JFormFieldQuantumcropperjs extends JFormField
{

	/**
	 * @var string
	 */
	public $type = 'Quantumcropperjs';

	/**
	 * @var string
	 */
	protected $layout = 'quantumcropperjs';


	/**
	 * Allow to override renderer include paths in child fields
	 *
	 * @return  array
	 *
	 * @since   3.5
	 */
	protected function getLayoutPaths()
	{
		return array_merge(parent::getLayoutPaths(), [
			JPATH_ROOT . '/administrator/components/com_quantummanager/layouts/fields',
		]);
	}


	/**
	 * @return array
	 */
	protected function getLayoutData()
	{
		JLoader::register('QuantummanagerHelperImage', JPATH_ROOT . '/administrator/components/com_quantummanager/helpers/image.php');
		$driver = (new QuantummanagerHelperImage)->getNameDriver();

        $values_default = [
            'compression' => QuantummanagerHelper::getParamsComponentValue('compression', 90),
            'sharpen' => QuantummanagerHelper::getParamsComponentValue('sharpen', 0),
            'brightness' => QuantummanagerHelper::getParamsComponentValue('brightness', 0),
            'blur' => QuantummanagerHelper::getParamsComponentValue('blur', 0),
        ];

		return array_merge(parent::getLayoutData(),
			[
				'paramsComponents' => ComponentHelper::getParams('com_quantummanager'),
				'cssClass' => $this->cssClass,
				'driver' => $driver,
                'values_default' => $values_default
			]
		);
	}


	public function getInput()
	{
		try {


			$this->__set('standalone', $this->getAttribute('standalone', true));
			$this->__set('cssClass', $this->getAttribute('cssClass', ''));

			JLoader::register('QuantummanagerHelper', JPATH_SITE . '/administrator/components/com_quantummanager/helpers/quantummanager.php');
            JLoader::register('QuantummanagerLibs', JPATH_SITE . '/administrator/components/com_quantummanager/helpers/quantumlibs.php');

            QuantummanagerLibs::includeScriptHead();
            QuantummanagerLibs::includes([
                'core',
                'utils',
                'imageEditor',

            ]);


			HTMLHelper::_('stylesheet', 'com_quantummanager/quantumcropperjs.css', [
				'version' => filemtime(__FILE__),
				'relative' => true
			]);

			HTMLHelper::_('script', 'com_quantummanager/quantumcropperjs.js', [
				'version' => filemtime(__FILE__),
				'relative' => true
			]);


			$field = parent::getInput();

			if($this->standalone)
			{
				$filemanager = new FileLayout( 'fieldstandalone', JPATH_ROOT . '/administrator/components/com_quantummanager/layouts');
				return $filemanager->render(['field' => $field]);
			}

			return $field;
		}
		catch (Exception $e) {
			echo $e->getMessage();
		}
	}

}