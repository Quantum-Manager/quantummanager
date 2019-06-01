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
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Layout\FileLayout;

/**
 * Class JFormFieldQuantumUpload
 */
class JFormFieldQuantumCombine extends JFormField
{

	/**
	 * @var string
	 */
	public $type = 'QuantumCombine';

	/**
	 * @var string
	 */
	protected $layout = 'quantumcombine';

	/**
	 * @var string
	 */
	protected $renderLabelLayout = 'renderlabel';


	/**
	 * Allow to override renderer include paths in child fields
	 *
	 * @return  array
	 *
	 * @since   3.5
	 */
	protected function getLayoutPaths()
	{
		return [
			JPATH_ROOT . '/administrator/components/com_quantummanager/layouts/fields',
			JPATH_ROOT . '/layouts/joomla/form',
		];
	}


	public function getInput()
	{
		try {

			$componentParams = ComponentHelper::getParams('com_quantummanager');

			if(empty($this->fields))
			{
				$this->__set('fields', $this->getAttribute('fields', true));
			}

			if(!empty($this->fields))
			{

				if(file_exists(JPATH_ROOT . '/media/com_quantummanager/css/' . $this->layout . '.css'))
				{
					HTMLHelper::_('stylesheet', 'com_quantummanager/' . $this->layout . '.css', [
						'version' => filemtime(__FILE__),
						'relative' => true
					]);
				}

				if(file_exists(JPATH_ROOT . '/media/com_quantummanager/js/' . $this->layout . '.js'))
				{
					HTMLHelper::_('script', 'com_quantummanager/' . $this->layout . '.js', [
						'version' => filemtime(__FILE__),
						'relative' => true
					]);
				}

				HTMLHelper::_('script', 'com_quantummanager/combine.js', [
					'version' => filemtime(__FILE__),
					'relative' => true
				]);

				$fields = json_decode($this->fields, JSON_OBJECT_AS_ARRAY);
				$htmlFields = [
					'top' => '',
					'container-top' => '',
					'left' => '',
					'center' => '',
					'right' => '',
					'container-bottom' => '',
					'bottom' => '',
					'container-bottom-fixed' => '',
				];

				foreach ($fields as $field => $options)
				{
					$position = 'center';
					$classField = 'JFormField' . ucfirst($field);

					JLoader::register($classField, JPATH_ROOT . '/administrator/components/com_quantummanager/fields/' . $field . '.php');

					$optionsForField = $options;
					$optionsForField = array_merge($optionsForField, [
						'standalone' => false
					]);

					$dataAttributes = array_map(function($value, $key)
					{
						return $key.'="'.$value.'"';
					}, array_values($optionsForField), array_keys($optionsForField));


					if(isset($optionsForField['position'])) {
						$position = $optionsForField['position'];
					}

					$fieldObject = new $classField;
					$fieldObject->setup(new SimpleXMLElement("<field name=\"upload-files\" type=\"" . $field . "\" " . implode(' ', $dataAttributes) . " />"), '');
					$htmlFields[$position] .= $fieldObject->getInput();


				}

				if((int)$componentParams->get('help', 1))
				{
					$htmlFields['container-bottom-fixed'] .= (new FileLayout( 'help', JPATH_ROOT . '/administrator/components/com_quantummanager/layouts'))->render();
				}

				JLoader::register('QuantummanagerHelper', JPATH_SITE . '/administrator/components/com_quantummanager/helpers/quantummanager.php');
				QuantummanagerHelper::loadLang();

				$filemanager = new FileLayout($this->layout, JPATH_ROOT . '/administrator/components/com_quantummanager/layouts');
				return $filemanager->render(array_merge($this->getLayoutData(), $htmlFields));

			}
			else
			{
				return '';
			}

		}
		catch (Exception $e) {
			echo $e->getMessage();
		}
	}



}