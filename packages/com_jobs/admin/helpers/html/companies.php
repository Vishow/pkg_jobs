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
 * Utility class working with companies
 *
 * @package     Jobs
 * @subpackage  com_jobs
 * @since       3.0
 */
abstract class JHtmlCompanies
{
	/**
	 * Displays a batch widget for moving or copying items.
	 *
	 * @param   string  $extension  The extension.
	 *
	 * @return  string  The necessary HTML for the widget.
	 *
	 * @since   3.0
	 */
	public static function item($extension)
	{
		// Create the copy/move options.
		$options = array(JHtml::_('select.option', 'c', JText::_('JLIB_HTML_BATCH_COPY')),
			JHtml::_('select.option', 'm', JText::_('JLIB_HTML_BATCH_MOVE')));

		// Create the batch selector to move or copy.
		$lines = array('<label id="batch-choose-action-lbl" for="batch-choose-action">', JText::_('COM_JOBS_BATCH_MENU_LABEL'), '</label>',
			'<div id="batch-move-copy" class="control-group radio">',
			JHtml::_('select.radiolist', $options, 'batch[move_copy]', '', 'value', 'text', 'm'), '</div><hr />');

		return implode("\n", $lines);
	}
}
