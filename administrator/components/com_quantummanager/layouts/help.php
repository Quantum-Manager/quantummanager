<?php
/**
 * @package    quantummanager
 *
 * @author     Cymbal <cymbal@delo-design.ru>
 * @copyright  Copyright (C) 2019 "Delo Design". All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://delo-design.ru
 */
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
?>

<div class="quantummanager-help">
	<div class="close"></div>
	<div class="quantummanager-help-content">
		<div>
			<span><?php echo Text::_('COM_QUANTUMMANAGER_HELP_QUESTIONS'); ?></span>
		</div>
		<div>
			<a href="https://github.com/Delo-Design/quantummanager/issues/new" target="_blank"><?php echo Text::_('COM_QUANTUMMANAGER_HELP_QUESTIONS_CREATE'); ?></a>
			<span>|</span>
			<a href="https://github.com/Delo-Design/quantummanager/issues" target="_blank"><?php echo Text::_('COM_QUANTUMMANAGER_HELP_QUESTIONS_SHOW'); ?></a>
		</div>
		<div>
			<span><?php echo Text::_('COM_QUANTUMMANAGER_HELP_AUTHOR'); ?></span>
			<span><a href="tg://resolve?domain=tsymbalmitia">@tsymbalmitia</a> (telegram)</span>
			<span>|</span>
			<span><a href="mailto:quantum@delo-design.ru">quantum@hika.su</a> (email)</span>
		</div>
		<div>
			<button class="btn">Скрыть</button>
		</div>
	</div>
</div>
