<?php

namespace Joomla\Component\QuantumManager\Administrator\Field;

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

class QuantumtoolbarField extends QuantumbaseField
{

	public $type = 'QuantumToolbar';

	protected $layout = 'quantumtoolbar';

	protected string $buttons;

	protected string $buttonsBun;

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
				'buttons'    => explode(',', $this->buttons),
				'buttonsBun' => explode(',', $this->buttonsBun),
				'cssClass'   => $this->cssClass,
			]
		);
	}

	public function getInput(): string
	{
		try
		{

			$this->__set('standalone', $this->getAttribute('standalone', true));
			$this->__set('buttons', $this->getAttribute('buttons', 'all'));
			$this->__set('buttonsBun', $this->getAttribute('buttonsBun', ','));
			$this->__set('cssClass', $this->getAttribute('cssClass', ''));

			QuantummanagerLibsHelper::includeScriptHead();
			QuantummanagerLibsHelper::includes([
				'core',
				'utils',
			]);

			HTMLHelper::_('stylesheet', 'com_quantummanager/quantumtoolbar.css', [
				'version'  => filemtime(__FILE__),
				'relative' => true
			]);

			HTMLHelper::_('script', 'com_quantummanager/quantumtoolbar.js', [
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