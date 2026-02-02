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
use Joomla\Component\QuantumManager\Administrator\Helper\QuantummanagerHelper;

extract($displayData);
$buttons = $displayData['buttons'];
$buttonsBun = $displayData['buttonsBun'];
$cssClass = $displayData['cssClass'];

if(!is_array($buttons))
{
    $buttons = [];
}

if(!is_array($buttonsBun))
{
	$buttonsBun = [];
}

?>

<div class="quantummanager-module quantummanager-backgrounds-muted quantumtoolbar-module <?php echo $cssClass ?>" data-type="Quantumtoolbar" data-options="buttons:<?php echo implode(',', $buttons) ?>;buttonsBun:<?php echo implode(',', $buttonsBun) ?>;">
	<div class="left"></div>
	<div class="center"></div>
	<div class="right"></div>
</div>

<?php

$langs = json_encode([
	'buttonOptions' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_OPTIONS'), ENT_QUOTES),
]);

QuantummanagerHelper::scriptInsertOnPage('quantumToolbar', <<<EOF
    window.QuantumtoolbarLang = $langs;
EOF
);
