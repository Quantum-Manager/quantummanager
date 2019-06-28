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
$presets = $paramsComponents->get('custompresetscrop');
?>

<div class="quantummanager-module quantumcropperjs-module" data-type="Quantumcropperjs" data-options="">
    <div class="cropper-save">
        <div class="loader"><?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_SAVEAREA'); ?><span></span><span></span><span></span><span></span></div>
    </div>
    <div class="toolbar">
        <div class="name-file-wrap">
            <label><?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_LABEL_FILE'); ?></label>
            <div class="input-wrapper">
                <input type="text" placeholder="Input Outline" class="quantumcropperjs-name-file" name="name" >
                <span class="bottom"></span>
                <span class="right"></span>
                <span class="top"></span>
                <span class="left"></span>
            </div>
            <select class="quantumcropperjs-name-exs" name="exs">
                <option value="jpg">jpg</option>
                <option value="jpeg">jpeg</option>
                <option value="png">png</option>
                <option value="webp">webp</option>
            </select>
        </div>
        <div class="change-ratio-wrap">
            <label><?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_LABEL_RATIO'); ?></label>
            <select class="change-ratio" name="change-ratio">
                <option value="NaN" selected="selected"><?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_FREE'); ?></option>
                <?php if(!empty($presets)) : ?>
                    <?php foreach ($presets as $preset) : ?>
                        <option value="<?php echo $preset->ratio ?>"><?php echo $preset->label ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
                <option value="1">1:1</option>
                <option value="1.7777777777777777">16:9</option>
                <option value="2">18:9</option>
                <option value="1.3333333333333333">4:3</option>
                <option value="0.6666666666666666">2:3</option>
            </select>
        </div>
        <div class="input-width-height-wrap">
            <label><?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_TOOLTIP_CROP'); ?></label>

            <div>
                <label><?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_TOOLTIP_WIDTH'); ?></label>
                <div>
                    <input type="number" class="crop-width-value" disabled>
                    <span>PX</span>
                </div>
            </div>

            <div>
                <label><?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_TOOLTIP_HEIGHT'); ?></label>
                <div>
                    <input type="number" class="crop-height-value" disabled>
                    <span>PX</span>
                </div>
            </div>
        </div>
        <div class="input-width-height-wrap">
            <label><?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_TOOLTIP_IMAGE'); ?></label>

            <div>
                <label><?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_TOOLTIP_WIDTH'); ?></label>
                <div>
                    <input type="text" class="image-width-value">
                    <span>PX</span>
                </div>
            </div>

            <div>
                <label><?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_TOOLTIP_HEIGHT'); ?></label>
                <div>
                    <input type="text" class="image-height-value">
                    <span>PX</span>
                </div>
            </div>

            <label class="image-width-height-ratio-checkbox"><input type="checkbox" class="image-width-height-ratio" checked="checked"> <?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_LABEL_SAVE_RATIO'); ?></label>
        </div>
        <div class="button-wrap btn-group">
            <button class="btn btn-save">
                <span><?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_SAVE'); ?></span>
            </button>
            <button class="btn btn-close">
                <span><?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_CLOSE'); ?></span>
            </button>
        </div>
    </div>
    <div class="editor">
        <div class="cropperjs"></div>
        <div class="toolbar-footer">
            <div class="buttons-methods">

                <div class="btn-group">
                    <button type="button" class="btn quantummanager-tooltip" data-method="zoom" data-option="0.1" data-tooltip="<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_ZOOM_IN'); ?>">
                        <span class="docs-tooltip" data-toggle="tooltip" title="" data-original-title="cropper.zoom(0.1)">
                            <span class="quantummanager-icon quantummanager-icon-zoom-in"></span>
                        </span>
                    </button>
                    <button type="button" class="btn quantummanager-tooltip" data-method="zoom" data-option="-0.1" data-tooltip="<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_ZOOM_OUT'); ?>">
                        <span class="docs-tooltip" data-toggle="tooltip" title="" data-original-title="cropper.zoom(-0.1)">
                            <span class="quantummanager-icon quantummanager-icon-zoom-out"></span>
                        </span>
                    </button>
                </div>

                <div class="btn-group">
                    <button type="button" class="btn quantummanager-tooltip" data-method="rotate" data-option="-45" data-tooltip="<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_ROTATE_LEFT'); ?>">
                        <span class="docs-tooltip" data-toggle="tooltip" title="" data-original-title="cropper.rotate(-45)">
                            <span class="quantummanager-icon quantummanager-icon-rotate-left"></span>
                        </span>
                    </button>
                    <button type="button" class="btn quantummanager-tooltip" data-method="rotate" data-option="45" data-tooltip="<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_ROTATE_RIGHT'); ?>">
                        <span class="docs-tooltip" data-toggle="tooltip" title="" data-original-title="cropper.rotate(45)">
                            <span class="quantummanager-icon quantummanager-icon-rotate-right"></span>
                        </span>
                    </button>
                </div>

                <div class="btn-group">
                    <button type="button" class="btn quantummanager-tooltip" data-method="scaleX" data-option="-1" data-tooltip="<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_FLIP_VERTICAL'); ?>">
                        <span class="docs-tooltip" data-toggle="tooltip" title="" data-original-title="cropper.scaleX(-1)">
                            <span class="quantummanager-icon quantummanager-icon-flip-horizontal"></span>
                        </span>
                    </button>
                    <button type="button" class="btn quantummanager-tooltip" data-method="scaleY" data-option="-1" data-tooltip="<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_FLIP_HORIZONTAL'); ?>">
                        <span class="docs-tooltip" data-toggle="tooltip" title="" data-original-title="cropper.scaleY(-1)">
                            <span class="quantummanager-icon quantummanager-icon-flip-vertical"></span>
                        </span>
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>