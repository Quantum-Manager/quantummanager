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

<div class="quantummanager-module quantumpexels-module" data-type="Quantumpexels" data-options="">
    <div class="quantumpexels-save">
        <div class="loader"><?php echo Text::_('COM_QUANTUMMANAGER_ACTION_SAVEAREA'); ?><span></span><span></span><span></span><span></span></div>
    </div>
    <div class="quantumpexels-module-container">

        <div class="quantumpexels-module-header">
            <div>
                <label>
                    <input name="q" type="text" placeholder="<?php echo Text::_('COM_QUANTUMMANAGER_FIELD_LABEL_SEARCH'); ?>">
                </label>
            </div>
            <div class="header-filters">
                <div class="fields">
                    <?php
                    $locals = [
                        'en-US',
                        'pt-BR',
                        'es-ES',
                        'ca-ES',
                        'de-DE',
                        'it-IT',
                        'fr-FR',
                        'sv-SE',
                        'id-ID',
                        'pl-PL',
                        'ja-JP',
                        'zh-TW',
                        'zh-CN',
                        'ko-KR',
                        'th-TH',
                        'nl-NL',
                        'hu-HU',
                        'vi-VN',
                        'cs-CZ',
                        'da-DK',
                        'fi-FI',
                        'uk-UA',
                        'el-GR',
                        'ro-RO',
                        'nb-NO',
                        'sk-SK',
                        'tr-TR',
                        'ru-RU'
                    ];
                    $lang = \Joomla\CMS\Factory::getLanguage();
                    $current_locale = 'en-US';

                    if(in_array($lang->getTag(), $locals))
                    {
                        $current_locale = $lang->getTag();
                    }

                    ?>
                    <div class="quantummanager-dropdown filter-field" data-name="locale" data-value="<?php echo $current_locale ?>">
                        <div class="quantummanager-dropdown-title"><?php echo Text::_('COM_QUANTUMMANAGER_QUANTUMPEXELS_FILTERS_LOCAL') ?></div>
                        <div class="quantummanager-dropdown-content">
                            <ul>
                                <?php foreach ($locals as $local) : ?>
                                <li data-value="<?php echo $local ?>"><?php echo $local ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    <div class="quantummanager-dropdown filter-field" data-name="size" data-value="optimal" data-disabled>
                        <div class="quantummanager-dropdown-title"><?php echo Text::_('COM_QUANTUMMANAGER_QUANTUMPEXELS_FILTERS_SIZE') ?></div>
                        <div class="quantummanager-dropdown-content">
                            <ul>
                                <li data-value="tiny"><?php echo Text::_('COM_QUANTUMMANAGER_QUANTUMPEXELS_FILTERS_SIZE_TINY') ?></li>
                                <li data-value="landscape"><?php echo Text::_('COM_QUANTUMMANAGER_QUANTUMPEXELS_FILTERS_SIZE_LANDSCAPE') ?></li>
                                <li data-value="portrait"><?php echo Text::_('COM_QUANTUMMANAGER_QUANTUMPEXELS_FILTERS_SIZE_PORTRAIT') ?></li>
                                <li data-value="small"><?php echo Text::_('COM_QUANTUMMANAGER_QUANTUMPEXELS_FILTERS_SIZE_SMALL') ?></li>
                                <li data-value="medium"><?php echo Text::_('COM_QUANTUMMANAGER_QUANTUMPEXELS_FILTERS_SIZE_MEDIUM') ?></li>
                                <li data-value="large"><?php echo Text::_('COM_QUANTUMMANAGER_QUANTUMPEXELS_FILTERS_SIZE_LARGE') ?></li>
                                <li data-value="large2x"><?php echo Text::_('COM_QUANTUMMANAGER_QUANTUMPEXELS_FILTERS_SIZE_LARGE2X') ?></li>
                                <li data-value="optimal"><?php echo Text::_('COM_QUANTUMMANAGER_QUANTUMPEXELS_FILTERS_SIZE_OPTIMAL') ?></li>
                                <li data-value="original"><?php echo Text::_('COM_QUANTUMMANAGER_QUANTUMPEXELS_FILTERS_SIZE_ORIGINAL') ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="source">
                    <span><?php echo Text::_('COM_QUANTUMMANAGER_QUANTUMPEXELS_LICENSE') ?></span> <a href="https://pexels.com" target="_blank"><span class="quantummanager-icon quantummanager-icon-pexels"></span></a>
                </div>
            </div>
        </div>

        <div class="quantumpexels-module-container-search-wrap">
            <div class="quantumpexels-module-container-search">
                <div class="quantumpexels-module-search grid"></div>
                <div class="quantumpexels-module-load-page">
                    <button class="btn"><?php echo Text::_('COM_QUANTUMMANAGER_ACTION_LOAD'); ?></button>
                </div>
            </div>
        </div>

        <button class="quantumpexels-module-close btn"><span class="quantummanager-icon quantummanager-icon-close"></span> <?php echo Text::_('COM_QUANTUMMANAGER_WINDOW_CLOSE'); ?></button>
    </div>

</div>

<script type="text/javascript">
    window.QuantumpexelsLang = {
        'notFound': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ALERT_NOTFOUND'), ENT_QUOTES); ?>",
        'button': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_QUANTUMPEXELS_BUTTON'), ENT_QUOTES); ?>",
        'buttonLoad': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_LOAD'), ENT_QUOTES); ?>",
        'save': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_SAVE'), ENT_QUOTES); ?>",
        'close': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_WINDOW_CLOSE'), ENT_QUOTES); ?>",
        'search': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_FIELD_LABEL_SEARCH'), ENT_QUOTES); ?>",
        'photostock': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_PHOTOSTOCKS'), ENT_QUOTES); ?>",
        'searchHelper': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_FIELD_LABEL_SEARCH_HELPER'), ENT_QUOTES); ?>",
    };
</script>