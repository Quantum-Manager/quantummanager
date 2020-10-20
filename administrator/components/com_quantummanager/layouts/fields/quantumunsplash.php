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

?>

<div class="quantummanager-module quantumunsplash-module" data-type="Quantumunsplash" data-options="">
    <div class="quantumunsplash-save">
        <div class="loader"><?php echo Text::_('COM_QUANTUMMANAGER_ACTION_SAVEAREA'); ?><span></span><span></span><span></span><span></span></div>
    </div>
    <div class="quantumunsplash-module-container">

        <div class="quantumunsplash-module-header">
            <div>
                <label>
                    <input name="q" type="text" placeholder="<?php echo Text::_('COM_QUANTUMMANAGER_FIELD_LABEL_SEARCH'); ?>">
                </label>
            </div>
            <div class="header-filters">
                <div class="fields"></div>
                <div class="source">
                    <span><?php echo Text::_('COM_QUANTUMMANAGER_QUANTUMUNSPLASH_LICENSE') ?></span> <a href="https://unsplash.com" target="_blank"><span class="quantummanager-icon quantummanager-icon-unsplash"></span></a>
                </div>
            </div>
        </div>

        <div class="quantumunsplash-module-container-search-wrap">
            <div class="quantumunsplash-module-container-search">
                <div class="quantumunsplash-module-search grid"></div>
                <div class="quantumunsplash-module-load-page">
                    <button class="btn"><?php echo Text::_('COM_QUANTUMMANAGER_ACTION_LOAD'); ?></button>
                </div>
            </div>
        </div>

        <button class="quantumunsplash-module-close btn"><span class="quantummanager-icon quantummanager-icon-close"></span> <?php echo Text::_('COM_QUANTUMMANAGER_WINDOW_CLOSE'); ?></button>
    </div>

</div>

<?php
    $langs = json_encode([
        'notFound' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ALERT_NOTFOUND'), ENT_QUOTES),
        'button' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_QUANTUMUNSPLASH_BUTTON'), ENT_QUOTES),
        'buttonLoad' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_LOAD'), ENT_QUOTES),
        'save' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_SAVE'), ENT_QUOTES),
        'close' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_WINDOW_CLOSE'), ENT_QUOTES),
        'search' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_FIELD_LABEL_SEARCH'), ENT_QUOTES),
        'photostock' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_PHOTOSTOCKS'), ENT_QUOTES),
        'searchHelper' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_FIELD_LABEL_SEARCH_HELPER'), ENT_QUOTES),
    ]);

    QuantummanagerHelper::scriptInsertOnPage('quantumUnsplash', <<<EOF
    window.QuantumunsplashLang = {$langs};
EOF
);