<?php
/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$app = Factory::getApplication();
$folder = $app->input->get('folder', '', 'string');

if(!empty($folder))
{
    $app->getSession()->set('quantummanagerroot', 'images/' . $folder);
}
else
{
	$app->getSession()->clear('quantummanagerroot');
}

HTMLHelper::_('stylesheet', 'com_quantummanager/modal.css', [
	'version' => filemtime(__FILE__),
	'relative' => true
]);

if($app->input->get('e_name', '') !== '') {

	HTMLHelper::_('jquery.framework');

	HTMLHelper::_('script', 'com_quantummanager/modaleditor.js', [
		'version' => filemtime(__FILE__),
		'relative' => true
	]);


} else {

	HTMLHelper::_('script', 'com_quantummanager/modalfield.js', [
		'version' => filemtime(__FILE__),
		'relative' => true
	]);

}



?>

	<?php

	try {
		JLoader::register('JFormFieldQuantumCombine', JPATH_ROOT . '/administrator/components/com_quantummanager/fields/quantumcombine.php');
		JLoader::register('QuantummanagerHelper', JPATH_SITE . '/administrator/components/com_quantummanager/helpers/quantummanager.php');
		$folderRoot = 'root';

		$buttonsBun = [];
		$fields = [
			'quantumtreecatalogs' => [
				'directory' => $folderRoot,
				'position' => 'left',
				'cssClass' => 'quantumtreecatalogs-module-muted'
			],
			'quantumupload' => [
				'maxsize' => QuantummanagerHelper::getParamsComponentValue('maxsize', '10'),
				'dropAreaHidden' => QuantummanagerHelper::getParamsComponentValue('dropareahidden', '0'),
				'directory' => $folderRoot
			],
			'quantumtoolbar' => [
				'position' => 'top',
				'buttons' => 'all',
				'buttonsBun' => '',
				'cssClass' => 'quantummanager-module-height-1-1 quantumtoolbar-module-muted quantumtoolbar-padding-horizontal',
			],
			'quantumviewfiles' => [
				'directory' => $folderRoot,
				'view' => 'list-grid',
				'onlyfiles' => '0',
				'metafile' => QuantummanagerHelper::getParamsComponentValue('metafile' , '1'),
			],
			'quantumcropperjs' => [
				'position' => 'bottom'
			]
			/*'quantumcodemirror' => [
				'position' => 'center'
			],*/
		];

		if((int)QuantummanagerHelper::getParamsComponentValue('unsplash', '1'))
		{
			$fields['quantumunsplash'] = [
				'position' => 'bottom'
			];
		}

		$actions = QuantummanagerHelper::getActions();
		if (!$actions->get('core.create'))
		{
			$buttonsBun[] = 'viewfilesCreateDirectory';
			unset($fields['quantumupload']);
		}

		if (!$actions->get('core.delete'))
		{
			unset($fields['quantumcropperjs']);
		}

		if (!$actions->get('core.delete'))
		{
			$buttonsBun[] = 'viewfilesDelete';
		}

		$optionsForField = [
			'name' => 'filemanager',
			'label' => '',
			'fields' => json_encode($fields)
		];

		$field = new JFormFieldQuantumCombine();
		foreach ($optionsForField as $name => $value)
		{
			$field->__set($name, $value);
		}
		echo $field->getInput();
	}
	catch (Exception $e) {
		echo $e->getMessage();
	}
?>

<script type="text/javascript">
    window.QuantumwindowLang = {
        'buttonInsert': '<?php echo Text::_('COM_QUANTUMMANAGER_WINDOW_INSERT'); ?>',
        'inputAlt': '<?php echo Text::_('COM_QUANTUMMANAGER_WINDOW_ALT'); ?>',
        'inputWidth': '<?php echo Text::_('COM_QUANTUMMANAGER_WINDOW_WIDTH'); ?>',
        'inputHeight': '<?php echo Text::_('COM_QUANTUMMANAGER_WINDOW_HEIGHT'); ?>',
    };
</script>


