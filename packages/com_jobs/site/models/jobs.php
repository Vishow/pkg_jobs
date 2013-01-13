<?php
/**
 * @package     Jobs
 * @subpackage  com_jobs
 * @copyright   Copyright (C) 2012 AtomTech, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Register dependent classes.
JLoader::register('JobsHelper', JPATH_ADMINISTRATOR . '/components/com_jobs/helpers/jobs.php');

/**
 * This models supports retrieving lists of jobs.
 *
 * @package     Jobs
 * @subpackage  com_jobs
 * @since       3.0
 */
class JobsModelJobs extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   3.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'alias', 'a.alias',
				'name', 'c.name', 'ar.name',
				'address_state', 'c.address_state',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'state', 'a.state',
				'access', 'a.access', 'access_level',
				'created', 'a.created',
				'created_by', 'a.created_by',
				'hits', 'a.hits',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Get the configuration options.
		$app    = JFactory::getApplication();
		$input  = $app->input;
		$params = JComponentHelper::getParams('com_jobs');
		$user   = JFactory::getUser();

		// Optional filter text.
		$search = urldecode($input->getString('searchword'));
		$this->setState('filter.search', $search);

		// List state information.
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'uint');
		$this->setState('list.limit', $limit);

		$limitstart = $input->get('limitstart', 0, 'uint');
		$this->setState('list.start', $limitstart);

		// Load the ordering.
		$orderCol = $input->get('filter_order', 'a.created');

		// Load the area.
		$areaId = $input->get('area', null, 'uint');
		$this->setState('filter.area_id', $areaId);

		// Load the state.
		$statePrefix = $input->get('state', $params->get('default_state', 'all'), 'word');
		$this->setState('filter.state_prefix', strtoupper($statePrefix));

		// Load only my ads.
		$myAds = $input->get('my_ads', null, 'uint');
		$this->setState('filter.my_ads', $myAds);

		if (!in_array($orderCol, $this->filter_fields))
		{
			$orderCol = 'a.created';
		}

		$this->setState('list.ordering', $orderCol);

		// Load the direction.
		$listOrder = $input->get('filter_order_Dir', 'DESC');

		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', '')))
		{
			$listOrder = 'DESC';
		}

		$this->setState('list.direction', $listOrder);

		// Load the parameters.
		$this->setState('params', $params);

		// Process show_noauth parameter.
		if (!$params->get('show_noauth'))
		{
			$this->setState('filter.access', true);
		}
		else
		{
			$this->setState('filter.access', false);
		}

		if ((!$user->authorise('core.edit.state', 'com_jobs')) && (!$user->authorise('core.edit', 'com_jobs')))
		{
			// Limit to published for people who can't edit or edit.state.
			$this->setState('filter.state', 1);

			// Filter by start and end dates.
			$this->setState('filter.publish_date', true);
		}

		// Load the layout.
		$this->setState('layout', $input->get('layout'));
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   3.0
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.access');
		$id .= ':' . $this->getState('filter.state');
		$id .= ':' . $this->getState('filter.area_id');
		$id .= ':' . $this->getState('filter.state_prefix');
		$id .= ':' . $this->getState('filter.my_ads');

		return parent::getStoreId($id);
	}

	/**
	 * Method to get a list of items.
	 *
	 * @return  mixed  An array of objects on success, false on failure.
	 *
	 * @since   3.0
	 */
	public function getItems()
	{
		// Initialiase variables.
		$items  = parent::getItems();
		$user   = JFactory::getUser();
		$userId = $user->get('id');
		$guest  = $user->get('guest');
		$groups = $user->getAuthorisedViewLevels();
		$input  = JFactory::getApplication()->input;

		// Convert the parameter fields into objects.
		foreach ($items as &$item)
		{
			$item->params = clone $this->getState('params');

			// Get display date.
			switch ($item->params->get('list_show_date'))
			{
				case 'modified':
					$item->displayDate = $item->modified;
					break;

				case 'published':
					$item->displayDate = ($item->publish_up == 0) ? $item->created : $item->publish_up;
					break;

				default:
				case 'created':
					$item->displayDate = $item->created;
					break;
			}

			// Compute the asset access permissions.
			// Technically guest could edit an job, but lets not check that to improve performance a little.
			if (!$guest)
			{
				$asset = 'com_jobs.job.' . $item->id;

				// Check general edit permission first.
				if ($user->authorise('core.edit', $asset))
				{
					$item->params->set('access-edit', true);
				}
				// Now check if edit.own is available.
				elseif (!empty($userId) && $user->authorise('core.edit.own', $asset))
				{
					// Check for a valid user and that they are the owner.
					if ($userId == $item->created_by)
					{
						$item->params->set('access-edit', true);
					}
				}
			}

			$access = $this->getState('filter.access');

			if ($access)
			{
				// If the access filter has been set, we already have only the jobs this user can view.
				$item->params->set('access-view', true);
			}
			else
			{
				// If no access filter is set, the layout takes some responsibility for display of limited information.
				if ($item->catid == 0 || $item->category_access === null)
				{
					$item->params->set('access-view', in_array($item->access, $groups));
				}
				else
				{
					$item->params->set('access-view', in_array($item->access, $groups) && in_array($item->category_access, $groups));
				}
			}
		}

		return $items;
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  string  An SQL query
	 *
	 * @since   3.0
	 */
	protected function getListQuery()
	{
		// Initialiase variables.
		$user   = JFactory::getUser();
		$groups = implode(', ', $user->getAuthorisedViewLevels());

		// Create a new query object.
		$db     = $this->getDbo();
		$query  = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.status, a.title, a.alias, a.disabled, a.expiry_date, a.checked_out, a.checked_out_time'
				. ', a.hits'
				. ', a.state, a.access, a.params, a.created, a.created_by, a.created_by_alias'
			)
		);
		$query->from($db->quoteName('#__jobs_offers') . ' AS a');

		// Join over the companies.
		$query->join('LEFT', $db->quoteName('#__jobs_companies') . ' AS co ON co.id = a.company_id');

		// Join over the companies.
		$query->select('c.name AS company_name, c.address_state AS state_name');
		$query->join('LEFT', $db->quoteName('#__jobs_companies') . ' AS c ON c.id = a.company_id');

		// Join over the areas.
		$query->select('ar.name AS area_name');
		$query->join('LEFT', $db->quoteName('#__jobs_areas') . ' AS ar ON ar.id = a.area_id');

		// Join over the users for the author and modified_by names.
		$query->select("CASE WHEN a.created_by_alias > ' ' THEN a.created_by_alias ELSE ua.name END AS author");
		$query->select("ua.email AS author_email");

		$query->join('LEFT', '#__users AS ua ON ua.id = a.created_by');
		$query->join('LEFT', '#__users AS uam ON uam.id = a.modified_by');

		// Filter by area.
		$areaId = $this->getState('filter.area_id');

		if ($areaId)
		{
			$query->where('a.area_id = ' . (int) $areaId);
		}

		// Filter by state prefix.
		$statePrefix = $this->getState('filter.state_prefix');

		if ($statePrefix != 'ALL')
		{
			$query->where('co.address_state = "' . (string) $statePrefix . '"');
		}

		// Filter by my_ads.
		$myAds = $this->getState('filter.my_ads');

		if ($myAds == 1)
		{
			$query->where('a.created_by = ' . (int) $user->id);
		}

		// Filter by access level.
		if ($access = $this->getState('filter.access'))
		{
			$query->where('a.access IN (' . $groups . ')');
		}

		// Filter by state.
		$state = $this->getState('filter.state');

		if (is_numeric($state))
		{
			$query->where('a.state = ' . (int) $state);
		}

		// Filter by expiry dates.
		// $nullDate = $db->Quote($db->getNullDate());
		// $nowDate = $db->Quote(JFactory::getDate()->toSql());

		// if ($this->getState('filter.publish_date'))
		// {
		// 	$query->where('(a.expiry_date = ' . $nullDate . ' OR a.expiry_date >= ' . $nowDate . ')');
		// }

		// Filter by search in title.
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$search = $db->Quote('%' . $db->escape($search, true) . '%');
			$query->where('(a.title LIKE ' . $search . ')');
		}

		// Add the list ordering clause.
		$orderCol = $this->getState('list.ordering', 'a.created');
		$query->order($db->escape($orderCol) . ' ' . $db->escape($this->getState('list.direction', 'DESC')));

		return $query;
	}

	/**
	 * Method to get lists for filters.
	 *
	 * @return  array
	 *
	 * @since   3.0
	 */
	public function getLists()
	{
		// Initialiase variables.
		$statePrefix = $this->getState('filter.state_prefix');
		$lists = array();

		// Built area list.
		$areas = array();
		$areas[] = JHtml::_('select.option', 'all', JText::_('COM_JOBS_SELECT_AREAS'));

		foreach (JobsHelper::getAreaOptions(true, $statePrefix) as $area)
		{
			$areas[] = JHtml::_('select.option', $area->value, $area->text);
		}

		$lists['area'] = JHtml::_('select.genericlist', $areas, 'area', 'class="input-block-level"', 'value', 'text', $this->getState('filter.area_id'));

		// Built state list.
		$states = array();
		$states[] = JHtml::_('select.option', 'all', JText::_('COM_JOBS_SELECT_STATES'));

		foreach (JobsHelper::getStateOptions(true) as $state)
		{
			$states[] = JHtml::_('select.option', strtolower($state->value), $state->text);
		}

		$lists['state'] = JHtml::_('select.genericlist', $states, 'state', 'class="input-block-level"', 'value', 'text', strtolower($statePrefix));

		return $lists;
	}
}
