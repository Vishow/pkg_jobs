<?php
/**
 * @package     Jobs
 * @subpackage  com_jobs
 * @copyright   Copyright (C) 2012 AtomTech, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('checkboxes');

/**
 * Benefit Field class for the Jobs.
 *
 * @package     Jobs
 * @subpackage  com_jobs
 * @since       3.0
 */
class JFormFieldBenefit extends JFormFieldCheckboxes
{
	/**
	 * The form field type.
	 *
	 * @var     string
	 * @since   3.0
	 */
	protected $type = 'Benefit';

	/**
	 * Flag to tell the field to always be in multiple values mode.
	 *
	 * @var     boolean
	 * @since   3.0
	 */
	protected $forceMultiple = true;

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
		$options = JobsHelper::getBenefitOptions();

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
