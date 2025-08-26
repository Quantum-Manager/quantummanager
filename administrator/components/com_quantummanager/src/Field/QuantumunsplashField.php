<?php namespace Joomla\Component\QuantumManager\Administrator\Field;

/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\Component\QuantumManager\Administrator\Helper\QuantummanagerLibsHelper;

class QuantumunsplashField extends QuantumbaseField
{

	public $type = 'QuantumUnsplash';

	protected $layout = 'quantumunsplash';

	protected function getLayoutPaths(): array
	{
		return array_merge(parent::getLayoutPaths(), [
			JPATH_ROOT . '/administrator/components/com_quantummanager/layouts/fields',
		]);
	}

	protected function getLayoutData(): array
	{
		return array_merge(parent::getLayoutData(),
			[
				'cssClass' => $this->cssClass,
			]
		);
	}

	public function getInput(): string
	{
		try
		{

			$this->__set('standalone', $this->getAttribute('standalone', true));
			$this->__set('cssClass', $this->getAttribute('cssClass', ''));

			QuantummanagerLibsHelper::includeScriptHead();
			QuantummanagerLibsHelper::includes([
				'core',
				'utils',
				'dynamicGrid',
			]);

			HTMLHelper::_('stylesheet', 'com_quantummanager/quantumunsplash.css', [
				'version'  => filemtime(__FILE__),
				'relative' => true
			]);

			HTMLHelper::_('script', 'com_quantummanager/quantumunsplash.js', [
				'version'  => filemtime(__FILE__),
				'relative' => true
			]);

			$field = parent::getInput();

			if ($this->standalone)
			{
				$filemanager = new FileLayout('fieldstandalone', JPATH_ROOT . '/administrator/components/com_quantummanager/layouts');

				return $filemanager->render(['field' => $field]);
			}

			return $field;
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}

}