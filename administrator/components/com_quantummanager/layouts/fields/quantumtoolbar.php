<?php defined('JPATH_PLATFORM') or die;
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