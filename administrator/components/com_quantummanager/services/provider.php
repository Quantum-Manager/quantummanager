<?php \defined('_JEXEC') or die;

use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Component\Router\RouterFactoryInterface;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\CMS\HTML\Registry;
use Joomla\CMS\Mail\MailerFactoryInterface;
use Joomla\CMS\MVC\Factory\ApiMVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Router\SiteRouter;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Component\QuantumManager\Administrator\Extension\FrontMVCFactory;
use Joomla\Component\QuantumManager\Administrator\Extension\QuantumManagerComponent;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;

return new class implements ServiceProviderInterface {

	public function register(Container $container)
	{
		$container->set(
			MVCFactoryInterface::class,
			function (Container $container) {

				if (Factory::getApplication()->isClient('api'))
				{
					$factory = new ApiMVCFactory('\\Joomla\\Component\\QuantumManager');
				}
				elseif (Factory::getApplication()->isClient('site'))
				{
					$factory = new FrontMVCFactory('\\Joomla\\Component\\QuantumManager');
				}
				else
				{
					$factory = new MVCFactory('\\Joomla\\Component\\QuantumManager');
				}

				$factory->setFormFactory($container->get(FormFactoryInterface::class));
				$factory->setDispatcher($container->get(DispatcherInterface::class));
				$factory->setDatabase($container->get(DatabaseInterface::class));
				$factory->setSiteRouter($container->get(SiteRouter::class));
				$factory->setCacheControllerFactory($container->get(CacheControllerFactoryInterface::class));
				$factory->setUserFactory($container->get(UserFactoryInterface::class));
				$factory->setMailerFactory($container->get(MailerFactoryInterface::class));

				return $factory;
			}
		);

		$container->registerServiceProvider(new ComponentDispatcherFactory('\\Joomla\\Component\\QuantumManager'));
		$container->registerServiceProvider(new RouterFactory('\\Joomla\\Component\\QuantumManager'));

		$container->set(
			ComponentInterface::class,
			function (Container $container) {
				$component = new QuantumManagerComponent($container->get(ComponentDispatcherFactoryInterface::class));

				$component->setRegistry($container->get(Registry::class));
				//$component->setMVCFactory($container->get(MVCFactoryInterface::class));
				$component->setRouterFactory($container->get(RouterFactoryInterface::class));

				return $component;
			}
		);

		Factory::getApplication()->getLanguage()->load('com_quantummanager', JPATH_ADMINISTRATOR, null, true);
	}
};

