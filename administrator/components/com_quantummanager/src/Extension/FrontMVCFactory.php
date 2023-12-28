<?php namespace Joomla\Component\QuantumManager\Administrator\Extension;

// phpcs:disable PSR1.Files.SideEffects
use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Factory\MVCFactory;
use Joomla\Component\QuantumManager\Administrator\Helper\QuantummanagerHelper;
use Joomla\Input\Input;

\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Factory to create MVC objects based on a namespace. Note that in an API Application model and table objects will be
 * created from their administrator counterparts.
 *
 * @since  4.0.0
 */
final class FrontMVCFactory extends MVCFactory
{
	/**
	 * Method to load and return a model object.
	 *
	 * @param   string  $name    The name of the model.
	 * @param   string  $prefix  Optional model prefix.
	 * @param   array   $config  Optional configuration array for the model.
	 *
	 * @return  \Joomla\CMS\MVC\Model\ModelInterface  The model object
	 *
	 * @throws  \Exception
	 * @since   4.0.0
	 */
	public function createController($name, $prefix, array $config, CMSApplicationInterface $app, Input $input)
	{
		if (!(int) QuantummanagerHelper::getParamsComponentValue('front', 0))
		{
			throw new \InvalidArgumentException(Text::sprintf('JLIB_APPLICATION_ERROR_NOT_ACCESS'));
		}

		// проверяем что пользователь авторизован
		if (Factory::getUser()->id === 0)
		{
			throw new \InvalidArgumentException(Text::sprintf('JLIB_APPLICATION_ERROR_NOT_ACCESS'));
		}

		$controller = parent::createController($name, $prefix, $config, $app, $input);

		if (!$controller)
		{
			$controller = parent::createController($name, 'Administrator', $config, $app, $input);
		}

		return $controller;
	}

}
