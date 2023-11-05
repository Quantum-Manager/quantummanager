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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\Component\QuantumManager\Administrator\Helper\QuantummanagerHelper;
use Joomla\Component\QuantumManager\Administrator\Helper\QuantummanagerLibsHelper;

/**
 * Class QuantumuploadField
 */
class QuantumuploadField extends QuantumbaseField
{

	/**
	 * @var string
	 */
	public $type = 'QuantumUpload';

	/**
	 * @var string
	 * @since version
	 */
	protected $layout = 'quantumupload';

	/**
	 * Allow to override renderer include paths in child fields
	 *
	 * @return  array
	 *
	 * @since   3.5
	 */
	protected function getLayoutPaths()
	{
		$renderer = new FileLayout('default');
		$renderer->getDefaultIncludePaths();

		return array_merge(parent::getLayoutPaths(), [
			JPATH_ROOT . '/administrator/components/com_quantummanager/layouts/fields'
		], $renderer->getDefaultIncludePaths());
	}


	/**
	 * @return array
	 */
	protected function getLayoutData()
	{

		return array_merge(parent::getLayoutData(),
			[
				'scope'          => $this->scope,
				'directory'      => $this->directory,
				'dropAreaHidden' => $this->dropAreaHidden,
				'maxsize'        => $this->maxsize,
				'maxsizeServer'  => $this->maxsizeServer,
				'cssClass'       => $this->cssClass,
				'other'          => '',
			]
		);
	}

	public function getInput()
	{
		try
		{

			$this->__set('standalone', $this->getAttribute('standalone', true));
			$this->__set('cssClass', $this->getAttribute('cssClass', ''));
			$this->__set('maxsize', $this->getAttribute('maxsize', QuantummanagerHelper::getParamsComponentValue('maxsize', 2)));
			$this->__set('maxsizeServer', QuantummanagerHelper::fileUploadMaxSize());
			$this->directory      = isset($this->directory) ? $this->directory : $this->getAttribute('directory', 'images');
			$this->scope          = isset($this->scope) ? $this->scope : $this->getAttribute('scope', 'images');
			$this->dropAreaHidden = isset($this->dropAreaHidden) ? $this->dropAreaHidden : $this->getAttribute('dropAreaHidden', QuantummanagerHelper::getParamsComponentValue('dropareahidden', 0));

			QuantummanagerHelper::loadLang();
			QuantummanagerLibsHelper::includeScriptHead();
			QuantummanagerLibsHelper::includes([
				'core',
				'utils',
			]);

			HTMLHelper::_('stylesheet', 'com_quantummanager/quantumupload.css', [
				'version'  => filemtime(__FILE__),
				'relative' => true
			]);

			HTMLHelper::_('script', 'com_quantummanager/quantumupload.js', [
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