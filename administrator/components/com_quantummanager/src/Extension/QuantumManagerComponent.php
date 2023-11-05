<?php namespace Joomla\Component\QuantumManager\Administrator\Extension;

\defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Component\Router\RouterServiceInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use Joomla\CMS\Plugin\PluginHelper;
use Psr\Container\ContainerInterface;

class QuantumManagerComponent extends MVCComponent implements
	BootableExtensionInterface, RouterServiceInterface
{
	use HTMLRegistryAwareTrait;
	use RouterServiceTrait;

	public function boot(ContainerInterface $container)
	{
		Factory::getApplication()->getLanguage()->load('com_quantummanager');
		PluginHelper::importPlugin('quantummanager');
	}

}
