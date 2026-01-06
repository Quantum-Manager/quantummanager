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

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\QuantumManager\Administrator\Helper\QuantummanagerHelper;

class QuantumbaseField extends FormField
{

	private array $addLayouts = [];

	protected string $cssClass = '';

	protected bool $standalone = false;

	protected function getLayoutData(): array
	{
		$scopes = QuantummanagerHelper::getAllScope();

		if (empty($this->element))
		{
			$this->element = [
				'name'  => '',
				'label' => ''
			];
		}

		$urlFull = Uri::root(false);
		$urlBase = Uri::root(true);
		$urlRoot = $urlMedia = $urlFull;

		if (substr($urlBase, -1) !== '/')
		{
			$urlBase .= '/';
		}

		if (Factory::getApplication()->isClient('administrator'))
		{
			$urlFull .= 'administrator/';
			$urlBase .= 'administrator/';
		}

		return array_merge(parent::getLayoutData(),
			[
				'cssClass'  => $this->cssClass,
				'urlFull'   => $urlFull,
				'urlBase'   => $urlBase,
				'urlRoot'   => $urlRoot,
				'urlMedia'  => $urlMedia,
				'scopes'    => $scopes,
			]
		);
	}

	protected function getLayoutPaths(): array
	{
		return array_merge(parent::getLayoutPaths(), $this->addLayouts, [
			JPATH_ROOT . '/administrator/components/com_quantummanager/layouts/fields',
			JPATH_ROOT . '/layouts/joomla/form',
		]);
	}

	public function addCustomLayoutsPath(array $layouts): void
	{
		$this->addLayouts = array_merge($this->addLayouts, $layouts);
	}

}