<?php
/**
 * @package    quantummanager
 *
 * @author     tsymb <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\Filesystem\Folder;

defined('_JEXEC') or die;

/**
 * Quantummanager Controller.
 *
 * @package  quantummanager
 * @since    1.0
 */
class QuantummanagerControllerQuantumtreecatalogs extends AdminController
{


	public function getDirectories()
	{
		$app = Factory::getApplication();
		$data = $app->input->getArray();
		$path = JPATH_ROOT . DIRECTORY_SEPARATOR . $data['path'];

		JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
		echo QuantummanagerFileSystemLocal::getDirectories($path);

		$app->close();
	}

}
