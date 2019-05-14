<?php defined('JPATH_PLATFORM') or die;

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