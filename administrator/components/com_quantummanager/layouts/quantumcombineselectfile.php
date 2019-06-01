<?php

/**
 * @package    quantummanager
 *
 * @author     Cymbal <cymbal@delo-design.ru>
 * @copyright  Copyright (C) 2019 "Delo Design". All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://delo-design.ru
 */

use Joomla\CMS\Layout\FileLayout;

defined('_JEXEC') or die;
extract($displayData);
?>

<div class="quantumcombineselectfile">
    <input class="input-file <?php echo $displayData['class'] ?>" type="hidden" name="<?php echo $displayData['name'] ?>" id="<?php echo $displayData['id'] ?>" value="<?php echo $displayData['value'] ?>">

    <div class="preview-file" data-value="<?php echo $displayData['value'] ?>">
        <div class="image"></div>
        <button>Изменить</button>
    </div>

	<?php
	$template = new FileLayout('quantumcombine', JPATH_ROOT . '/administrator/components/com_quantummanager/layouts');
	echo $template->render($displayData);
	?>

</div>