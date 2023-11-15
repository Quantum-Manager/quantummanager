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
use Joomla\Component\QuantumManager\Administrator\Helper\QuantummanagerHelper;
use Joomla\Component\QuantumManager\Administrator\Helper\QuantummanagerLibsHelper;

/**
 * Class QuantumviewfilesField
 */
class QuantumviewfilesField extends QuantumbaseField
{

	/**
	 * @var string
	 */
	public $type = 'QuantumViewfiles';

	/**
	 * @var string
	 */
	protected $layout = 'quantumviewfiles';


	protected $metafile = '1';


	protected $watermark = '0';


	protected $folderdblclick = '0';


	protected $help = '1';


	protected $previewsfolder = '1';


	protected $previewsfolderopen = '1';


	protected $previewslist = [];


	protected $directory = '';


	protected $onlyfiles = '0';


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

		return array_merge(parent::getLayoutData(),
			[
				'directory'          => $this->directory,
				'onlyfiles'          => $this->onlyfiles,
				'metafile'           => $this->metafile,
				'watermark'          => $this->watermark,
				'help'               => $this->help,
				'folderdblclick'     => $this->folderdblclick,
				'previewsfolder'     => $this->previewsfolder,
				'previewsfolderopen' => $this->previewsfolderopen,
				'previewslist'       => $this->previewslist,
				'hash'               => md5(QuantummanagerHelper::preparePath($this->directory)),
				'cssClass'           => $this->cssClass,
				'quantumVersion'     => QuantummanagerHelper::getVersion(),
			]
		);
	}


	/**
	 *
	 * @return string
	 *
	 * @since version
	 */
	public function getInput()
	{
		try
		{

			$this->__set('standalone', $this->getAttribute('standalone', true));
			$this->__set('cssClass', $this->getAttribute('cssClass', ''));
			$this->__set('metafile', $this->getAttribute('metafile', '1'));
			$this->__set('watermark', $this->getAttribute('watermark', '0'));
			$this->__set('folderdblclick', $this->getAttribute('folderdblclick', QuantummanagerHelper::getParamsComponentValue('folderdblclick', '0')));
			$this->__set('help', $this->getAttribute('help', '1'));
			$this->__set('previewsfolder', $this->getAttribute('previewsfolderopen', QuantummanagerHelper::getParamsComponentValue('previewsfolder', '1')));
			$this->__set('previewsfolderopen', $this->getAttribute('previewsfolderopen', QuantummanagerHelper::getParamsComponentValue('previewsfolderopen', '1')));
			$this->__set('previewslist', $this->getAttribute('previewslist', QuantummanagerHelper::getParamsComponentValue('previewslist', [])));
			$this->directory = $this->getAttribute('directory', 'images');
			$this->onlyfiles = $this->getAttribute('onlyfiles', '0');

			QuantummanagerLibsHelper::includeScriptHead();
			QuantummanagerLibsHelper::includes([
				'core',
				'utils',
				'alert',
				'contextmenu',
				'clipboard',
				'notify',
				'lazyload',
				'dragSelect',
			]);

			HTMLHelper::_('stylesheet', 'com_quantummanager/quantumviewfiles.css', [
				'version'  => filemtime(__FILE__),
				'relative' => true
			]);

			HTMLHelper::_('script', 'com_quantummanager/quantumviewfiles.js', [
				'version'  => filemtime(__FILE__),
				'relative' => true
			]);


			$field = parent::getInput();

			if ($this->standalone)
			{
				$filemanager = new FileLayout('fieldstandalone', JPATH_ROOT . '/administrator/components/com_quantummanager/layouts');

				return $filemanager->render(['field' => $field]);
			}

			return $field;
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}

}