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

$scopeEnabled = [];
$scopeEnabled[] = $scope;

?>

<div class="quantummanager quantummanager-fieldstandalone <?php echo $cssClass ?>">
	<?php echo $displayData['field'] ?>
	<?php echo $displayData['other'] ?>
</div>

<script type="text/javascript">
    window.QuantumSettings = {
        urlFull: '<?php echo $urlFull ?>',
        urlBase: '<?php echo $urlBase ?>',
        scopeEnabled: '<?php echo implode(',', $scopeEnabled) ?>',
    };

    window.QuantumLang = {
        'alertBigData': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ALERT_BIG_IMAGE'), ENT_QUOTES); ?>",
        'ok': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_WINDOW_OK'), ENT_QUOTES); ?>",
        'close': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_WINDOW_CLOSE'), ENT_QUOTES); ?>",
        'copied': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_COPIED'), ENT_QUOTES); ?>",
        'cancel': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_WINDOW_CANCEL'), ENT_QUOTES); ?>"
    };
</script>