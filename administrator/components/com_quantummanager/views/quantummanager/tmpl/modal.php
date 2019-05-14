<?php
/**
 * @package    quantummanager
 *
 * @author     tsymb <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

$app = Factory::getApplication();

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
		$buttonsBun = [];
		$fields = [
			'quantumtreecatalogs' => [
				'directory' => 'images',
				'position' => 'left',
			],
			'quantumupload' => [
				'directory' => 'images'
			],
			'quantumtoolbar' => [
				'buttons' => 'all',
				'buttonsBun' => '',
			],
			'quantumviewfiles' => [
				'directory' => 'images',
				'view' => 'list-grid',
				'onlyfiles' => '0',
			],
			'quantumcropperjs' => [
				'position' => 'bottom'
			],
		];

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


