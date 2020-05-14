<?php
/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
extract($displayData);

$scopeEnabled = [];
foreach($scopes as $scope)
{
	$scopeEnabled[] = $scope->id;
}

?>

<div class="quantummanager <?php echo $cssClass ?>">

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

<script type="text/javascript">
    window.QuantumSettings = {
        urlFull: '<?php echo $urlFull ?>',
        urlBase: '<?php echo $urlBase ?>',
        scopeEnabled: '<?php echo implode(',', $scopeEnabled) ?>',
    };

    window.QuantumLang = {
        'ok': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_WINDOW_OK'), ENT_QUOTES); ?>",
        'close': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_WINDOW_CLOSE'), ENT_QUOTES); ?>",
        'copied': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_ACTION_COPIED'), ENT_QUOTES); ?>",
        'cancel': "<?php echo htmlspecialchars(Text::_('COM_QUANTUMMANAGER_WINDOW_CANCEL'), ENT_QUOTES); ?>"
    };

</script>
