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
 * View class for a list of studies.
 *
 * @package     Jobs
 * @subpackage  com_jobs
 * @since       3.0
 */
class JobsViewStudies extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  A template file to load. [optional]
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @since   3.0
	 */
	public function display($tpl = null)
	{
		// Initialise variables.
		$this->state      = $this->get('State');
		$this->pagination = $this->get('Pagination');
		$this->items      = $this->get('Items');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Load the submenu.
		JobsHelper::addSubmenu('studies');

		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	protected function addToolbar()
	{
		// Include dependancies.
		require_once JPATH_COMPONENT . '/helpers/jobs.php';

		// Initialise variables.
		$state = $this->get('State');
		$canDo = JobsHelper::getActions();
		$user  = JFactory::getUser();

		JToolbarHelper::title(JText::_('COM_JOBS_MANAGER_STUDIES'), 'studies.png');

		if ($canDo->get('core.create'))
		{
			JToolbarHelper::addNew('study.add');
		}

		if ($canDo->get('core.edit'))
		{
			JToolbarHelper::editList('study.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::publish('studies.publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('studies.unpublish', 'JTOOLBAR_UNPUBLISH', true);

			JToolbarHelper::archiveList('studies.archive');
		}

		if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('', 'studies.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::trash('studies.trash');
		}

		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_jobs');
		}

		JToolBarHelper::help('studies', $com = true);

		JHtmlSidebar::setAction('index.php?option=com_jobs&view=studies');

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_state',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true)
		);
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.state' => JText::_('JSTATUS'),
			'a.name' => JText::_('COM_JOBS_HEADING_NAME'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
