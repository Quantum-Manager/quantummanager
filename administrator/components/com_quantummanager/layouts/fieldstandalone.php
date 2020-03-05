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

<div class="quantummanager quantummanager-fieldstandalone">
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
        'ok': "<?php echo Text::_('COM_QUANTUMMANAGER_WINDOW_OK'); ?>",
        'close': "<?php echo Text::_('COM_QUANTUMMANAGER_WINDOW_CLOSE'); ?>",
        'cancel': "<?php echo Text::_('COM_QUANTUMMANAGER_WINDOW_CANCEL'); ?>"
    };
</script>