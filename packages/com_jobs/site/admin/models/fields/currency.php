<?php
/**
 * @package     Jobs
 * @subpackage  com_jobs
 * @copyright   Copyright (C) 2013 AtomTech, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * Currency Field class for the Jobs.
 *
 * @package     Jobs
 * @subpackage  com_jobs
 * @since       3.0
 */
class JFormFieldCurrency extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var     string
	 * @since   3.0
	 */
	protected $type = 'Currency';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   3.0
	 */
	protected function getOptions()
	{
		// Initialiase variables.
		$options = JobsHelper::getCurrencyOptions();

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
