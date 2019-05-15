<?php

/**
 * @package    quantummanager
 *
 * @author     Cymbal <cymbal@delo-design.ru>
 * @copyright  Copyright (C) 2019 "Delo Design". All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://delo-design.ru
 */

defined('JPATH_PLATFORM') or die;
extract($displayData);
$buttons = $displayData['buttons'];
$buttonsBun = $displayData['buttonsBun'];

if(!is_array($buttons))
{
    $buttons = [];
}

if(!is_array($buttonsBun))
{
	$buttonsBun = [];
}

?>

<div class="quantummanager-module quantumtoolbar-module" data-type="Quantumtoolbar" data-options="buttons:<?php echo implode(',', $buttons) ?>;buttonsBun:<?php echo implode(',', $buttonsBun) ?>;">
	<div class="left"></div>
	<div class="right"></div>
</div>