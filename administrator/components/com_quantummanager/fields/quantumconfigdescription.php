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
 * Class JFormFieldQuantumtoolbar
 */
class JFormFieldQuantumconfigdescription extends JFormField
{

	/**
	 * @var string
	 * @since version
	 */
	public $type = 'QuantumconfigDescription';

	public function getLabel()
	{
		return '';
	}


	/**
	 *
	 * @return string
	 *
	 * @since version
	 */
	public function getInput()
	{

		HTMLHelper::_('stylesheet', 'com_quantummanager/configfields.css', [
			'version' => filemtime(__FILE__),
			'relative' => true
		]);

		$fromText = $this->getAttribute('fromText', '');

		$html = "<div class='alert alert-info'>" . Text::_($fromText) . "</div>";

		return $html;
	}


}