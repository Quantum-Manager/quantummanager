<?php

namespace Joomla\Component\QuantumManager\Administrator\Controller;

/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\Component\QuantumManager\Administrator\Filesystem\LocalFilesystem;
use Joomla\Component\QuantumManager\Administrator\Helper\QuantummanagerHelper;

class QuantumcropperjsController extends BaseController
{

	public function getImageForCrop(): void
	{
		$data = $this->app->input->getArray();

		if (!isset($data['path'], $data['scope'], $data['file']))
		{
			$this->app->close();
		}

		echo LocalFilesystem::getImageForCrop($data['path'], $data['scope'], $data['file']);

		QuantummanagerHelper::setHeadersNoCache();
		$this->app->close();
	}

	public function save(): void
	{

		echo LocalFilesystem::converterSave();

		QuantummanagerHelper::setHeadersNoCache();

		$this->app->close();
	}

}