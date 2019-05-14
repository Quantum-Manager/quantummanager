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
 * Class QuantummanagerControllerQuantumconverter
 */
class QuantummanagerControllerQuantumconverter extends AdminController
{

	public function save()
	{

		$app = Factory::getApplication();

		JLoader::register('QuantummanagerFileSystemLocal', JPATH_ROOT . '/administrator/components/com_quantummanager/filesystem/local.php');
		echo QuantummanagerFileSystemLocal::converterSave();

		$app->close();

	}

}