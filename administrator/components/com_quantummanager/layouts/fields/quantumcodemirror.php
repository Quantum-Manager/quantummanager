<?php
/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

defined('_JEXEC') or die;
extract($displayData);
?>

<div class="quantummanager-module quantumcodemirror-module" data-type="Quantumcodemirror" data-options="">
    <div class="toolbar">
        <div>
            <button class="qm-btn qm-btn-save">Сохранить</button>
            <button class="qm-btn qm-btn-close">Отменить</button>
        </div>
    </div>
    <div class="editor"></div>
</div>
