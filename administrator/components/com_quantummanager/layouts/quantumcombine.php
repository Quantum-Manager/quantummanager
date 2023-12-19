<?php
/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

use Joomla\CMS\Language\Text;
use Joomla\Component\QuantumManager\Administrator\Helper\QuantummanagerHelper;

defined('_JEXEC') or die;
extract($displayData);

$scopeEnabled = [];
foreach ($scopes as $scope)
{
	$scopeEnabled[] = $scope->id;
}

?>

<div class="quantummanager <?php echo $cssClass ?>">

	<?php if (!empty($displayData['top'])) : ?>
        <div class="quantummanager-top">
			<?php echo $displayData['top'] ?>
        </div>
	<?php endif; ?>

    <div class="quantummanager-container">

		<?php if (!empty($displayData['container-top'])) : ?>
            <div class="quantummanager-container-top">
				<?php echo $displayData['container-top'] ?>
            </div>
		<?php endif; ?>

		<?php if (!empty($displayData['container-left'])) : ?>
            <div class="quantummanager-left">
				<?php echo $displayData['container-left'] ?>
                <div class="quantummanager-left-toggle"></div>
            </div>
		<?php endif; ?>

        <div class="quantummanager-center">
            <div class="quantummanager-center-top">
				<?php if (!empty($displayData['container-center-top'])) : ?>
					<?php echo $displayData['container-center-top'] ?>
				<?php endif; ?>
            </div>

            <div class="quantummanager-center-center">
				<?php if (!empty($displayData['container-center-center'])) : ?>
					<?php echo $displayData['container-center-center'] ?>
				<?php endif; ?>
            </div>

        </div>

		<?php if (!empty($displayData['container-right'])) : ?>
            <div class="quantummanager-right">
				<?php echo $displayData['container-right'] ?>
                <div class="quantummanager-right-toggle"></div>
            </div>
		<?php endif; ?>

		<?php if (!empty($displayData['container-bottom'])) : ?>
            <div class="quantummanager-container-bottom">
				<?php echo $displayData['container-bottom'] ?>
            </div>
		<?php endif; ?>

    </div>

	<?php if (!empty($displayData['bottom'])) : ?>
        <div class="quantummanager-bottom">
			<?php echo $displayData['bottom'] ?>
        </div>
	<?php endif; ?>

	<?php if (!empty($displayData['container-bottom-fixed'])) : ?>
        <div class="quantummanager-container-bottom-fixed">
			<?php echo $displayData['container-bottom-fixed'] ?>
        </div>
	<?php endif; ?>

</div>

<?php
$options = [
	'isUserAdmin'  => QuantummanagerHelper::isUserAdmin() ? 'true' : 'false',
	'urlFull'      => $urlFull,
	'urlBase'      => $urlBase,
	'urlRoot'      => $urlRoot,
	'urlMedia'     => $urlMedia,
	'bufferPaste'  => (string) QuantummanagerHelper::getParamsComponentValue('bufferpaste', 0),
	'scopeEnabled' => implode(',', $scopeEnabled),
	'alertBigData' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ALERT_BIG_IMAGE'), ENT_QUOTES),
	'ok'           => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_WINDOW_OK'), ENT_QUOTES),
	'close'        => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_WINDOW_CLOSE'), ENT_QUOTES),
	'copied'       => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_COPIED'), ENT_QUOTES),
	'cancel'       => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_WINDOW_CANCEL'), ENT_QUOTES),
];

$optionsOutput = static function ($name) use (&$options) {
	return $options[$name];
};

QuantummanagerHelper::scriptInsertOnPage('quantumSettings', <<<EOF
window.QuantumSettings = {
        isUserAdmin: {$optionsOutput('isUserAdmin')},
        urlFull: '{$optionsOutput('urlFull')}',
        urlBase: '{$optionsOutput('urlBase')}',
        urlRoot: '{$optionsOutput('urlRoot')}',
        urlMedia: '{$optionsOutput('urlMedia')}',
        scopeEnabled: '{$optionsOutput('scopeEnabled')}',
        bufferPaste: '{$optionsOutput('bufferPaste')}'
    };

    window.QuantumLang = {
        'alertBigData': "{$optionsOutput('alertBigData')}",
        'ok': "{$optionsOutput('ok')}",
        'close': "{$optionsOutput('close')}",
        'copied': "{$optionsOutput('copied')}",
        'cancel': "{$optionsOutput('cancel')}"
    };
EOF
);

?>
