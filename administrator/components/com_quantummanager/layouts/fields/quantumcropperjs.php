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
?>

<div class="quantummanager-module quantumcropperjs-module" data-type="Quantumcropperjs" data-options="">
    <div class="toolbar">
        <div>
            <div class="btn-group">
                <button class="btn btn-save">
                    <span class="quantummanager-icon quantummanager-icon-save"></span>
                    <span><?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_SAVE'); ?></span>
                </button>
                <button class="btn btn-close">
                    <span class="quantummanager-icon quantummanager-icon-close"></span>
                    <span><?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_CLOSE'); ?></span>
                </button>
            </div>

            <div class="name-file-wrap">
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
        </div>
        <div>
            <div class="buttons-toggles">
                <div class="btn-group d-flex flex-nowrap" data-toggle="buttons">
                    <label class="btn active">
                        <input type="radio" class="sr-only" id="aspectRatio1" name="aspectRatio" value="1.7777777777777777">
                        <span class="docs-tooltip" data-toggle="tooltip" title="aspectRatio: 16 / 9">16:9</span>
                    </label>
                    <label class="btn">
                        <input type="radio" class="sr-only" id="aspectRatio2" name="aspectRatio" value="2">
                        <span class="docs-tooltip" data-toggle="tooltip" title="aspectRatio: 18/ 9">18:9</span>
                    </label>
                    <label class="btn">
                        <input type="radio" class="sr-only" id="aspectRatio2" name="aspectRatio" value="1.3333333333333333">
                        <span class="docs-tooltip" data-toggle="tooltip" title="aspectRatio: 4 / 3">4:3</span>
                    </label>
                    <label class="btn">
                        <input type="radio" class="sr-only" id="aspectRatio3" name="aspectRatio" value="1">
                        <span class="docs-tooltip" data-toggle="tooltip" title="aspectRatio: 1 / 1">1:1</span>
                    </label>
                    <label class="btn">
                        <input type="radio" class="sr-only" id="aspectRatio4" name="aspectRatio" value="0.6666666666666666">
                        <span class="docs-tooltip" data-toggle="tooltip" title="aspectRatio: 2 / 3">2:3</span>
                    </label>
                    <label class="btn">
                        <input type="radio" class="sr-only" id="aspectRatio5" name="aspectRatio" value="NaN">
                        <span class="docs-tooltip" data-toggle="tooltip" title="aspectRatio: NaN"><?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_FREE'); ?></span>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="editor"></div>
    <div class="toolbar-sizes">
        <div><?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_TOOLTIP_IMAGE'); ?></div>
        <div>
            <span><?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_TOOLTIP_WIDTH'); ?>:</span>
            <span class="image-width-value"></span>
            <span><?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_TOOLTIP_HEIGHT'); ?>:</span>
            <span class="image-height-value"></span>
        </div>
        <div><?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_TOOLTIP_CROP'); ?></div>
        <div>
            <span><?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_TOOLTIP_WIDTH'); ?>:</span>
            <span class="crop-width-value"></span>
            <span><?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_TOOLTIP_HEIGHT'); ?>:</span>
            <span class="crop-height-value"></span>
        </div>
    </div>
    <div class="toolbar-footer">
        <div class="buttons-methods">
            <div class="btn-group">
                <button type="button" class="btn quantummanager-tooltip" data-method="setDragMode" data-option="move" data-tooltip="<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_MOVE'); ?>">
                        <span class="docs-tooltip" data-toggle="tooltip" title="" data-original-title="cropper.setDragMode(&quot;move&quot;)">
                            <span class="quantummanager-icon quantummanager-icon-move"></span>
                        </span>
                </button>
                <button type="button" class="btn quantummanager-tooltip" data-method="setDragMode" data-option="crop" data-tooltip="<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_CROP'); ?>">
                        <span class="docs-tooltip" data-toggle="tooltip" title="" data-original-title="cropper.setDragMode(&quot;crop&quot;)">
                            <span class="quantummanager-icon quantummanager-icon-crop"></span>
                        </span>
                </button>
            </div>

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
                <button type="button" class="btn quantummanager-tooltip" data-method="move" data-option="-10" data-second-option="0" data-tooltip="<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_MOVE_LEFT'); ?>">
                        <span class="docs-tooltip" data-toggle="tooltip" title="" data-original-title="cropper.move(-10, 0)">
                            <span class="quantummanager-icon quantummanager-icon-left"></span>
                        </span>
                </button>
                <button type="button" class="btn quantummanager-tooltip" data-method="move" data-option="10" data-second-option="0" data-tooltip="<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_MOVE_RIGHT'); ?>">
                        <span class="docs-tooltip" data-toggle="tooltip" title="" data-original-title="cropper.move(10, 0)">
                            <span class="quantummanager-icon quantummanager-icon-right"></span>
                        </span>
                </button>
                <button type="button" class="btn quantummanager-tooltip" data-method="move" data-option="0" data-second-option="-10" data-tooltip="<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_MOVE_UP'); ?>">
                        <span class="docs-tooltip" data-toggle="tooltip" title="" data-original-title="cropper.move(0, -10)">
                            <span class="quantummanager-icon quantummanager-icon-up"></span>
                        </span>
                </button>
                <button type="button" class="btn quantummanager-tooltip" data-method="move" data-option="0" data-second-option="10" data-tooltip="<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_MOVE_DOWN'); ?>">
                        <span class="docs-tooltip" data-toggle="tooltip" title="" data-original-title="cropper.move(0, 10)">
                            <span class="quantummanager-icon quantummanager-icon-down"></span>
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

            <div class="btn-group">
                <button type="button" class="btn quantummanager-tooltip" data-method="crop" data-tooltip="<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_CROP'); ?>">
                        <span class="docs-tooltip" data-toggle="tooltip" title="" data-original-title="cropper.crop()">
                            <span class="quantummanager-icon quantummanager-icon-crop"></span>
                        </span>
                </button>
                <button type="button" class="btn quantummanager-tooltip" data-method="clear" data-tooltip="<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_CLEAR'); ?>">
                        <span class="docs-tooltip" data-toggle="tooltip" title="" data-original-title="cropper.clear()">
                            <span class="quantummanager-icon quantummanager-icon-clear"></span>
                        </span>
                </button>
            </div>
            <!--
            <div class="btn-group">
                <button type="button" class="btn" data-method="disable" data-tooltip="<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_DISABLE'); ?>">
                        <span class="docs-tooltip" data-toggle="tooltip" title="" data-original-title="cropper.disable()">
                            <span class="quantummanager-icon quantummanager-icon-lock"></span>
                        </span>
                </button>
                <button type="button" class="btn" data-method="enable" data-tooltip="<?php echo Text::_('COM_QUANTUMMANAGER_FIELDS_QUANTUMCROPPERJS_ENABLE'); ?>">
                        <span class="docs-tooltip" data-toggle="tooltip" title="" data-original-title="cropper.enable()">
                            <span class="quantummanager-icon quantummanager-icon-unlock"></span>
                        </span>
                </button>
            </div>
            -->

        </div>
    </div>
</div>