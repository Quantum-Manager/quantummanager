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
$id = mt_rand(111111, 999999);
$document = \Joomla\CMS\Factory::getDocument();
?>

<div class="quantummanager-module quantumviewfiles-module" data-type="Quantumviewfiles" data-options="hash:<?php echo $displayData['hash'] ?>;directory:<?php echo $displayData['directory'] ?>;onlyfiles:<?php echo $displayData['onlyfiles'] ?>;metafile:<?php echo $displayData['metafile'] ?>;watermark:<?php echo $displayData['watermark'] ?>">
    <div class="quantumviewfiles-module-heading">
        <div class="breadcumbs"></div>
        <div class="filter-search">
            <label for="filter-search-<?php echo $id ?>" class="uk-form-icon uk-icon" uk-icon="icon: pencil">
                <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" data-svg="search"><circle fill="none" stroke="#000" stroke-width="1.1" cx="9" cy="9" r="7"></circle><path fill="none" stroke="#000" stroke-width="1.1" d="M14,14 L18,18 L14,14 Z"></path></svg>            </label>
            <input id="filter-search-<?php echo $id ?>" type="text" name="search" placeholder="<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_FILTER_NAME'); ?>">
        </div>
    </div>
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

<div style="display: none">
	<?php echo file_get_contents(JPATH_ROOT . DIRECTORY_SEPARATOR . '/media/com_quantummanager/images/icons/file.svg') ?>
</div>

<?php
    $mapFileColors = include implode(DIRECTORY_SEPARATOR, [JPATH_ROOT, 'administrator', 'components', 'com_quantummanager', 'layouts', 'mapfilescolors.php']);
?>

<?php
    $css = '';
    $colorDefault = $mapFileColors['default'];
    $css .= 'svg.svg-icon use.main { fill: ' . $colorDefault[0] . ';} ';
    $css .= 'svg.svg-icon use.tail { fill: ' . $colorDefault[1] . ';} ';
    foreach ($mapFileColors as $exs => $color)
    {
        $css .= 'svg.svg-icon.'.$exs.' use.main { fill: ' . $color[0] . ';} ';
        $css .= 'svg.svg-icon.'.$exs.' use.tail { fill: ' . $color[1] . ';} ';
    }
    $document->addStyleDeclaration($css);

?>

<script type="text/javascript">
    window.QuantumviewfilesLang = {
        'ok': "<?php echo Text::_('COM_QUANTUMMANAGER_WINDOW_OK'); ?>",
        'close': "<?php echo Text::_('COM_QUANTUMMANAGER_WINDOW_CLOSE'); ?>",
        'cancel': "<?php echo Text::_('COM_QUANTUMMANAGER_WINDOW_CANCEL'); ?>",
        'loading': "<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_LOADING'); ?>",
        'empty': "<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_EMPTY'); ?>",
        'buttonBack': "<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_BUTTON_BACK'); ?>",
        'buttonUp': "<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_BUTTON_UP'); ?>",
        'buttonUpload': "<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_BUTTON_UPLOAD'); ?>",
        'buttonCreateDirectory': "<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_BUTTON_CREATE_DIRECTORY'); ?>",
        'buttonEdit': "<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_BUTTON_EDIT'); ?>",
        'buttonWatermark': "<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_BUTTON_WATERMARK'); ?>",
        'buttonDelete': "<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_BUTTON_DELETE'); ?>",
        'buttonTable': "<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_BUTTON_TABLE'); ?>",
        'buttonGrid': "<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_BUTTON_GRID'); ?>",
        'directoryName': "<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_DIRECTORY_NAME'); ?>",
        'directoryNameRequired': "<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_DIRECTORY_NAME_REQUIRED'); ?>",
        'fileName': "<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_FILE_NAME'); ?>",
        'changeGridViews': "<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMVIEWFILES_CHANGE_GRID_VIEWS'); ?>",
        'metaFileShow': "<?php echo Text::_('COM_QUANTUMMANAGER_FILE_METAINFO_SHOW'); ?>",
        'metaFileHide': "<?php echo Text::_('COM_QUANTUMMANAGER_FILE_METAINFO_HIDE'); ?>",
        'metaSelectCount': "<?php echo Text::_('COM_QUANTUMMANAGER_FILE_METAINFO_SELECT_COUNT'); ?>",
        'metaSelectSize': "<?php echo Text::_('COM_QUANTUMMANAGER_FILE_METAINFO_SELECT_SIZE'); ?>",
        'contextReload': "<?php echo Text::_('COM_QUANTUMMANAGER_CONTEXT_RELOAD'); ?>",
        'contextRename': "<?php echo Text::_('COM_QUANTUMMANAGER_CONTEXT_RENAME'); ?>",
        'contextCopyLink': "<?php echo Text::_('COM_QUANTUMMANAGER_CONTEXT_COPY_LINK'); ?>",
        'contextPreviewFile': "<?php echo Text::_('COM_QUANTUMMANAGER_CONTEXT_PREVIEW_FILE'); ?>",
        'contextDelete': "<?php echo Text::_('COM_QUANTUMMANAGER_CONTEXT_DELETE'); ?>",
        'help': "<?php echo Text::_('COM_QUANTUMMANAGER_HELP'); ?>",
        'helpText': "<?php echo Text::_('COM_QUANTUMMANAGER_HELP_TEXT'); ?>",
        'helpCopyright': "<?php echo Text::_('COM_QUANTUMMANAGER_HELP_COPYRIGHT'); ?>",
        'helpCopyrightImages': "<?php echo Text::_('COM_QUANTUMMANAGER_HELP_COPYRIGHT_IMAGES'); ?>",
        'helpLove': "<?php echo Text::_('COM_QUANTUMMANAGER_HELP_LOVE'); ?>",
        'helpButtonProductPage': "<?php echo Text::_('COM_QUANTUMMANAGER_HELP_BUTTON_PRODUCT_PAGE'); ?>",
        'helpButtonDocumentation': "<?php echo Text::_('COM_QUANTUMMANAGER_HELP_BUTTON_DOCUMENTATION'); ?>",
        'helpButtonSupport': "<?php echo Text::_('COM_QUANTUMMANAGER_HELP_BUTTON_SUPPORT'); ?>",
        'helpButtonReview': "<?php echo Text::_('COM_QUANTUMMANAGER_HELP_BUTTON_REVIEW'); ?>"
    };
</script>
