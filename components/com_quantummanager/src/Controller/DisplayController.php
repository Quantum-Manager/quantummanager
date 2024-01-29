<?php namespace Joomla\Component\QuantumManager\Site\Controller;

/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSWebApplicationInterface;
use Joomla\CMS\Cache\Exception\CacheExceptionInterface;
use Joomla\CMS\Document\DocumentAwareInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\LanguageAwareInterface;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\User\CurrentUserInterface;

/**
 * DisplayController Controller.
 *
 * @package  quantummanager
 * @since    1.0
 */
class DisplayController extends BaseController
{

	protected $default_view = 'quantummanager';

}
