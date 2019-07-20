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
$id = rand(111111, 999999);
?>

<div class="quantummanager-module quantumviewfiles-module" data-type="Quantumviewfiles" data-options="hash:<?php echo $displayData['hash'] ?>;directory:<?php echo $displayData['directory'] ?>;onlyfiles:<?php echo $displayData['onlyfiles'] ?>;metafile:<?php echo $displayData['metafile'] ?>">
    <div class="quantumviewfiles-module-heading">
        <div class="breadcumbs"></div>
        <div class="filter-search">
            <label for="filter-search-<?php echo $id ?>" class="uk-form-icon uk-icon" uk-icon="icon: pencil">
                <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" data-svg="search"><circle fill="none" stroke="#000" stroke-width="1.1" cx="9" cy="9" r="7"></circle><path fill="none" stroke="#000" stroke-width="1.1" d="M14,14 L18,18 L14,14 Z"></path></svg>            </label>
            <input id="filter-search-<?php echo $id ?>" type="text" name="search" placeholder="<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_FILTER_NAME'); ?>">
        </div>
    </div>
    <div class="filters"></div>
    <div class="view-wrap">
        <div class="view"></div>

        <?php if($displayData['metafile'] === '1') :?>
        <div class="meta-file hidden">
            <div class="meta-file-list">

            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
    window.QuantumviewfilesLang = {
        'loading': '<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_LOADING'); ?>',
        'empty': '<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_EMPTY'); ?>',
        'buttonBack': '<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_BUTTON_BACK'); ?>',
        'buttonUp': '<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_BUTTON_UP'); ?>',
        'buttonUpload': '<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_BUTTON_UPLOAD'); ?>',
        'buttonCreateDirectory': '<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_BUTTON_CREATE_DIRECTORY'); ?>',
        'buttonEdit': '<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_BUTTON_EDIT'); ?>',
        'buttonDelete': '<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_BUTTON_DELETE'); ?>',
        'buttonTable': '<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_BUTTON_TABLE'); ?>',
        'buttonGrid': '<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_BUTTON_GRID'); ?>',
        'directoryName': '<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_DIRECTORY_NAME'); ?>',
        'directoryNameRequired': '<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_DIRECTORY_NAME_REQUIRED'); ?>',
        'fileName': '<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_FILE_NAME'); ?>',
        'changeGridViews': '<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_CHANGE_GRID_VIEWS'); ?>',
        'metaFileShow': '<?php echo Text::_('COM_QUANTUMMANAGER_FILE_METAINFO_SHOW'); ?>',
        'metaFileHide': '<?php echo Text::_('COM_QUANTUMMANAGER_FILE_METAINFO_HIDE'); ?>',
        'contextReload': '<?php echo Text::_('COM_QUANTUMMANAGER_CONTEXT_RELOAD'); ?>',
        'contextRename': '<?php echo Text::_('COM_QUANTUMMANAGER_CONTEXT_RENAME'); ?>',
        'contextCopyLink': '<?php echo Text::_('COM_QUANTUMMANAGER_CONTEXT_COPY_LINK'); ?>',
        'contextPreviewFile': '<?php echo Text::_('COM_QUANTUMMANAGER_CONTEXT_PREVIEW_FILE'); ?>',
        'contextDelete': '<?php echo Text::_('COM_QUANTUMMANAGER_CONTEXT_DELETE'); ?>',
    };
</script>
