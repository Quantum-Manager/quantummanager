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

JFormHelper::loadFieldClass('text');

/**
 * Class JFormFieldQuantumconfigscopetitle
 */
class JFormFieldQuantumconfigscopetitle extends JFormFieldText
{

	/**
	 * @var string
	 * @since version
	 */
	public $type = 'QuantumconfigScopeTitle';

	/**
	 *
	 * @return string
	 *
	 * @since version
	 */
	public function getInput()
	{
		$id = $this->form->getData()->get('id');
		$this->value = Text::_('COM_QUANTUMMANAGER_SCOPE_' . mb_strtoupper($id));
		return parent::getInput();
	}


}