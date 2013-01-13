<?php
/**
 * @package     Jobs
 * @subpackage  com_jobs
 * @copyright   Copyright (C) 2012 AtomTech, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Jobs Component Controller
 *
 * @package     Jobs
 * @subpackage  com_jobs
 * @since       3.0
 */
class JobsController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached.
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController  This object to support chaining.
	 *
	 * @since   3.0
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// Initialise variables.
		$cachable = true;
		$user = JFactory::getUser();

		// Set the default view name and format from the Request.
		// Note we are using j_id to avoid collisions with the router and the return page.
		$id    = $this->input->getInt('j_id');
		$vName = $this->input->get('view', 'jobs');
		$this->input->set('view', $vName);

		if ($user->get('id') ||($this->input->getMethod() == 'POST' && $vName = 'jobs'))
		{
			$cachable = false;
		}

		$safeurlparams = array(
			'id'               => 'INT',
			'limit'            => 'UINT',
			'limitstart'       => 'UINT',
			'filter_order'     => 'CMD',
			'filter_order_Dir' => 'CMD',
			'lang'             => 'CMD'
		);

		// Check for edit form.
		if (($vName == 'companyform' && !$this->checkEditId('com_jobs.edit.company', $id))
			|| ($vName == 'jobform' && !$this->checkEditId('com_jobs.edit.job', $id)))
		{
			// Somehow the person just went to the form - we don't allow that.
			return JError::raiseError(403, JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
		}

		return parent::display($cachable, $safeurlparams);
	}

	/**
	 * Method to prepare search.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function search()
	{
		// Slashes cause errors, <> get stripped anyway later on. # causes problems.
		$badchars = array('#', '>', '<', '\\');
		$searchword = trim(str_replace($badchars, '', $this->input->getString('searchword', null, 'post')));

		// If searchword enclosed in double quotes, strip quotes and do exact match.
		if (substr($searchword, 0, 1) == '"' && substr($searchword, -1) == '"')
		{
			$post['searchword'] = substr($searchword, 1, -1);
			$this->input->set('searchphrase', 'exact');
		}
		else
		{
			$post['searchword'] = $searchword;
		}

		$post['ordering']     = $this->input->getWord('ordering', null, 'post');
		$post['searchphrase'] = $this->input->getWord('searchphrase', 'all', 'post');
		$post['limit']        = $this->input->getUInt('limit', null, 'post');
		$post['area']         = $this->input->getString('area', 'all', 'post');
		$post['state']        = $this->input->getString('state', 'all', 'post');

		if ($post['limit'] === null)
		{
			unset($post['limit']);
		}

		$areas = $this->input->post->get('areas', null, 'array');

		if ($areas)
		{
			foreach ($areas as $area)
			{
				$post['areas'][] = JFilterInput::getInstance()->clean($area, 'cmd');
			}
		}

		// Set Itemid id for links from menu.
		$app   = JFactory::getApplication();
		$menu  = $app->getMenu();
		$items = $menu->getItems('link', 'index.php?option=com_jobs&view=jobs');

		if (isset($items[0]))
		{
			$post['Itemid'] = $items[0]->id;
		}
		elseif ($this->input->getInt('Itemid') > 0)
		{
			// Use Itemid from requesting page only if there is no existing menu
			$post['Itemid'] = $this->input->getInt('Itemid');
		}

		unset($post['task']);
		unset($post['submit']);

		$uri = JURI::getInstance();
		$uri->setQuery($post);
		$uri->setVar('option', 'com_jobs');

		$this->setRedirect(JRoute::_('index.php' . $uri->toString(array('query', 'fragment')), false));
	}
}
