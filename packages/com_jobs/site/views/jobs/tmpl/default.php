<?php
/**
 * @package     Jobs
 * @subpackage  com_jobs
 * @copyright   Copyright (C) 2013 AtomTech, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Register dependent classes.
JLoader::register('JobsHelper', JPATH_ADMINISTRATOR . '/components/com_jobs/helpers/jobs.php');

// Get the current user object.
$user = JFactory::getUser();

// Get the number of registers.
$count = JobsHelper::countRecords($user->id);
?>
<div class="jobs jobs-list<?php echo $this->pageclass_sfx; ?>">
	<div class="btn-group pull-right">
		<a href="<?php echo JobsHelperRoute::getJobFormRoute(0); ?>" class="btn"><i class="icon-suitcase"></i> <?php echo JText::_('COM_JOBS_JOB_REGISTER'); ?></a>
		<?php if (!$user->guest && $count): ?>
			<a href="<?php echo JRoute::_('index.php?option=com_jobs&view=jobs&my_ads=1'); ?>" class="btn"><i class="icon-flag"></i> <?php echo JText::_('COM_JOBS_MY_ADS'); ?> (<?php echo $count; ?>)</a>
		<?php endif; ?>
	</div>

	<?php if ($this->params->get('show_page_heading')): ?>
		<div class="page-header">
			<h1>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			</h1>
		</div>
	<?php endif; ?>

	<div class="row-fluid">
		<?php echo $this->loadTemplate('items'); ?>
	</div>

	<?php // Add pagination links. ?>
	<?php if (!empty($this->items)): ?>
		<?php if (($this->params->def('show_pagination', 2) == 1 || ($this->params->get('show_pagination') == 2)) && ($this->pagination->pagesTotal > 1)): ?>
			<div class="pagination">
				<?php if ($this->params->def('show_pagination_results', 1)): ?>
					<p class="counter pull-right">
						<?php echo $this->pagination->getPagesCounter(); ?>
					</p>
				<?php endif; ?>
				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>
</div>
