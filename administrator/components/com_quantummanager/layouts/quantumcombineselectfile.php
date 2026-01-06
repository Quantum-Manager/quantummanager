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
use Joomla\CMS\Layout\FileLayout;

extract($displayData);
?>

<div class="quantumcombineselectfile">
    <input class="input-file <?php echo $displayData['class'] ?>" type="hidden"
           name="<?php echo $displayData['name'] ?>" id="<?php echo $displayData['id'] ?>"
           value="<?php echo $displayData['value'] ?>">

    <div class="preview-file" data-value="<?php echo $displayData['value'] ?>">
        <div class="image"></div>
        <button><?php echo Text::_('COM_QUANTUMMANAGER_ACTION_EDIT') ?></button>
    </div>

	<?php
	$template = new FileLayout('quantumcombine', JPATH_ROOT . '/administrator/components/com_quantummanager/layouts');
	echo $template->render($displayData);
	?>

</div>