<?php
/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
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
