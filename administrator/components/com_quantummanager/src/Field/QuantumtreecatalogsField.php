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
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\QuantumManager\Administrator\Helper\QuantummanagerLibsHelper;

/**
 * Class QuantumtreecatalogsField
 */
class QuantumtreecatalogsField extends QuantumbaseField
{

	/**
	 * @var string
	 */
	public $type = 'QuantumTreeCatalogs';

	/**
	 * @var string
	 */
	protected $layout = 'quantumtreecatalogs';


	protected $directory = '';

	/**
	 * Allow to override renderer include paths in child fields
	 *
	 * @return  array
	 *
	 * @since   3.5
	 */
	protected function getLayoutPaths()
	{
		$path = JPATH_ROOT . '/administrator/components/com_quantummanager/layouts/fields';

		if ($this->standalone)
		{
			if (file_exists($path . '/single/' . $this->layout . '.php'))
			{
				$path .= '/single';
			}
		}

		return array_merge(parent::getLayoutPaths(), [
			$path,
		]);
	}


	/**
	 * @return array
	 */
	protected function getLayoutData()
	{
		return array_merge(parent::getLayoutData(),
			[
				'urlFull'   => Uri::root(false),
				'urlBase'   => Uri::root(true),
				'directory' => $this->directory,
				'cssClass'  => $this->cssClass,
				'scope'     => $this->scope,
				'other'     => '',
			]
		);
	}


	public function getInput()
	{
		try
		{

			$this->__set('standalone', $this->getAttribute('standalone', true));
			$this->__set('cssClass', $this->getAttribute('cssClass', ''));
			$this->directory = $this->getAttribute('directory', 'root');

			QuantummanagerLibsHelper::includeScriptHead();
			QuantummanagerLibsHelper::includes([
				'core',
				'utils',
				'alert',
			]);

			HTMLHelper::_('stylesheet', 'com_quantummanager/quantumtreecatalogs.css', [
				'version'  => filemtime(__FILE__),
				'relative' => true
			]);

			HTMLHelper::_('script', 'com_quantummanager/quantumtreecatalogs.js', [
				'version'  => filemtime(__FILE__),
				'relative' => true
			]);

			$field = parent::getInput();

			if ($this->standalone)
			{
				$filemanager = new FileLayout('fieldstandalone', JPATH_ROOT . '/administrator/components/com_quantummanager/layouts');

				return $filemanager->render(array_merge($this->getLayoutData(), ['field' => $field]));
			}

			return $field;
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}

}