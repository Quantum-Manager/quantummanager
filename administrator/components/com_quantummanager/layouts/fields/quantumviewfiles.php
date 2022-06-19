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
$options = [
    'hash:' . $hash,
    'directory:' . $directory,
    'onlyfiles:' . $onlyfiles,
    'metafile:' . $metafile,
    'watermark:' . $watermark,
    'help:' . $help,
    'previewsfolder:' . $previewsfolder,
    'previewsfolderopen:' . $previewsfolderopen,
    'folderdblclick:' . $folderdblclick,
];

?>

<div class="quantummanager-module quantumviewfiles-module" data-type="Quantumviewfiles" data-options="<?php echo implode(';', $options)?>">
    <div class="quantumviewfiles-module-heading  quantummanager-backgrounds-muted ">
        <ul class="breadcumbs"></ul>
        <div class="filter-search">
            <div class="input-wrapper">
                <label for="filter-search-<?php echo $id ?>" class="uk-form-icon uk-icon" uk-icon="icon: pencil">
                    <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" data-svg="search"><circle fill="none" stroke="#000" stroke-width="1.1" cx="9" cy="9" r="7"></circle><path fill="none" stroke="#000" stroke-width="1.1" d="M14,14 L18,18 L14,14 Z"></path></svg></label>
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
        <div class="meta-file quantummanager-backgrounds-muted" draggable="false">
            <div class="meta-file-list"></div>
        </div>
        <?php endif; ?>
    </div>
</div>

<div style="display: none">
	<?php echo file_get_contents(JPATH_ROOT . DIRECTORY_SEPARATOR . '/media/com_quantummanager/images/icons/file.svg') ?>
</div>

