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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

class QuantumconfigpathField extends TextField
{

	public $type = 'QuantumConfigpath';

	protected string $prefix = '';

	public function getInput(): string
	{

		HTMLHelper::_('stylesheet', 'com_quantummanager/configfields.css', [
			'version'  => filemtime(__FILE__),
			'relative' => true
		]);

		$this->__set('prefix', $this->getAttribute('prefix', Uri::root()));

		return "<div class='quantumconfig-quantumconfigpath-inputs'><span>" . $this->prefix . "</span>" . parent::getInput() . "</div>";
	}

}