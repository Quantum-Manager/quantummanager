<?php
/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;

$app = Factory::getApplication();
$app->getSession()->clear('quantummanagerroot');
$app->getSession()->clear('quantummanageraddscripts');

try {
    JLoader::register('JFormFieldQuantumCombine', JPATH_ROOT . '/administrator/components/com_quantummanager/fields/quantumcombine.php');
    JLoader::register('QuantummanagerHelper', JPATH_SITE . '/administrator/components/com_quantummanager/helpers/quantummanager.php');
    $folderRoot = 'root';

    $buttonsBun = [];
    $fields = [
        'quantumtreecatalogs' => [
            'label' => '',
            'directory' => $folderRoot,
            'position' => 'left',
            'cssClass' => 'quantumtreecatalogs-module-muted'
        ],
        'quantumupload' => [
			'label' => '',
			'maxsize' => QuantummanagerHelper::getParamsComponentValue('maxsize', '10'),
            'dropAreaHidden' => QuantummanagerHelper::getParamsComponentValue('dropareahidden', '0'),
            'directory' => $folderRoot
        ],
        'quantumtoolbar' => [
			'label' => '',
			'position' => 'top',
            'buttons' => 'all',
            'buttonsBun' => '',
            'cssClass' => 'quantummanager-module-height-1-1 quantumtoolbar-module-muted quantumtoolbar-padding-horizontal',
        ],
        'quantumviewfiles' => [
			'label' => '',
			'directory' => $folderRoot,
            'view' => 'list-grid',
            'onlyfiles' => '0',
            'watermark' => QuantummanagerHelper::getParamsComponentValue('overlay' , 0) > 0 ? '1' : '0',
			'help' => QuantummanagerHelper::getParamsComponentValue('help' , '1'),
			'metafile' => QuantummanagerHelper::getParamsComponentValue('metafile' , '1'),
        ],
        'quantumcropperjs' => [
			'label' => '',
			'position' => 'bottom'
        ],
    ];

    if((int)QuantummanagerHelper::getParamsComponentValue('unsplash', '1'))
    {
		$fields['quantumunsplash'] = [
			'label' => '',
			'position' => 'bottom'
        ];
    }

    if((int)QuantummanagerHelper::getParamsComponentValue('pixabay', '1'))
    {
        $fields['quantumpixabay'] = [
			'label' => '',
			'position' => 'bottom'
        ];
    }

    if((int)QuantummanagerHelper::getParamsComponentValue('pexels', '1'))
    {
        $fields['quantumpexels'] = [
            'label' => '',
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
        'cssClass' => 'quantummanager-full-component-wrap',
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