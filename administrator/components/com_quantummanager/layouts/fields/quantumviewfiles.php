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

<div class="quantummanager-module quantumviewfiles-module" data-type="Quantumviewfiles" data-options="hash:<?php echo $displayData['hash'] ?>;directory:<?php echo $displayData['directory'] ?>;onlyfiles:<?php echo $displayData['onlyfiles'] ?>;metafile:<?php echo $displayData['metafile'] ?>;watermark:<?php echo $displayData['watermark'] ?>;help:<?php echo $displayData['help'] ?>">
    <div class="quantumviewfiles-module-heading">
        <ul class="breadcumbs"></ul>
        <div class="filter-search">
            <div class="input-wrapper">
                <label for="filter-search-<?php echo $id ?>" class="uk-form-icon uk-icon" uk-icon="icon: pencil">
                    <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" data-svg="search"><circle fill="none" stroke="#000" stroke-width="1.1" cx="9" cy="9" r="7"></circle><path fill="none" stroke="#000" stroke-width="1.1" d="M14,14 L18,18 L14,14 Z"></path></svg>            </label>
                <input id="filter-search-<?php echo $id ?>" type="text" name="searchqunatumamanger" placeholder="<?php echo Text::_('COM_QUANTUMMANAGER_FIELD_LABEL_FILTER_NAME'); ?>">
                <span class="bottom"></span>
                <span class="right"></span>
                <span class="top"></span>
                <span class="left"></span>
            </div>
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
        'ok': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_WINDOW_OK'), ENT_QUOTES); ?>",
        'close': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_WINDOW_CLOSE'), ENT_QUOTES); ?>",
        'copied': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_COPIED'), ENT_QUOTES); ?>",
        'cancel': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_WINDOW_CANCEL'), ENT_QUOTES); ?>",
        'loading': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_LOADING'), ENT_QUOTES); ?>",
        'empty': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_QUANTUMVIEWFILES_EMPTY'), ENT_QUOTES); ?>",
        'buttonBack': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_BACK'), ENT_QUOTES); ?>",
        'buttonUp': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_UP'), ENT_QUOTES); ?>",
        'buttonUpload': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_UPLOADING'), ENT_QUOTES); ?>",
        'buttonCreateDirectory': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_CREATE'), ENT_QUOTES); ?>",
        'buttonEdit': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_EDIT'), ENT_QUOTES); ?>",
        'buttonWatermark': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_WATERMARK'), ENT_QUOTES); ?>",
        'buttonPaste': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_PASTE'), ENT_QUOTES); ?>",
        'buttonCopy': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_COPY'), ENT_QUOTES); ?>",
        'buttonCut': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_CUT'), ENT_QUOTES); ?>",
        'buttonDelete': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_DELETE'), ENT_QUOTES); ?>",
        'buttonTable': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_TABLE'), ENT_QUOTES); ?>",
        'buttonGrid': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_GRID'), ENT_QUOTES); ?>",
        'directoryName': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ALERT_DIRECTORY_NAME'), ENT_QUOTES); ?>",
        'directoryNameRequired': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ALERT_DIRECTORY_NAME_REQUIRED'), ENT_QUOTES); ?>",
        'fileName': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ALERT_FILE_NAME'), ENT_QUOTES); ?>",
        'changeGridViews': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_TOOLTIP_CHANGE_GRID_VIEWS'), ENT_QUOTES); ?>",
        'metaFileShow': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_METAINFO_SHOW'), ENT_QUOTES); ?>",
        'metaFileHide': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_METAINFO_HIDE'), ENT_QUOTES); ?>",
        'metaSelectCount': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_METAINFO_SELECT_COUNT'), ENT_QUOTES); ?>",
        'metaSelectObjectCount': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_METAINFO_SELECT_OBJECT_COUNT'), ENT_QUOTES); ?>",
        'metaSelectSize': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_METAINFO_SELECT_SIZE'), ENT_QUOTES); ?>",
        'contextReload': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_RELOAD'), ENT_QUOTES); ?>",
        'contextFolderCreate': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_FOLDER_CREATE'), ENT_QUOTES); ?>",
        'contextPaste': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_PASTE'), ENT_QUOTES); ?>",
        'contextRename': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_RENAME'), ENT_QUOTES); ?>",
        'contextCopyLink': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_COPY_LINK'), ENT_QUOTES); ?>",
        'contextPreviewFile': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_PREVIEW_FILE'), ENT_QUOTES); ?>",
        'contextSomeDelete': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_SOME_DELETE'), ENT_QUOTES); ?>",
        'contextDelete': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_DELETE'), ENT_QUOTES); ?>",
        'help': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_HELP'), ENT_QUOTES); ?>",
        'helpText': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_HELP_TEXT'), ENT_QUOTES); ?>",
        'helpCopyright': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_HELP_COPYRIGHT'), ENT_QUOTES); ?>",
        'helpCopyrightImages': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_HELP_COPYRIGHT_IMAGES'), ENT_QUOTES); ?>",
        'helpLove': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_HELP_LOVE'), ENT_QUOTES); ?>",
        'helpButtonProductPage': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_HELP_BUTTON_PRODUCT_PAGE'), ENT_QUOTES); ?>",
        'helpButtonDocumentation': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_HELP_BUTTON_DOCUMENTATION'), ENT_QUOTES); ?>",
        'helpButtonSupport': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_HELP_BUTTON_SUPPORT'), ENT_QUOTES); ?>",
        'helpButtonReview': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_HELP_BUTTON_REVIEW'), ENT_QUOTES); ?>"
    };
</script>
