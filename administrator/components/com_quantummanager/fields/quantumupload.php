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
use Joomla\CMS\Uri\Uri;

/**
 * Class JFormFieldQuantumUpload
 */
class JFormFieldQuantumupload extends JFormField
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
				'urlBase' => Uri::root(true),
				'scope' => $this->scope,
				'directory' => $this->directory,
				'dropAreaHidden' => $this->dropAreaHidden,
				'maxsize' => $this->maxsize,
				'cssClass' => $this->cssClass,
				'other' => '',
			]
		);
	}

	public function getInput()
	{
		try {

			$this->__set('standalone', $this->getAttribute('standalone', true));
			$this->__set('cssClass', $this->getAttribute('cssClass', ''));
			$this->__set('maxsize', $this->getAttribute('maxsize', '2'));
			$this->directory = $this->getAttribute('directory', 'images');
			$this->scope = $this->getAttribute('scope', 'images');
			$this->dropAreaHidden = $this->getAttribute('dropAreaHidden', '0');

			JLoader::register('QuantummanagerHelper', JPATH_SITE . '/administrator/components/com_quantummanager/helpers/quantummanager.php');
			QuantummanagerHelper::includeScriptHead();
			QuantummanagerHelper::loadLang();

			HTMLHelper::_('stylesheet', 'com_quantummanager/main.css', [
				'version' => filemtime(__FILE__),
				'relative' => true
			]);

			HTMLHelper::_('stylesheet', 'com_quantummanager/quantumupload.css', [
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

			HTMLHelper::_('script', 'com_quantummanager/utils.js', [
				'version' => filemtime(__FILE__),
				'relative' => true
			]);

			HTMLHelper::_('script', 'com_quantummanager/quantumupload.js', [
				'version' => filemtime(__FILE__),
				'relative' => true
			]);

			$field = parent::getInput();

			if($this->standalone)
			{
				$filemanager = new FileLayout( 'fieldstandalone', JPATH_ROOT . '/administrator/components/com_quantummanager/layouts');
				return $filemanager->render(array_merge($this->getLayoutData(), ['field' => $field]));
			}

			return $field;
		}
		catch (Exception $e) {
			echo $e->getMessage();
		}
	}

}