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
 * Class JFormFieldQuantumcodemirror
 */
class JFormFieldQuantumcodemirror extends JFormField
{

	/**
	 * @var string
	 */
	public $type = 'Quantumcodemirror';

	/**
	 * @var string
	 */
	protected $layout = 'quantumcodemirror';


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
		];
	}

	/**
	 * @return array
	 */
	protected function getLayoutData()
	{
		return array_merge(parent::getLayoutData(),
			[

			]
		);
	}


	public function getInput()
	{
		try {

			$doc = Factory::getDocument();
			$this->__set('standalone', $this->getAttribute('standalone', true));

			HTMLHelper::_('stylesheet', 'com_quantummanager/main.css', [
				'version' => filemtime(__FILE__),
				'relative' => true
			]);

			HTMLHelper::_('stylesheet', 'com_quantummanager/quantumcodemirror.css', [
				'version' => filemtime(__FILE__),
				'relative' => true
			]);

			HTMLHelper::_('script', 'com_quantummanager/main.js', [
				'version' => filemtime(__FILE__),
				'relative' => true
			]);


			HTMLHelper::_('script', 'com_quantummanager/utils.js', [
				'version' => filemtime(__FILE__),
				'relative' => true
			]);

			HTMLHelper::_('script', 'com_quantummanager/quantumcodemirror.js', [
				'version' => filemtime(__FILE__),
				'relative' => true
			]);


			$doc->addStyleSheet('/media/editors/codemirror/lib/codemirror.min.css');
			$doc->addStyleSheet('/media/editors/codemirror/theme/elegant.css');
			$doc->addScript('/media/editors/codemirror/lib/codemirror.min.js');
			$doc->addScript('/media/editors/codemirror/mode/javascript/javascript.min.js');


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