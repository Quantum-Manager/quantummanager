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

use Joomla\CMS\Language\Text;
extract($displayData);
?>

<div class="quantummanager-module quantumtreecatalogs-module" data-type="Quantumtreecatalogs" data-options="directory:<?php echo $displayData['directory'] ?>">
    <div class="tree-scroll"></div>
</div>

<script type="text/javascript">
    window.QuantumtreecatalogsLang = {
        'confirmDelete': '<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMTREECATALOGS_CONFIRM_DELETE'); ?>'
    }
</script>