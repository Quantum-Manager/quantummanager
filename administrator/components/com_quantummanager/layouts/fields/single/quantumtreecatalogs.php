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
$cssClass = $displayData['cssClass'];

?>

<div class="quantumtreecatalogs-module-select">
    <div class="quantummanager-module quantumtreecatalogs-module <?php echo $cssClass ?>" data-type="Quantumtreecatalogs" data-options="directory:<?php echo $displayData['directory'] ?>">
        <input type="text" name="<?php echo $name ?>" value="<?php echo $value ?>" class="inputbox quantumtreecatalogs-path-to">
        <div class="tree-scroll"></div>
    </div>
</div>


<?php
    $langs = json_encode([
        'fileName' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ALERT_FILE_NAME'), ENT_QUOTES),
        'directoryName' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ALERT_DIRECTORY_NAME'), ENT_QUOTES),
        'confirmDelete' => htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ALERT_CONFIRM_DELETE'), ENT_QUOTES),
    ]);

    QuantummanagerHelper::scriptInsertOnPage('quantumTreecatalogs', <<<EOF
    window.QuantumtreecatalogsLang = {$langs};
EOF
);