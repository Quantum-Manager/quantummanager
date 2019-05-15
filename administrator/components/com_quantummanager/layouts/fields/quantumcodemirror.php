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
extract($displayData);
?>

<div class="quantummanager-module quantumcodemirror-module" data-type="Quantumcodemirror" data-options="">
    <div class="toolbar">
        <div>
            <button class="btn btn-save">Сохранить</button>
            <button class="btn btn-close">Отменить</button>
        </div>
    </div>
    <div class="editor"></div>
</div>
