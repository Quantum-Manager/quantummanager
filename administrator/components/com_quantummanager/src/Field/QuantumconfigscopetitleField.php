<?php namespace Joomla\Component\QuantumManager\Administrator\Field;

/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\TextField;
use Joomla\CMS\Language\Text;

class QuantumconfigscopetitleField extends TextField
{

	public $type = 'QuantumconfigScopeTitle';

	public function getInput(): string
	{
		$id          = $this->form->getData()->get('id');
		$this->value = Text::_('COM_QUANTUMMANAGER_SCOPE_' . mb_strtoupper($id));

		return parent::getInput();
	}

}