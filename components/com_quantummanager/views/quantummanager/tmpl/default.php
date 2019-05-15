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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;

HTMLHelper::_('script', 'com_quantummanager/script.js', array('version' => 'auto', 'relative' => true));
HTMLHelper::_('stylesheet', 'com_quantummanager/style.css', array('version' => 'auto', 'relative' => true));

$layout = new FileLayout('quantummanager.page');
$data = array();
$data['text'] = 'Hello Joomla!';
echo $layout->render($data);
