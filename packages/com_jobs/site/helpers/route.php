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
 * Jobs Component Route Helper
 *
 * @static
 * @package     Jobs
 * @subpackage  com_jobs
 * @since       3.0
 */
abstract class JobsHelperRoute
{
	protected static $lookup;

	/**
	 * Method to get a route configuration for the job view.
	 *
	 * @param   int  $id  The route of the job.
	 *
	 * @return  string
	 *
	 * @since   3.0
	 */
	public static function getJobRoute($id)
	{
		// Initialiase variables.
		$needles = array(
			'job' => array((int) $id)
		);

		// Create the link
		$link = 'index.php?option=com_jobs&view=job&id=' . $id;

		if ($item = self::_findItem($needles))
		{
			$link .= '&Itemid=' . $item;
		}
		elseif ($item = self::_findItem(array('jobs' => array(0))))
		{
			$link .= '&Itemid=' . $item;
		}
		elseif ($item = self::_findItem())
		{
			$link .= '&Itemid=' . $item;
		}

		return $link;
	}

	/**
	 * Method to get a route configuration for the company view.
	 *
	 * @param   int  $id  The route of the company.
	 *
	 * @return  string
	 *
	 * @since   3.0
	 */
	public static function getCompanyRoute($id)
	{
		// Initialiase variables.
		$needles = array(
			'company' => array((int) $id)
		);

		// Create the link
		$link = 'index.php?option=com_jobs&view=company&id=' . $id;

		if ($item = self::_findItem($needles))
		{
			$link .= '&Itemid=' . $item;
		}
		elseif ($item = self::_findItem())
		{
			$link .= '&Itemid=' . $item;
		}

		return $link;
	}

	/**
	 * Method to get a route configuration for the job form view.
	 *
	 * @param   int     $id      The id of the job.
	 * @param   string  $return  The return page variable.
	 *
	 * @return  string
	 *
	 * @since   3.0
	 */
	public static function getJobFormRoute($id, $return = null)
	{
		// Create the link.
		if ($id)
		{
			$link = 'index.php?option=com_jobs&task=job.edit&j_id=' . $id;
		}
		else
		{
			$link = 'index.php?option=com_jobs&task=job.add&j_id=0';
		}

		if ($return)
		{
			$link .= '&return=' . $return;
		}

		return $link;
	}

	/**
	 * Method to find the item.
	 *
	 * @param   array  $needles  The needles to find.
	 *
	 * @return  null
	 *
	 * @since   3.0
	 */
	protected static function _findItem($needles = null)
	{
		// Initialiase variables.
		$app   = JFactory::getApplication();
		$menus = $app->getMenu('site');

		// Prepare the reverse lookup array.
		if (self::$lookup === null)
		{
			self::$lookup = array();

			$component = JComponentHelper::getComponent('com_jobs');
			$items     = $menus->getItems('component_id', $component->id);

			if ($items)
			{
				foreach ($items as $item)
				{
					if (isset($item->query) && isset($item->query['view']))
					{
						$view = $item->query['view'];

						if (!isset(self::$lookup[$view]))
						{
							self::$lookup[$view] = array();
						}

						if (isset($item->query['id']))
						{
							self::$lookup[$view][$item->query['id']] = $item->id;
						}
						else
						{
							self::$lookup[$view][] = $item->id;
						}
					}
				}
			}
		}

		if ($needles)
		{
			foreach ($needles as $view => $ids)
			{
				if (isset(self::$lookup[$view]))
				{
					foreach ($ids as $id)
					{
						if (isset(self::$lookup[$view][(int) $id]))
						{
							return self::$lookup[$view][(int) $id];
						}
					}
				}
			}
		}
		else
		{
			$active = $menus->getActive();

			if ($active && $active->component == 'com_jobs')
			{
				return $active->id;
			}
		}

		return null;
	}
}
