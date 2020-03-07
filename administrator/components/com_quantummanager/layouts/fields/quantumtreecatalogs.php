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
extract($displayData);
$cssClass = $displayData['cssClass'];

?>

<div class="quantummanager-module quantumtreecatalogs-module <?php echo $cssClass ?>" data-type="Quantumtreecatalogs" data-options="directory:<?php echo $displayData['directory'] ?>">
    <div class="tree-scroll"></div>
</div>

<script type="text/javascript">
    window.QuantumtreecatalogsLang = {
        'confirmDelete': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ALERT_CONFIRM_DELETE'), ENT_QUOTES); ?>"
    }
</script>