<?php
/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

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
        urlBase: '<?php echo $urlBase ?>',
        scopeEnabled: '<?php echo implode(',', $scopeEnabled) ?>',
    }
</script>