<?php
/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Layout\FileLayout;

/**
 * Class JFormFieldQuantumviewfiles
 */
class JFormFieldQuantumviewfiles extends JFormField
{

	/**
	 * @var string
	 */
	public $type = 'QuantumViewfiles';

	/**
	 * @var string
	 */
	protected $layout = 'quantumviewfiles';


	public function __construct($form = null)
	{
		JLoader::register('QuantummanagerHelper', JPATH_SITE . '/administrator/components/com_quantummanager/helpers/quantummanager.php');
		parent::__construct($form);
	}

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
				'directory' => $this->directory,
				'onlyfiles' => $this->onlyfiles,
				'metafile' => $this->metafile,
				'watermark' => $this->watermark,
				'help' => $this->help,
				'hash' => md5(QuantummanagerHelper::preparePath($this->directory)),
				'cssClass' => $this->cssClass,
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
		try {

			$this->__set('standalone', $this->getAttribute('standalone', true));
			$this->__set('cssClass', $this->getAttribute('cssClass', ''));
			$this->__set('metafile', $this->getAttribute('metafile', '1'));
			$this->__set('watermark', $this->getAttribute('watermark', '0'));
			$this->__set('help', $this->getAttribute('help', '1'));
			$this->directory = $this->getAttribute('directory', 'images');
			$this->onlyfiles = $this->getAttribute('onlyfiles', '0');

			QuantummanagerHelper::includeScriptHead();

			HTMLHelper::_('stylesheet', 'com_quantummanager/main.css', [
				'version' => filemtime(__FILE__),
				'relative' => true
			]);

			HTMLHelper::_('stylesheet', 'com_quantummanager/quantumviewfiles.css', [
				'version' => filemtime(__FILE__),
				'relative' => true
			]);

			HTMLHelper::_('stylesheet', 'com_quantummanager/contextual.css', [
				'version' => filemtime(__FILE__),
				'relative' => true
			]);

			HTMLHelper::_('stylesheet', 'com_quantummanager/notify.css', [
				'version' => filemtime(__FILE__),
				'relative' => true
			]);

			HTMLHelper::_('script', 'com_quantummanager/main.js', [
				'version' => filemtime(__FILE__),
				'relative' => true
			]);

			HTMLHelper::_('script', 'com_quantummanager/jsalert.min.js', [
				'version' => filemtime(__FILE__),
				'relative' => true
			]);

			HTMLHelper::_('script', 'com_quantummanager/clipboard.min.js', [
				'version' => filemtime(__FILE__),
				'relative' => true
			]);

			HTMLHelper::_('script', 'com_quantummanager/utils.js', [
				'version' => filemtime(__FILE__),
				'relative' => true
			]);

			HTMLHelper::_('script', 'com_quantummanager/quantumviewfiles.js', [
				'version' => filemtime(__FILE__),
				'relative' => true
			]);

			HTMLHelper::_('script', 'com_quantummanager/ds.min.js', [
				'version' => filemtime(__FILE__),
				'relative' => true
			]);

			HTMLHelper::_('script', 'com_quantummanager/lazyload.min.js', [
				'version' => filemtime(__FILE__),
				'relative' => true
			]);

			HTMLHelper::_('script', 'com_quantummanager/contentual.js', [
				'version' => filemtime(__FILE__),
				'relative' => true
			]);

			HTMLHelper::_('script', 'com_quantummanager/notify.js', [
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