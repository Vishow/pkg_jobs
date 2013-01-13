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
 * Jobs helper.
 *
 * @package     Jobs
 * @subpackage  com_jobs
 * @since       3.0
 */
class JobsHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function addSubmenu($vName = 'cpanel')
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_JOBS_SUBMENU_CPANEL'),
			'index.php?option=com_jobs&view=cpanel',
			$vName == 'cpanel'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_JOBS_SUBMENU_JOBS'),
			'index.php?option=com_jobs&view=jobs',
			$vName == 'jobs'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_JOBS_SUBMENU_COMPANIES'),
			'index.php?option=com_jobs&view=companies',
			$vName == 'companies'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_JOBS_SUBMENU_SECTORS'),
			'index.php?option=com_jobs&view=sectors',
			$vName == 'sectors'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_JOBS_SUBMENU_BENEFITS'),
			'index.php?option=com_jobs&view=benefits',
			$vName == 'benefits'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_JOBS_SUBMENU_AREAS'),
			'index.php?option=com_jobs&view=areas',
			$vName == 'areas'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_JOBS_SUBMENU_LEVELS'),
			'index.php?option=com_jobs&view=levels',
			$vName == 'levels'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_JOBS_SUBMENU_CONTRACTS'),
			'index.php?option=com_jobs&view=contracts',
			$vName == 'contracts'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_JOBS_SUBMENU_STUDIES'),
			'index.php?option=com_jobs&view=studies',
			$vName == 'studies'
		);
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return  JObject  A JObject containing the allowed actions.
	 *
	 * @since   3.0
	 */
	public static function getActions()
	{
		$user   = JFactory::getUser();
		$result = new JObject;
		$assetName = 'com_jobs';

		$actions = JAccess::getActions($assetName, 'component');

		foreach ($actions as $action)
		{
			$result->set($action->name, $user->authorise($action->name, $assetName));
		}

		return $result;
	}

	/**
	 * Get a list of filter options for the states.
	 *
	 * @param   boolean  $filter  If true, filter the result.
	 *
	 * @return  array  An array of JHtmlOption elements.
	 *
	 * @return  3.0
	 */
	public static function getStateOptions($filter = false)
	{
		// Initialize variables.
		$options = array();

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('a.prefix AS value, a.name AS text');
		$query->from($db->quoteName('#__jobs_states') . ' AS a');
		$query->order('a.id');

		if ($filter)
		{
			// Join over the offers.
			$query->join('RIGHT', $db->quoteName('#__jobs_companies') . ' AS c ON c.address_state = a.prefix');
			$query->join('RIGHT', $db->quoteName('#__jobs_offers') . ' AS o ON o.company_id = c.id');
			$query->group('a.name');
		}

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		return $options;
	}

	/**
	 * Get a list of filter options for the sectors.
	 *
	 * @return  array  An array of JHtmlOption elements.
	 *
	 * @return  3.0
	 */
	public static function getSectorOptions()
	{
		// Initialize variables.
		$options = array();

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('a.id AS value, a.name AS text');
		$query->from($db->quoteName('#__jobs_sectors') . ' AS a');
		$query->order('a.ordering');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		return $options;
	}

	/**
	 * Get a list of filter options for the areas.
	 *
	 * @param   boolean  $filter  If true, filter the result.
	 * @param   string   $state   The state prefix.
	 *
	 * @return  array  An array of JHtmlOption elements.
	 *
	 * @return  3.0
	 */
	public static function getAreaOptions($filter = false, $state = null)
	{
		// Initialize variables.
		$options = array();

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('a.id AS value, a.name AS text');
		$query->from($db->quoteName('#__jobs_areas') . ' AS a');
		$query->order('a.ordering');

		if ($filter)
		{
			// Join over the offers.
			$query->join('RIGHT', $db->quoteName('#__jobs_offers') . ' AS o ON o.area_id = a.id');

			if (is_string($state) && $state != 'ALL')
			{
				// Join over the companies.
				$query->join('RIGHT', $db->quoteName('#__jobs_companies') . ' AS c ON c.id = o.company_id');
				$query->where('c.address_state = "' . (string) $state . '"');
			}

			$query->group('a.name');
		}

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		return $options;
	}

	/**
	 * Get a list of filter options for the levels.
	 *
	 * @return  array  An array of JHtmlOption elements.
	 *
	 * @return  3.0
	 */
	public static function getLevelOptions()
	{
		// Initialize variables.
		$options = array();

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('a.id AS value, a.name AS text');
		$query->from($db->quoteName('#__jobs_levels') . ' AS a');
		$query->order('a.ordering');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		return $options;
	}

	/**
	 * Get a list of filter options for the contracts.
	 *
	 * @return  array  An array of JHtmlOption elements.
	 *
	 * @return  3.0
	 */
	public static function getContractOptions()
	{
		// Initialize variables.
		$options = array();

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('a.id AS value, a.name AS text');
		$query->from($db->quoteName('#__jobs_contracts') . ' AS a');
		$query->order('a.ordering');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		return $options;
	}

	/**
	 * Get a list of filter options for the studies.
	 *
	 * @return  array  An array of JHtmlOption elements.
	 *
	 * @return  3.0
	 */
	public static function getStudyOptions()
	{
		// Initialize variables.
		$options = array();

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('a.id AS value, a.name AS text');
		$query->from($db->quoteName('#__jobs_studies') . ' AS a');
		$query->order('a.ordering');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		return $options;
	}

	/**
	 * Get a list of filter options for the benefits.
	 *
	 * @return  array  An array of JHtmlOption elements.
	 *
	 * @return  3.0
	 */
	public static function getBenefitOptions()
	{
		// Initialize variables.
		$options = array();

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('a.id AS value, a.name AS text');
		$query->from($db->quoteName('#__jobs_benefits') . ' AS a');
		$query->order('a.ordering');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		return $options;
	}

	/**
	 * Method to get offers benefits.
	 *
	 * @param   int  $id  The offer id to filter.
	 *
	 * @return  array
	 *
	 * @since   3.0
	 */
	public static function getOffersBenefits($id)
	{
		// Initialiase variables.
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Create the base select statement.
		$query->from($db->quoteName('#__jobs_offers_benefits') . ' AS a');
		$query->where('a.offer_id = ' . (int) $id);

		// Join over the benefits.
		$query->select('b.name AS offer_name');
		$query->join('LEFT', $db->quoteName('#__jobs_benefits') . ' AS b ON b.id = a.benefit_id');

		// Set the query and load the result.
		$db->setQuery($query);

		try
		{
			$items = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		return $items;
	}

	/**
	 * Get a list of filter options for the workdays.
	 *
	 * @return  array  An array of JHtmlOption elements.
	 *
	 * @return  3.0
	 */
	public static function getWorkdayOptions()
	{
		// Initialize variables.
		$options = array();

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('a.id AS value, a.name AS text');
		$query->from($db->quoteName('#__jobs_workdays') . ' AS a');
		$query->order('a.ordering');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		return $options;
	}

	/**
	 * Get a list of filter options for the currencies.
	 *
	 * @return  array  An array of JHtmlOption elements.
	 *
	 * @return  3.0
	 */
	public static function getCurrencyOptions()
	{
		// Initialize variables.
		$options = array();

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('a.code AS value, a.name AS text');
		$query->from($db->quoteName('#__jobs_currencies') . ' AS a');
		$query->order('a.name');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		return $options;
	}

	/**
	 * Get the status of record.
	 *
	 * @param   date  $status   The status of register.
	 * @param   int   $created  The created date.
	 * @param   date  $expiry   The expiry date.
	 *
	 * @return  string
	 *
	 * @since   3.0
	 */
	public static function getStatus($status, $created, $expiry)
	{
		// Load the parameters.
		$params = JComponentHelper::getParams('com_jobs');

		// Initialiase variables.
		$db      = JFactory::getDbo();
		$nowDate = JFactory::getDate(date('Y-m-d H:i', strtotime('now')));
		$daysNew = JFactory::getDate('+' . $params->get('days_new', 3) . ' day ' . $created);

		if ($status)
		{
			switch ($status)
			{
				case '1':
					$text  = 'renewed';
					$label = ' label-warning';
					break;

				case '2':
				default:
					$text  = 'filled';
					$label = ' label-inverse';
					break;
			}

		}
		elseif ($nowDate <= $daysNew)
		{
			$text  = 'new';
			$label = ' label-success';
		}
		elseif ($expiry <= $nowDate)
		{
			$text  = 'expired';
			$label = ' label-important';
		}
		else
		{
			return false;
		}

		$html = '<span class="label' . $label . '">' . JText::_('COM_JOBS_STATUS_' . strtoupper($text)) . '</span>';

		return $html;
	}

	/**
	 * Method to show formated salary.
	 *
	 * @param   decimal  $min   The minimum of salary.
	 * @param   decimal  $max   The maximum of salary.
	 * @param   boolean  $hide  If true, hide salary.
	 *
	 * @return  string
	 *
	 * @since   3.0
	 */
	public static function getSalary($min, $max, $hide = false)
	{
		if (!intval($min) || $hide)
		{
			return JText::_('COM_JOBS_JOB_SALARY_COMBINE');
		}

		// Get the language.
		$lang   = JFactory::getLanguage();
		$locale = $lang->getLocale();

		setlocale(LC_MONETARY, $locale[0]);

		if (intval($max))
		{
			$salary = JText::sprintf('COM_JOBS_JOB_SALARY_VALUE', money_format('%n', $min), money_format('%n', $max));
		}
		else
		{
			$salary = money_format('%n', $min);
		}

		return $salary;
	}

	/**
	 * Method to count user records in table.
	 *
	 * @param   integer  $user  The user id.
	 *
	 * @return  integer
	 *
	 * @since   3.0
	 */
	public static function countRecords($user = null)
	{
		if (empty($user))
		{
			return false;
		}

		// Initialiase variables.
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Create the base select statement.
		$query->select('COUNT(id)');
		$query->from($db->quoteName('#__jobs_offers'));
		$query->where('created_by = ' . (int) $user);

		// Set the query and load the result.
		$db->setQuery($query);
		$records = $db->loadResult();

		return $records;
	}
}
