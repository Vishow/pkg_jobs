<?php
/**
 * @package     Jobs
 * @subpackage  com_jobs
 * @copyright   Copyright (C) 2012 AtomTech, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Base this model on the backend version.
require_once JPATH_ADMINISTRATOR . '/components/com_jobs/models/job.php';

/**
 * Jobs Component Job Model
 *
 * @package     Jobs
 * @subpackage  com_jobs
 * @since       3.0
 */
class JobsModelJobForm extends JobsModelJob
{
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
		$app = JFactory::getApplication();

		// Load state from the request.
		$pk = $app->input->getInt('j_id');
		$this->setState('job.id', $pk);

		$return = $app->input->get('return', null, 'base64');
		$this->setState('return_page', base64_decode($return));

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

		$this->setState('layout', $app->input->get('layout'));
	}

	/**
	 * Method to get job data.
	 *
	 * @param   integer  $itemId  The id of the job.
	 *
	 * @return  mixed  Jobs item data object on success, false on failure.
	 */
	public function getItem($itemId = null)
	{
		// Initialiase variables.
		$itemId = (int) (!empty($itemId)) ? $itemId : $this->getState('job.id');

		// Get a row instance.
		$table = $this->getTable();

		// Attempt to load the row.
		$return = $table->load($itemId);

		// Check for a table object error.
		if ($return === false && $table->getError())
		{
			$this->setError($table->getError());
			return false;
		}

		$properties = $table->getProperties(1);
		$value = JArrayHelper::toObject($properties, 'JObject');

		// Convert param field to Registry.
		$value->params = new JRegistry;
		$value->params->loadString($value->params);

		// Compute selected asset permissions.
		$user   = JFactory::getUser();
		$userId = $user->get('id');
		$asset  = 'com_jobs.job.' . $value->id;

		// Check general edit permission first.
		if ($user->authorise('core.edit', $asset))
		{
			$value->params->set('access-edit', true);
		}
		// Now check if edit.own is available.
		elseif (!empty($userId) && $user->authorise('core.edit.own', $asset))
		{
			// Check for a valid user and that they are the owner.
			if ($userId == $value->created_by)
			{
				$value->params->set('access-edit', true);
			}
		}

		// Check edit state permission.
		if ($itemId)
		{
			// Existing item.
			$value->params->set('access-change', $user->authorise('core.edit.state', $asset));
		}
		else
		{
			$value->params->set('access-change', $user->authorise('core.edit.state', 'com_jobs'));
		}

		return $value;
	}

	/**
	 * Get the return URL.
	 *
	 * @return  string  The return URL.
	 *
	 * @since   3.0
	 */
	public function getReturnPage()
	{
		return base64_encode($this->getState('return_page'));
	}
}
