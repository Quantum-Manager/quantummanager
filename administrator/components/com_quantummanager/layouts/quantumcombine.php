<?php
/**
 * @package    quantummanager
 *
 * @author     Cymbal <cymbal@delo-design.ru>
 * @copyright  Copyright (C) 2019 "Delo Design". All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://delo-design.ru
 */

defined('_JEXEC') or die;
extract($displayData);
?>

<div class="quantummanager">

	<?php if(!empty($displayData['top'])) : ?>
        <div class="quantummanager-top">
            <?php echo $displayData['top'] ?>
        </div>
	<?php endif; ?>

    <div class="quantummanager-container">

		<?php if(!empty($displayData['container-top'])) : ?>
            <div class="quantummanager-container-top">
				<?php echo $displayData['container-top'] ?>
            </div>
		<?php endif; ?>

		<?php if(!empty($displayData['left'])) : ?>
            <div class="quantummanager-left">
                <?php echo $displayData['left'] ?>
                <div class="quantummanager-left-toggle"></div>
            </div>
		<?php endif; ?>

        <?php if(!empty($displayData['center'])) : ?>
            <div class="quantummanager-center">
                <?php echo $displayData['center'] ?>
            </div>
		<?php endif; ?>

        <?php if(!empty($displayData['right'])) : ?>
            <div class="quantummanager-right">
                <?php echo $displayData['right'] ?>
                <div class="quantummanager-right-toggle"></div>
            </div>
        <?php endif; ?>

		<?php if(!empty($displayData['container-bottom'])) : ?>
            <div class="quantummanager-container-bottom">
				<?php echo $displayData['container-bottom'] ?>
            </div>
		<?php endif; ?>

    </div>

	<?php if(!empty($displayData['bottom'])) : ?>
        <div class="quantummanager-bottom">
            <?php echo $displayData['bottom'] ?>
        </div>
	<?php endif; ?>

	<?php if(!empty($displayData['container-bottom-fixed'])) : ?>
        <div class="quantummanager-container-bottom-fixed">
			<?php echo $displayData['container-bottom-fixed'] ?>
        </div>
	<?php endif; ?>

</div>
