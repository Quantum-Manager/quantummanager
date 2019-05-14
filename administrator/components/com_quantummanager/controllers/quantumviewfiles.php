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
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\Filesystem\Folder;

/**
 * Quantummanager Controller.
 *
 * @package  quantummanager
 * @since    1.0
 */
class QuantummanagerControllerquantumviewfiles extends AdminController
{

	public function createDirectory()
	{
		$app = Factory::getApplication();
		$data = $app->input->getArray();

		JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
		echo QuantummanagerFileSystemLocal::createDirectory($data['path'], $data['name']);

		$app->close();
	}

	public function getFiles()
	{
		$app = Factory::getApplication();
		$data = $app->input->getArray();

		JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
		echo QuantummanagerFileSystemLocal::getFiles($data['path']);

		$app->close();
	}



	public function delete()
	{
		$app = Factory::getApplication();
		$data = $app->input->getArray();
		$path = $data['path'];
		$list = json_decode($data['list']);

		JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
		echo QuantummanagerFileSystemLocal::delete($path, $list);

		$app->close();

	}

}
