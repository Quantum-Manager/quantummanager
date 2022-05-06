<?php
/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
extract($displayData);

$scopeEnabled   = [];
$scopeEnabled[] = $scope;

?>

    <div class="quantummanager quantummanager-fieldstandalone <?php echo $cssClass ?>">
		<?php echo $displayData['field'] ?>
		<?php echo $displayData['other'] ?>
    </div>

<?php
$options = [
	'isUserAdmin'  => QuantummanagerHelper::isUserAdmin() ? 'true' : 'false',
	'urlFull'      => $urlFull,
	'urlBase'      => $urlBase,
	'isJoomla4'    => QuantummanagerHelper::isJoomla4() ? '1' : '0',
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
        scopeEnabled: '{$optionsOutput('scopeEnabled')}',
        isJoomla4: '{$optionsOutput('isJoomla4')}'
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