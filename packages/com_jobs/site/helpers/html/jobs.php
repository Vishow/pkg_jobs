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
 * Jobs HTML behavior class for Jobs.
 *
 * @package     Jobs
 * @subpackage  com_jobs
 * @since       3.0
 */
abstract class JHtmlJobs
{
	/**
	 * Method to sort a column in a grid.
	 *
	 * @param   string  $title          The link title.
	 * @param   string  $order          The order field for the column.
	 * @param   string  $direction      The current direction.
	 * @param   string  $selected       The selected ordering.
	 * @param   string  $task           An optional task override.
	 * @param   string  $new_direction  An optional direction for the new column.
	 * @param   string  $tip            An optional text shown as tooltip title instead of $title.
	 *
	 * @return  string
	 *
	 * @since   3.0
	 */
	public static function sort($title, $order, $direction = 'asc', $selected = 0, $task = null, $new_direction = 'asc', $tip = '')
	{
		// Load the tooltip behavior.
		JHtml::_('behavior.tooltip');

		// Initialiase variables.
		$direction = strtolower($direction);
		$icon      = array('caret-up', 'caret-down');
		$index     = (int) ($direction == 'desc');

		if ($order != $selected)
		{
			$direction = $new_direction;
		}
		else
		{
			$direction = ($direction == 'desc') ? 'asc' : 'desc';
		}

		$html = '<a href="#" onclick="Joomla.tableOrdering(\'' . $order . '\',\'' . $direction . '\',\'' . $task . '\');"'
			. ' class="hasTip" title="' . JText::_($tip ? $tip : $title) . '::' . JText::_('JGLOBAL_CLICK_TO_SORT_THIS_COLUMN') . '">';
		$html .= JText::_($title);

		if ($order == $selected)
		{
			$html .= ' <i class="icon-' . $icon[$index] . '"></i>';
		}

		$html .= '</a>';

		return $html;
	}
}
