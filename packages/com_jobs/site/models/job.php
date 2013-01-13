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
 * Jobs Component Model for a Job record
 *
 * @package     Jobs
 * @subpackage  com_jobs
 * @since       3.0
 */
class JobsModelJob extends JModelItem
{
	/**
	 * Model context string.
	 *
	 * @access  protected
	 * @var     string
	 */
	protected $_context = 'com_jobs.job';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	protected function populateState()
	{
		// Initialiase variables.
		$app    = JFactory::getApplication();
		$params = $app->getParams();

		// Load the object state.
		$id = $app->input->getInt('id');
		$this->setState('job.id', $id);

		// Load the parameters.
		$this->setState('params', $params);

		// Get the current user object.
		$user = JFactory::getUser();

		if ((!$user->authorise('core.edit.state', 'com_jobs')) &&  (!$user->authorise('core.edit', 'com_jobs')))
		{
			$this->setState('filter.published', 1);
			$this->setState('filter.archived', 2);
		}
	}

	/**
	 * Method to get job data.
	 *
	 * @param   integer  $pk  The id of the job.
	 *
	 * @return  mixed  Menu item data object on success, false on failure.
	 *
	 * @since   3.0
	 */
	public function &getItem($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('job.id');

		if ($this->_item === null)
		{
			$this->_item = array();
		}

		if (!isset($this->_item[$pk]))
		{
			try
			{
				$db = $this->getDbo();
				$query = $db->getQuery(true);

				$query->select($this->getState('item.select', 'a.*'));
				$query->from($db->quoteName('#__jobs_offers') . ' AS a');

				// Join on user table.
				$query->select('u.name AS author');
				$query->join('LEFT', '#__users AS u on u.id = a.created_by');

				$query->where('a.id = ' . (int) $pk);

				// Join over the companies.
				$query->select('com.name AS company_name, com.phone AS company_phone, com.email AS company_email, com.address_city AS company_city, com.address_state AS company_state');
				$query->join('LEFT', $db->quoteName('#__jobs_companies') . ' AS com ON com.id = a.company_id');

				// Join over the areas.
				$query->select('ar.name AS area_name');
				$query->join('LEFT', $db->quoteName('#__jobs_areas') . ' AS ar ON ar.id = a.area_id');

				// Join over the levels.
				$query->select('l.name AS level_name');
				$query->join('LEFT', $db->quoteName('#__jobs_levels') . ' AS l ON l.id = a.level_id');

				// Join over the contracts.
				$query->select('co.name AS contract_name');
				$query->join('LEFT', $db->quoteName('#__jobs_contracts') . ' AS co ON co.id = a.contract_id');

				// Join over the workdays.
				$query->select('w.name AS workday_name');
				$query->join('LEFT', $db->quoteName('#__jobs_workdays') . ' AS w ON w.id = a.workday_id');

				// Join over the studies.
				$query->select('s.name AS study_name');
				$query->join('LEFT', $db->quoteName('#__jobs_studies') . ' AS s ON s.id = a.study_id');

				// Filter by start and end dates.
				$nullDate = $db->quote($db->getNullDate());
				$nowDate = $db->quote(JFactory::getDate()->toSql());

				// Filter by published state.
				$published = $this->getState('filter.published');
				$archived = $this->getState('filter.archived');

				if (is_numeric($published))
				{
					$query->where('(a.state = ' . (int) $published . ' OR a.state =' . (int) $archived . ')');
				}

				$db->setQuery($query);

				$data = $db->loadObject();

				if (empty($data))
				{
					throw new Exception(JText::_('COM_JOBS_ERROR_JOB_NOT_FOUND'), 404);
				}

				// Check for published state if filter set.
				if (((is_numeric($published)) || (is_numeric($archived))) && (($data->state != $published) && ($data->state != $archived)))
				{
					JError::raiseError(404, JText::_('COM_JOBS_ERROR_JOB_NOT_FOUND'));
				}

				// Convert parameter fields to objects.
				$registry = new JRegistry;
				$registry->loadString($data->params);
				$data->params = clone $this->getState('params');
				$data->params->merge($registry);

				$registry = new JRegistry;
				$registry->loadString($data->metadata);
				$data->metadata = $registry;

				// Compute selected asset permissions.
				$user = JFactory::getUser();

				// Technically guest could edit an job, but lets not check that to improve performance a little.
				if (!$user->get('guest'))
				{
					$userId = $user->get('id');
					$asset  = 'com_jobs.job.' . $data->id;

					// Check general edit permission first.
					if ($user->authorise('core.edit', $asset))
					{
						$data->params->set('access-edit', true);
					}
					// Now check if edit.own is available.
					elseif (!empty($userId) && $user->authorise('core.edit.own', $asset))
					{
						// Check for a valid user and that they are the owner.
						if ($userId == $data->created_by)
						{
							$data->params->set('access-edit', true);
						}
					}
				}

				// Compute view access permissions.
				if ($access = $this->getState('filter.access'))
				{
					// If the access filter has been set, we already know this user can view.
					$data->params->set('access-view', true);
				}
				else
				{
					// If no access filter is set, the layout takes some responsibility for display of limited information.
					$user = JFactory::getUser();
					$groups = $user->getAuthorisedViewLevels();
					$data->params->set('access-view', in_array($data->access, $groups));
				}

				$this->_item[$pk] = $data;
			}
			catch (Exception $e)
			{
				if ($e->getCode() == 404)
				{
					// Need to go thru the error handler to allow Redirect to work.
					JError::raiseError(404, $e->getMessage());
				}
				else
				{
					$this->setError($e);
					$this->_item[$pk] = false;
				}
			}
		}

		return $this->_item[$pk];
	}

	/**
	 * Increment the hit counter for the job.
	 *
	 * @param   int  $pk  Optional primary key of the item to increment.
	 *
	 * @return  boolean  True if successful; false otherwise and internal error set.
	 *
	 * @since   3.0
	 */
	public function hit($pk = 0)
	{
		// Initialiase variables.
		$input    = JFactory::getApplication()->input;
		$hitcount = $input->getInt('hitcount', 1);

		if ($hitcount)
		{
			$pk = (!empty($pk)) ? $pk : (int) $this->getState('job.id');
			$db = $this->getDbo();

			$db->setQuery(
				'UPDATE #__jobs_offers' .
				' SET hits = hits + 1' .
				' WHERE id = ' . (int) $pk
			);

			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->setError($e->getMessage());
				return false;
			}
		}

		return true;
	}
}
