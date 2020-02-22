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

HTMLHelper::_('stylesheet', 'com_quantummanager/modalcontent.css', [
	'version' => filemtime(__FILE__),
	'relative' => true
]);

HTMLHelper::_('jquery.framework');

HTMLHelper::_('script', 'com_quantummanager/modalcontent.js', [
	'version' => filemtime(__FILE__),
	'relative' => true
]);


HTMLHelper::_('script', 'com_quantummanager/sortable.min.js', [
	'version' => filemtime(__FILE__),
	'relative' => true
]);

?>

<?php

    JLoader::register('JFormFieldQuantumCombine', JPATH_ROOT . '/administrator/components/com_quantummanager/fields/quantumcombine.php');
    JLoader::register('QuantummanagerHelper', JPATH_SITE . '/administrator/components/com_quantummanager/helpers/quantummanager.php');
    JLoader::register('QuantummanagerbuttonHelper', JPATH_ROOT . '/plugins/editors-xtd/quantummanagerbutton/helper.php');

    QuantummanagerbuttonHelper::loadLang();
    $fieldsForContentPlugin = QuantummanagerbuttonHelper::getFieldsForScopes();
    $templatelistForContentPlugin = QuantummanagerbuttonHelper::getTemplateListForScopes();
    $groups = Factory::getUser()->groups;

	try {

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
				'watermark' => QuantummanagerHelper::getParamsComponentValue('overlay' , 0) > 0 ? '1' : '0',
				'help' => QuantummanagerHelper::getParamsComponentValue('help' , '1'),
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

		if((int)QuantummanagerHelper::getParamsComponentValue('pixabay', '1'))
		{
			$fields['quantumpixabay'] = [
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

    window.QuantumContentPlugin = {
        templatelist: '<?php echo QuantummanagerHelper::escapeJsonString(json_encode($templatelistForContentPlugin)) ?>',
        fields: '<?php echo QuantummanagerHelper::escapeJsonString(json_encode($fieldsForContentPlugin, JSON_FORCE_OBJECT)) ?>'
    };

    window.QuantumwindowLang = {
        'buttonInsert': '<?php echo Text::_('COM_QUANTUMMANAGER_WINDOW_INSERT'); ?>',
        'inputAlt': '<?php echo Text::_('COM_QUANTUMMANAGER_WINDOW_ALT'); ?>',
        'inputWidth': '<?php echo Text::_('COM_QUANTUMMANAGER_WINDOW_WIDTH'); ?>',
        'defaultScope': '<?php echo Text::_('PLG_BUTTON_QUANTUMMANAGERCONTENT_SCOPES_NAME_DEFAULT'); ?>',
        'defaultName': '<?php echo Text::_('PLG_BUTTON_QUANTUMMANAGERCONTENT_SCOPES_DOCS_FIELDSFORM_NAME_NAME'); ?>',
        'defaultNameValue': '<?php echo Text::_('PLG_BUTTON_QUANTUMMANAGERCONTENT_SCOPES_IMAGES_FIELDSFORM_DEFAULT_NAME'); ?>',
        'helpTemplate': '<?php echo Text::_('PLG_BUTTON_QUANTUMMANAGERCONTENT_HELP_TEMPLATE'); ?>',
        'helpSettings': '<?php echo in_array('2', $groups) || in_array('8', $groups) ? Text::sprintf('PLG_BUTTON_QUANTUMMANAGERCONTENT_HELP_SETTINGS', 'index.php?' . http_build_query(['option' => 'com_plugins', 'view' => 'plugins', 'filter.search' => Text::_('PLG_BUTTON_QUANTUMMANAGERCONTENT')])) : '' ?>',
    };
</script>


