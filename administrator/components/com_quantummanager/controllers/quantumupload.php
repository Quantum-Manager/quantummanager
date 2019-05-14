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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;

/**
 * Quantummanager Controller.
 *
 * @package  quantummanager
 * @since    1.0
 */
class QuantummanagerControllerQuantumupload extends AdminController
{


	public function upload()
	{
		$app = Factory::getApplication();

		JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
		echo QuantummanagerFileSystemLocal::upload();

		$app->close();
	}

}
