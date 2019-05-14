<?php defined('JPATH_PLATFORM') or die;
use Joomla\CMS\Language\Text;
extract($displayData);
?>

<div class="quantummanager-module quantumviewfiles-module" data-type="Quantumviewfiles" data-options="directory:<?php echo $displayData['directory'] ?>;onlyfiles:<?php echo $displayData['onlyfiles'] ?> ">
    <div class="breadcumbs"></div>
    <div class="filters">
        <!--
        <div class="head">
            Сортировать по
        </div>
        <div>
            размеру
        </div>
        <div>
            дате
        </div>
        <div>
            алфавиту
        </div>
        -->
    </div>
    <div class="view"></div>
</div>

<script type="text/javascript">
    window.QuantumviewfilesLang = {
        'loading': '<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_LOADING'); ?>',
        'empty': '<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_EMPTY'); ?>',
        'buttonBack': '<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_BUTTON_BACK'); ?>',
        'buttonUp': '<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_BUTTON_UP'); ?>',
        'buttonCreateDirectory': '<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_BUTTON_CREATE_DIRECTORY'); ?>',
        'buttonEdit': '<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_BUTTON_EDIT'); ?>',
        'buttonDelete': '<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_BUTTON_DELETE'); ?>',
        'buttonTable': '<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_BUTTON_TABLE'); ?>',
        'buttonGrid': '<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_BUTTON_GRID'); ?>',
        'directoryName': '<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_DIRECTORY_NAME'); ?>',
        'changeGridViews': '<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_CHANGE_GRID_VIEWS'); ?>'
    };
</script>
