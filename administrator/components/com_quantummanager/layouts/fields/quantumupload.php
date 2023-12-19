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
use Joomla\Component\QuantumManager\Administrator\Helper\QuantummanagerHelper;

$cssClass = $displayData['cssClass'];
?>

<div class="quantummanager-module quantummanager-backgrounds-muted quantumupload-module <?php echo $cssClass ?> <?php if($displayData['dropAreaHidden'] === '1') : ?> qm-padding-none <?php endif; ?>" data-type="Qantumupload" data-options="maxsize:<?php echo $displayData['maxsize'] ?>;maxsizeServer:<?php echo $displayData['maxsizeServer'] ?>;scope:<?php echo $displayData['scope'] ?>;directory:<?php echo $displayData['directory'] ?>;dropAreaHidden:<?php echo $displayData['dropAreaHidden'] ?>">

	<?php $id = mt_rand(11111, 99999); ?>
    <div class="drop-area <?php if($displayData['dropAreaHidden'] === '1') : ?> qm-hide <?php endif; ?>">
        <div class="form-upload">
            <input type="hidden" class="pathElem" name="path">
            <span class="quantummanager-icon quantummanager-icon-upload"></span>
            <p><?php echo Text::_('COM_QUANTUMMANAGER_QUANTUMUPLOAD_UPLOAD_DROP') ?></p>
            <label class="button" for="fileElem-<?= $id ?>"><?php echo Text::_('COM_QUANTUMMANAGER_QUANTUMUPLOAD_UPLOAD_SELECT') ?></label>
            <input type="file" id="fileElem-<?= $id ?>" class="fileElem" multiple accept="*">
        </div>
    </div>

    <progress class="progress-bar" max="100" value="0"></progress>

    <div class="upload-errors">
        <div></div>
        <a class="uk-alert-close uk-close uk-icon upload-errors-close"><svg width="14" height="14" viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg"><line fill="none" stroke="#000" stroke-width="1.1" x1="1" y1="1" x2="13" y2="13"></line><line fill="none" stroke="#000" stroke-width="1.1" x1="13" y1="1" x2="1" y2="13"></line></svg></a>
    </div>

</div>

<?php
    $langs = json_encode([
        'dragDrop' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_QUANTUMUPLOAD_DRAG_DROP'), ENT_QUOTES),
        'file' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_QUANTUMUPLOAD_UPLOAD_ERROR_FILE'), ENT_QUOTES),
        'megabyte' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_QUANTUMUPLOAD_UPLOAD_ERROR_MEGABITE'), ENT_QUOTES),
        'maxsize' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_QUANTUMUPLOAD_UPLOAD_ERROR_MAXSIZE'), ENT_QUOTES),
        'exs' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_QUANTUMUPLOAD_UPLOAD_ERROR_EXS'), ENT_QUOTES),
    ]);

    QuantummanagerHelper::scriptInsertOnPage('quantumUpload', <<<EOF
    window.QuantumuploadLang = {$langs};
EOF
);