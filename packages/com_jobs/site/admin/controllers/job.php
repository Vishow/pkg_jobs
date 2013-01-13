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
 * Job controller class.
 *
 * @package     Jobs
 * @subpackage  com_jobs
 * @since       3.0
 */
class JobsControllerJob extends JControllerForm
{
	/**
	 * @var     string  The prefix to use with controller messages.
	 * @since   3.0
	 */
	protected $text_prefix = 'COM_JOBS_JOB';

	/**
	 * Method to run batch operations.
	 *
	 * @param   object  $model  The model.
	 *
	 * @return  boolean  True if successful, false otherwise and internal error is set.
	 *
	 * @since   3.0
	 */
	public function batch($model = null)
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model
		$model = $this->getModel('Job', 'JobsModel', array());

		// Preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_jobs&view=jobs' . $this->getRedirectToListAppend(), false));
		return parent::batch($model);
	}
}
