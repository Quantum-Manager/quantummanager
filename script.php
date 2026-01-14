<?php

defined('_JEXEC') or die;

use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;
use Joomla\Database\DatabaseDriver;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class () implements ServiceProviderInterface {
	public function register(Container $container): void
	{
		$container->set(InstallerScriptInterface::class, new class ($container->get(AdministratorApplication::class)) implements InstallerScriptInterface {

			protected AdministratorApplication $app;

			protected DatabaseDriver $db;

			protected string $minimumJoomla = '4.2.0';

			protected string $minimumPhp = '8.1';

			public function __construct(AdministratorApplication $app)
			{
				$this->app = $app;
				$this->db  = Factory::getContainer()->get('DatabaseDriver');
			}

			public function preflight(string $type, InstallerAdapter $adapter): bool
			{
				if (!$this->checkCompatible())
				{
					return false;
				}

				return true;
			}

			protected function checkCompatible(): bool
			{
				$app = Factory::getApplication();

				if (!(new Version())->isCompatible($this->minimumJoomla))
				{
					$app->enqueueMessage(Text::sprintf('COM_QUANTUMMANAGER_ERROR_WRONG_JOOMLA', $this->minimumJoomla),
						'error');

					return false;
				}

				if (!(version_compare(PHP_VERSION, $this->minimumPhp) >= 0))
				{
					$app->enqueueMessage(Text::sprintf('COM_QUANTUMMANAGER_ERROR_COMPATIBLE_PHP', $this->minimumPhp),
						'error');

					return false;
				}

				return true;
			}

			public function install(InstallerAdapter $adapter): bool
			{
				return true;
			}

			public function update(InstallerAdapter $adapter): bool
			{
				return true;
			}

			public function uninstall(InstallerAdapter $adapter): bool
			{
				return true;
			}

			public function postflight(string $type, InstallerAdapter $adapter): bool
			{
				return true;
			}
		});
	}
};