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

use Joomla\CMS\Component\ComponentHelper;

?>
<div id="j-main-container" class="span11">

    <?php

    try {
		JLoader::register('JFormFieldQuantumCombine', JPATH_ROOT . '/administrator/components/com_quantummanager/fields/quantumcombine.php');
		JLoader::register('QuantummanagerHelper', JPATH_SITE . '/administrator/components/com_quantummanager/helpers/quantummanager.php');
        $folderRoot = QuantummanagerHelper::getFolderRoot();

		$buttonsBun = [];
		$fields = [
			'quantumtreecatalogs' => [
				'directory' => $folderRoot,
				'position' => 'left',
			],
			'quantumupload' => [
				'directory' => $folderRoot
			],
			'quantumtoolbar' => [
				'buttons' => 'all',
				'buttonsBun' => '',
			],
			'quantumviewfiles' => [
				'directory' => $folderRoot,
				'view' => 'list-grid',
				'onlyfiles' => '0',
			],
			'quantumcropperjs' => [
				'position' => 'bottom'
            ],
			/*'quantumcodemirror' => [
                'position' => 'center'
            ],*/
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

</div>
