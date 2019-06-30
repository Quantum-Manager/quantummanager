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
use Joomla\CMS\Factory;

$app = Factory::getApplication();
$app->getSession()->clear('quantummanagerroot');


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
            'maxsize' => QuantummanagerHelper::getParamsComponentValue('maxsize'),
            'dropAreaHidden' => 1,
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
            'metafile' => QuantummanagerHelper::getParamsComponentValue('metafile'),
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
?>