<?php
    $mapFileColors = include implode(DIRECTORY_SEPARATOR, [JPATH_ROOT, 'administrator', 'components', 'com_quantummanager', 'layouts', 'mapfilescolors.php']);
    $mapFileColorsReplace = QuantummanagerHelper::getParamsComponentValue('colorsfiles', []);
    foreach ($mapFileColorsReplace as $value)
    {
        $mapFileColors[$value->ext] = [$value->main, $value->fold];
    }
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


    $previewslist = json_encode($previewslist);
    $langs = json_encode([
        'ok' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_WINDOW_OK'), ENT_QUOTES),
        'close' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_WINDOW_CLOSE'), ENT_QUOTES),
        'copied' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_COPIED'), ENT_QUOTES),
        'cancel' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_WINDOW_CANCEL'), ENT_QUOTES),
        'loading' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_LOADING'), ENT_QUOTES),
        'empty' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_QUANTUMVIEWFILES_EMPTY'), ENT_QUOTES),
        'error' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_QUANTUMVIEWFILES_ERROR'), ENT_QUOTES),
        'buttonBack'=> htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_BACK'), ENT_QUOTES),
        'buttonUp' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_UP'), ENT_QUOTES),
        'buttonUpload' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_UPLOADING'), ENT_QUOTES),
        'buttonCreateDirectory' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_CREATE'), ENT_QUOTES),
        'buttonEdit' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_EDIT'), ENT_QUOTES),
        'buttonWatermark' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_WATERMARK'), ENT_QUOTES),
        'buttonPaste' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_PASTE'), ENT_QUOTES),
        'buttonCopy' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_COPY'), ENT_QUOTES),
        'buttonSelectAll' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_SELECT_ALL'), ENT_QUOTES),
        'buttonCut' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_CUT'), ENT_QUOTES),
        'buttonDuplicate' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_DUPLICATE'), ENT_QUOTES),
        'buttonDelete' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_DELETE'), ENT_QUOTES),
        'buttonSortNone' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_SORT_NONE'), ENT_QUOTES),
        'buttonSortName' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_SORT_NAME'), ENT_QUOTES),
        'buttonSortSize' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_SORT_SIZE'), ENT_QUOTES),
        'buttonSortCreated' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_SORT_CREATED'), ENT_QUOTES),
        'buttonSortUpdated' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_SORT_UPDATED'), ENT_QUOTES),
        'buttonSortAsc' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_SORT_ASC'), ENT_QUOTES),
        'buttonSortDesc' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_SORT_DESC'), ENT_QUOTES),
        'buttonTable' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_TABLE'), ENT_QUOTES),
        'buttonGrid' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_GRID'), ENT_QUOTES),
        'buttonPreviews' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_PREVIEWS'), ENT_QUOTES),
        'directoryName' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ALERT_DIRECTORY_NAME'), ENT_QUOTES),
        'directoryNameRequired' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ALERT_DIRECTORY_NAME_REQUIRED'), ENT_QUOTES),
        'fileName' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ALERT_FILE_NAME'), ENT_QUOTES),
        'changeGridViews' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_TOOLTIP_CHANGE_GRID_VIEWS'), ENT_QUOTES),
        'metaFileShow' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_METAINFO_SHOW'), ENT_QUOTES),
        'metaFileHide' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_METAINFO_HIDE'), ENT_QUOTES),
        'metaSelectCount' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_METAINFO_SELECT_COUNT'), ENT_QUOTES),
        'metaSelectObjectCount' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_METAINFO_SELECT_OBJECT_COUNT'), ENT_QUOTES),
        'metaSelectSize' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_METAINFO_SELECT_SIZE'), ENT_QUOTES),
        'contextReload' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_RELOAD'), ENT_QUOTES),
        'contextFolderCreate' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_FOLDER_CREATE'), ENT_QUOTES),
        'contextPaste' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_PASTE'), ENT_QUOTES),
        'contextRename' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_RENAME'), ENT_QUOTES),
        'contextCopyLink' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_COPY_LINK'), ENT_QUOTES),
        'contextPreviewFile' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_PREVIEW_FILE'), ENT_QUOTES),
        'contextSomeDelete' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_SOME_DELETE'), ENT_QUOTES),
        'contextDelete' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_DELETE'), ENT_QUOTES),
        'help' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_HELP'), ENT_QUOTES),
        'helpText' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_HELP_TEXT'), ENT_QUOTES),
        'helpCopyright' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_HELP_COPYRIGHT'), ENT_QUOTES),
        'helpCopyrightImages' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_HELP_COPYRIGHT_IMAGES'), ENT_QUOTES),
        'helpLove' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_HELP_LOVE'), ENT_QUOTES),
        'helpButtonProductPage' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_HELP_BUTTON_PRODUCT_PAGE'), ENT_QUOTES),
        'helpButtonDocumentation' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_HELP_BUTTON_DOCUMENTATION'), ENT_QUOTES),
        'helpButtonLocalizations' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_HELP_BUTTON_LOCALIZATIONS'), ENT_QUOTES),
        'helpButtonSupport' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_HELP_BUTTON_SUPPORT'), ENT_QUOTES),
        'helpButtonReview' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_HELP_BUTTON_REVIEW'), ENT_QUOTES),
        'helpHotkeys' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_HOTKEYS'), ENT_QUOTES),
        'helpHotkeysCopy' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_HOTKEYS_COPY'), ENT_QUOTES),
        'helpHotkeysCut' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_HOTKEYS_CUT'), ENT_QUOTES),
        'helpHotkeysPaste' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_HOTKEYS_PASTE'), ENT_QUOTES),
        'helpHotkeysRename' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_HOTKEYS_RENAME'), ENT_QUOTES),
        'helpHotkeysDelete' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_HOTKEYS_DELETE'), ENT_QUOTES),
        'helpHotkeysRefresh' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_HOTKEYS_REFRESH'), ENT_QUOTES),
        'helpHotkeysBack' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_HOTKEYS_BACK'), ENT_QUOTES),
    ]);

    QuantummanagerHelper::scriptInsertOnPage('quantumViewfiles', <<<EOF
    window.QuantumviewfilesVerison = '{$quantumVersion}';
    window.QuantumviewfilesPreviews = {$previewslist};
    window.QuantumviewfilesLang = $langs;
EOF
);