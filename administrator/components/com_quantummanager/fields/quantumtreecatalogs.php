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
 * Class JFormFieldQuantumtreecatalogs
 */
class JFormFieldQuantumtreecatalogs extends JFormField
{

	/**
	 * @var string
	 */
	public $type = 'QuantumTreeCatalogs';

	/**
	 * @var string
	 */
	protected $layout = 'quantumtreecatalogs';


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

        if($this->standalone)
        {
            if(file_exists($path . '/single/' . $this->layout . '.php'))
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
				'directory' => $this->directory,
				'cssClass' => $this->cssClass,
			]
		);
	}


	public function getInput()
	{
		try {

			$this->__set('standalone', $this->getAttribute('standalone', true));
			$this->__set('cssClass', $this->getAttribute('cssClass', ''));
			$this->directory = $this->getAttribute('directory', 'images');

			JLoader::register('QuantummanagerHelper', JPATH_SITE . '/administrator/components/com_quantummanager/helpers/quantummanager.php');
            JLoader::register('QuantummanagerLibs', JPATH_SITE . '/administrator/components/com_quantummanager/helpers/quantumlibs.php');

            QuantummanagerLibs::includeScriptHead();
            QuantummanagerLibs::includes([
                'core',
                'utils',
                'alert',
            ]);


			HTMLHelper::_('stylesheet', 'com_quantummanager/quantumtreecatalogs.css', [
				'version' => filemtime(__FILE__),
				'relative' => true
			]);

			HTMLHelper::_('script', 'com_quantummanager/quantumtreecatalogs.js', [
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