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

<div class="quantummanager-module quantumpixabay-module" data-type="Quantumpixabay" data-options="">
    <div class="quantumpixabay-save">
        <div class="loader"><?php echo Text::_('COM_QUANTUMMANAGER_ACTION_SAVEAREA'); ?><span></span><span></span><span></span><span></span></div>
    </div>
    <div class="quantumpixabay-module-container">

        <div class="quantumpixabay-module-header">
            <div>
                <label>
                    <input name="q" type="text" placeholder="<?php echo Text::_('COM_QUANTUMMANAGER_FIELD_LABEL_SEARCH'); ?>">
                </label>
            </div>
            <div class="header-filters">
                <div class="fields">
                    <div class="quantummanager-dropdown filter-field" data-name="image_type" data-value="all">
                        <div class="quantummanager-dropdown-title"><?php echo Text::_('COM_QUANTUMMANAGER_QUANTUMPIXABAY_FILTERS_IMAGE_TYPE') ?></div>
                        <div class="quantummanager-dropdown-content">
                            <ul>
                                <li data-value="all"><?php echo Text::_('COM_QUANTUMMANAGER_ACTION_ALL') ?></li>
                                <li data-value="photo"><?php echo Text::_('COM_QUANTUMMANAGER_QUANTUMPIXABAY_FILTERS_IMAGE_TYPE_PHOTO') ?></li>
                                <li data-value="illustration"><?php echo Text::_('COM_QUANTUMMANAGER_QUANTUMPIXABAY_FILTERS_IMAGE_TYPE_ILLUSTRATION') ?></li>
                                <li data-value="vector"><?php echo Text::_('COM_QUANTUMMANAGER_QUANTUMPIXABAY_FILTERS_IMAGE_TYPE_VECTOR') ?></li>
                            </ul>
                        </div>
                    </div>
                    <div class="quantummanager-dropdown filter-field" data-name="colors" data-value="">
                        <div class="quantummanager-dropdown-title"><?php echo Text::_('COM_QUANTUMMANAGER_QUANTUMPIXABAY_FILTERS_COLORS') ?></div>
                        <div class="quantummanager-dropdown-content">
                            <ul>
                                <li class="color-all" data-value=""><span class="color-all"></span></li>
                                <li class="color-grayscale" data-value="grayscale" style="background-color: #808080"><span style="background: #808080"></span></li>
                                <li class="color-transparent" data-value="transparent" style="border: solid 1px #000"><span style="border: solid 1px #000"></span></li>
                                <li class="color-red" data-value="red" style="background: red"><span style="background: red"></span></li>
                                <li class="color-orange" data-value="orange" style="background: orange"><span style="background: orange"></span></li>
                                <li class="color-yellow" data-value="yellow" style="background: yellow"><span style="background: yellow"></span></li>
                                <li class="color-green" data-value="green" style="background: green"><span style="background: green"></span></li>
                                <li class="color-turquoise" data-value="turquoise" style="background: turquoise"><span style="background: turquoise"></span></li>
                                <li class="color-blue" data-value="blue" style="background: blue"><span style="background: blue"></span></li>
                                <li class="color-lilac" data-value="lilac" style="background: #c8a2c8"><span style="background: #c8a2c8"></span></li>
                                <li class="color-pink" data-value="pink" style="background: pink"><span style="background: pink"></span></li>
                                <li class="color-white" data-value="white" style="background: white"><span style="background: white"></span></li>
                                <li class="color-gray" data-value="gray" style="background: gray"><span style="background: gray"></span></li>
                                <li class="color-black" data-value="black" style="background: black"><span style="background: black"></span></li>
                                <li class="color-brown" data-value="brown" style="background: brown"><span style="background: brown"></span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="quantummanager-dropdown filter-field" data-name="orientation" data-value="all">
                        <div class="quantummanager-dropdown-title"><?php echo Text::_('COM_QUANTUMMANAGER_QUANTUMPIXABAY_FILTERS_ORIENTATION') ?></div>
                        <div class="quantummanager-dropdown-content">
                            <ul>
                                <li data-value="all"><?php echo Text::_('COM_QUANTUMMANAGER_ACTION_ALL') ?></li>
                                <li data-value="horizontal"><?php echo Text::_('COM_QUANTUMMANAGER_QUANTUMPIXABAY_FILTERS_ORIENTATION_HORIZONTAL') ?></li>
                                <li data-value="vertical"><?php echo Text::_('COM_QUANTUMMANAGER_QUANTUMPIXABAY_FILTERS_ORIENTATION_VERTICAL') ?></li>
                            </ul>
                        </div>
                    </div>
                    <div class="quantummanager-dropdown filter-field" data-name="size" data-value="original" data-disabled>
                        <div class="quantummanager-dropdown-title"><?php echo Text::_('COM_QUANTUMMANAGER_QUANTUMPIXABAY_FILTERS_SIZE') ?></div>
                        <div class="quantummanager-dropdown-content">
                            <ul>
                                <li data-value="small"><?php echo Text::_('COM_QUANTUMMANAGER_QUANTUMPIXABAY_FILTERS_SIZE_SMALL') ?></li>
                                <li data-value="medium"><?php echo Text::_('COM_QUANTUMMANAGER_QUANTUMPIXABAY_FILTERS_SIZE_MEDIUM') ?></li>
                                <li data-value="large"><?php echo Text::_('COM_QUANTUMMANAGER_QUANTUMPIXABAY_FILTERS_SIZE_LARGE') ?></li>
                                <li data-value="original"><?php echo Text::_('COM_QUANTUMMANAGER_QUANTUMPIXABAY_FILTERS_SIZE_ORIGINAL') ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="source">
                    <span><?php echo Text::_('COM_QUANTUMMANAGER_QUANTUMPIXABAY_LICENSE') ?></span> <a href="https://pixabay.com" target="_blank"><span class="quantummanager-icon quantummanager-icon-pixabay"></span></a>
                </div>
            </div>
        </div>

        <div class="quantumpixabay-module-container-search-wrap">
            <div class="quantumpixabay-module-container-search">
                <div class="quantumpixabay-module-search grid"></div>
                <div class="quantumpixabay-module-load-page">
                    <button class="btn"><?php echo Text::_('COM_QUANTUMMANAGER_ACTION_LOAD'); ?></button>
                </div>
            </div>
        </div>

        <button class="quantumpixabay-module-close btn"><span class="quantummanager-icon quantummanager-icon-close"></span> <?php echo Text::_('COM_QUANTUMMANAGER_WINDOW_CLOSE'); ?></button>
    </div>

</div>

<?php
    $langs = json_encode([
        'notFound' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ALERT_NOTFOUND'), ENT_QUOTES),
        'button' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_QUANTUMPIXABAY_BUTTON'), ENT_QUOTES),
        'buttonLoad' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_LOAD'), ENT_QUOTES),
        'save' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_SAVE'), ENT_QUOTES),
        'close' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_WINDOW_CLOSE'), ENT_QUOTES),
        'search' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_FIELD_LABEL_SEARCH'), ENT_QUOTES),
        'photostock' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_PHOTOSTOCKS'), ENT_QUOTES),
        'searchHelper' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_FIELD_LABEL_SEARCH_HELPER'), ENT_QUOTES),
    ]);

    QuantummanagerHelper::scriptInsertOnPage('quantumPixabay', <<<EOF
    window.QuantumpixabayLang = {$langs};
EOF
);