<?php namespace Joomla\Component\Multisite\Administrator\Dispatcher;

\defined('_JEXEC') or die;

use Joomla\CMS\Access\Exception\NotAllowed;
use Joomla\CMS\Dispatcher\ComponentDispatcher;

class Dispatcher extends ComponentDispatcher
{
	protected function checkAccess()
	{
		// Check the user has permission to access this component if in the backend
		if (!$this->app->getIdentity()->authorise('core.manage', 'com_quantummanager'))
		{
			throw new NotAllowed($this->app->getLanguage()->_('JERROR_ALERTNOAUTHOR'), 403);
		}
	}
}
