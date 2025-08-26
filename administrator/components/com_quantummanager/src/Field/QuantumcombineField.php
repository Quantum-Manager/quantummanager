<?php namespace Joomla\Component\QuantumManager\Administrator\Field;

/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\Component\QuantumManager\Administrator\Helper\QuantummanagerHelper;
use Joomla\Component\QuantumManager\Administrator\Helper\QuantummanagerLibsHelper;
use SimpleXMLElement;

class QuantumcombineField extends QuantumbaseField
{

	public $type = 'QuantumCombine';

	protected $layout = 'quantumcombine';

	protected string $fields;

	public function getInput(): string
	{
		try
		{

			QuantummanagerLibsHelper::includeScriptHead();

			$componentParams = ComponentHelper::getParams('com_quantummanager');

			if (empty($this->fields))
			{
				$this->__set('fields', $this->getAttribute('fields', true));
			}

			if (!empty($this->fields))
			{

				if (file_exists(JPATH_ROOT . '/media/com_quantummanager/css/' . $this->layout . '.css'))
				{
					HTMLHelper::_('stylesheet', 'com_quantummanager/' . $this->layout . '.css', [
						'version'  => filemtime(__FILE__),
						'relative' => true
					]);
				}

				if (file_exists(JPATH_ROOT . '/media/com_quantummanager/js/' . $this->layout . '.js'))
				{
					HTMLHelper::_('script', 'com_quantummanager/' . $this->layout . '.js', [
						'version'  => filemtime(__FILE__),
						'relative' => true,
						'defer'    => 'defer'
					]);
				}

				QuantummanagerHelper::loadLang();

				QuantummanagerLibsHelper::includes([
					'utils',
					'split'
				]);

				HTMLHelper::_('script', 'com_quantummanager/combine.js', [
					'version'  => filemtime(__FILE__),
					'relative' => true
				]);

				$fields     = json_decode($this->fields, JSON_OBJECT_AS_ARRAY);
				$htmlFields = [
					'top'                    => '',
					'container-top'          => '',
					'left'                   => '',
					'center'                 => '',
					'right'                  => '',
					'container-bottom'       => '',
					'bottom'                 => '',
					'container-bottom-fixed' => '',
				];

				foreach ($fields as $field => $options)
				{
					$position   = 'center';
					$classField = 'Joomla\\Component\\QuantumManager\\Administrator\\Field\\' . ucfirst($field) . 'Field';

					$optionsForField = $options;
					$optionsForField = array_merge($optionsForField, [
						'standalone' => false
					]);

					$dataAttributes = array_map(static function ($value, $key) {
						return $key . '="' . $value . '"';
					}, array_values($optionsForField), array_keys($optionsForField));


					if (isset($optionsForField['position']))
					{
						$position = $optionsForField['position'];
					}

					if (!isset($htmlFields[$position]))
					{
						$htmlFields[$position] = '';
					}

					$fieldObject = new $classField;
					$fieldObject->setup(new SimpleXMLElement('<field name="" type="' . $field . '" ' . implode(' ', $dataAttributes) . ' />'), '');
					$htmlFields[$position] .= $fieldObject->getInput();

				}

				if ((int) $componentParams->get('review', 1))
				{
					$htmlFields['container-bottom-fixed'] .= (new FileLayout('jedreview', JPATH_ROOT . '/administrator/components/com_quantummanager/layouts'))->render();
				}


				$filemanager = new FileLayout($this->layout, JPATH_ROOT . '/administrator/components/com_quantummanager/layouts');
				$filemanager->addIncludePaths($this->getLayoutPaths());

				return $filemanager->render(array_merge($this->getLayoutData(), $htmlFields));

			}

			return '';

		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}

	}

}