<?php

use Joomla\CMS\Layout\FileLayout;

defined('_JEXEC') or die;
extract($displayData);
?>



<div class="quantummanager quantumcombineselectfile">
    <input class="input-file <?php echo $displayData['class'] ?>" type="hidden" name="<?php echo $displayData['name'] ?>" id="<?php echo $displayData['id'] ?>" value="<?php echo $displayData['value'] ?>">

	<div class="preview-file" data-value="<?php echo $displayData['value'] ?>">
        <div class="image"></div>
        <button>Изменить</button>
	</div>

	<div class="quantummanager-modules">
		<?php
            $template = new FileLayout('quantumcombine', JPATH_ROOT . '/administrator/components/com_quantummanager/layouts');
            echo $template->render($displayData);
        ?>
	</div>
</div>