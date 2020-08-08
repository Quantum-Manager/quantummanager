<?php
/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

defined('_JEXEC') or die;


JFormHelper::loadFieldClass('text');

/**
 * Class JFormFieldQuantumexiflist
 */
class JFormFieldQuantumexiflist extends JFormFieldList
{

    /**
     * @var string
     * @since version
     */
    public $type = 'QuantumExifList';


    public function getOptions()
    {
        $options = parent::getOptions();

        $exifList = file_get_contents(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, ['components', 'com_quantummanager', 'exiflist.txt']));
        $exifList = explode("\n", $exifList);

        foreach ($exifList as $value)
        {
            $option        = new stdClass();
            $option->value = $value;
            $option->text  = $value;
            $options[]     = $option;
        }

        $this->_options = $options;

        return $this->_options;

    }

}