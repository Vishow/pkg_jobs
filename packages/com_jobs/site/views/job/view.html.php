<?php
/**
 * @package     Jobs
 * @subpackage  com_jobs
 * @copyright   Copyright (C) 2013 AtomTech, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * HTML Job View class for the Jobs component
 *
 * @package     Jobs
 * @subpackage  com_jobs
 * @since       3.0
 */
class JobsViewJob extends JViewLegacy
{
	protected $item;

	protected $params;

	protected $print;

	protected $state;

	protected $user;

	/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  The template file to include.
	 *
	 * @return  mixed  False on error, null otherwise.
	 *
	 * @since   3.0
	 */
	public function display($tpl = null)
	{
		// Initialiase variables.
		$app         = JFactory::getApplication();
		$user        = JFactory::getUser();
		$userId      = $user->get('id');

		// Get some data from the models
		$this->item  = $this->get('Item');
		$this->print = $app->input->getBool('print');
		$this->state = $this->get('State');
		$this->user  = $user;

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}

		// Create a shortcut for $item.
		$item = &$this->item;

		// Add router helpers.
		$item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;

		// Merge job params. If this is single-job view, menu params override job params
		// Otherwise, job params override menu item params
		$this->params = $this->state->get('params');
		$active = $app->getMenu()->getActive();
		$temp   = clone ($this->params);

		// Check to see which parameters should take priority
		if ($active)
		{
			$currentLink = $active->link;

			// If the current view is the active item and an job view for this job, then the menu item params take priority
			if (strpos($currentLink, 'view=job') && (strpos($currentLink, '&id=' . (string) $item->id)))
			{
				// $item->params are the job params, $temp are the menu item params
				// Merge so that the menu item params take priority
				$item->params->merge($temp);

				// Load layout from active query (in case it is an alternative menu item)
				if (isset($active->query['layout']))
				{
					$this->setLayout($active->query['layout']);
				}
			}
			else
			{
				// Current view is not a single job, so the job params take priority here
				// Merge the menu item params with the job params so that the job params take priority
				$temp->merge($item->params);
				$item->params = $temp;

				// Check for alternative layouts (since we are not in a single-job menu item)
				// Single-job menu item layout takes priority over alt layout for an job
				if ($layout = $item->params->get('job_layout'))
				{
					$this->setLayout($layout);
				}
			}
		}
		else
		{
			// Merge so that job params take priority
			$temp->merge($item->params);
			$item->params = $temp;

			// Check for alternative layouts (since we are not in a single-job menu item)
			// Single-job menu item layout takes priority over alt layout for an job
			if ($layout = $item->params->get('job_layout'))
			{
				$this->setLayout($layout);
			}
		}

		$offset = $this->state->get('list.offset');

		// Check the view access to the job (the model has already computed the values).
		if ($item->params->get('access-view') != true && (($item->params->get('show_noauth') != true && $user->get('guest'))))
		{
			JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return;
		}

		// Increment the hit counter of the job.
		if (!$this->params->get('intro_only') && $offset == 0)
		{
			$model = $this->getModel();
			$model->hit();
		}

		// Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->item->params->get('pageclass_sfx'));

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	protected function _prepareDocument()
	{
		// Initialiase variables.
		$app     = JFactory::getApplication();
		$menus   = $app->getMenu();
		$pathway = $app->getPathway();
		$title   = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_JOBS_JOB_DEFAULT_PAGE_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		$id = (int) @$menu->query['id'];

		// If the menu item does not concern this job.
		if ($menu && ($menu->query['option'] != 'com_jobs' || $menu->query['view'] != 'job' || $id != $this->item->id))
		{
			// If this is not a single job menu item, set the page title to the job title.
			if ($this->item->title)
			{
				$title = $this->item->title;
			}

			$path = array(
				array(
					'title' => $menu->title,
					'link' => $menu->link
				),
				array(
					'title' => $this->item->title,
					'link' => ''
				)
			);

			foreach ($path as $item)
			{
				$pathway->addItem($item['title'], $item['link']);
			}
		}
		else
		{
			$pathway->addItem($this->item->title, '');
		}

		// Check for empty title and add site name if param is set
		if (empty($title))
		{
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}

		$this->document->setTitle($title);

		// Configure the document meta-description.
		if ($this->item->metadesc)
		{
			$this->document->setDescription($this->item->metadesc);
		}
		elseif (!$this->item->metadesc && $this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		// Configure the document meta-keywords.
		if ($this->item->metakey)
		{
			$this->document->setMetadata('keywords', $this->item->metakey);
		}
		elseif (!$this->item->metakey && $this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		// Configure the document robots.
		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}

		if ($app->getCfg('MetaAuthor') == '1')
		{
			$this->document->setMetaData('author', $this->item->author);
		}

		$mdata = $this->item->metadata->toArray();

		foreach ($mdata as $k => $v)
		{
			if ($v)
			{
				$this->document->setMetadata($k, $v);
			}
		}

		// If there is a pagebreak heading or title, add it to the page title
		if (!empty($this->item->page_title))
		{
			$this->item->title = $this->item->title . ' - ' . $this->item->page_title;
			$this->document->setTitle($this->item->page_title . ' - ' . JText::sprintf('PLG_CONTENT_PAGEBREAK_PAGE_NUM', $this->state->get('list.offset') + 1));
		}

		if ($this->print)
		{
			$this->document->setMetaData('robots', 'noindex, nofollow');
		}
	}
}
