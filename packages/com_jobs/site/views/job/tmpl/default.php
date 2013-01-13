<?php
/**
 * @package     Jobs
 * @subpackage  com_jobs
 * @copyright   Copyright (C) 2013 AtomTech, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

// Register dependent classes.
JLoader::register('JobsHelper', JPATH_ADMINISTRATOR . '/components/com_jobs/helpers/jobs.php');
JLoader::register('MaskHelper', JPATH_ADMINISTRATOR . '/components/com_jobs/helpers/mask.php');

// Create shortcuts to some parameters.
$params   = $this->item->params;
$canEdit  = $params->get('access-edit');
$user     = JFactory::getUser();
$benefits = JobsHelper::getOffersBenefits($this->item->id);

// Load the tooltip behavior.
JHtml::_('behavior.caption');
?>
<div class="jobs job-item<?php echo $this->pageclass_sfx; ?>">
	<?php if ($this->params->get('show_page_heading', 1)): ?>
		<div class="page-header">
			<h1>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			</h1>
		</div>
	<?php endif; ?>

	<?php if (!$this->print): ?>
		<?php if ($canEdit || $params->get('show_print_icon', 1) || $params->get('show_email_icon', 1)): ?>
			<div class="btn-group pull-right">
				<?php if ($params->get('show_email_icon', 1)): ?>
					<?php echo JHtml::_('icon.email', $this->item, $params); ?>
				<?php endif; ?>
				<?php if ($params->get('show_print_icon', 1)): ?>
					<?php echo JHtml::_('icon.print_popup', $this->item, $params); ?>
				<?php endif; ?>
				<?php if ($canEdit): ?>
					<?php echo JHtml::_('icon.edit', $this->item, $params); ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	<?php else : ?>
		<div class="pull-right">
			<?php echo JHtml::_('icon.print_screen', $this->item, $params); ?>
		</div>
	<?php endif; ?>

	<div class="page-header">
		<h2>
			<?php echo $this->escape($this->item->title); ?>
			<span class="label"><?php echo JText::plural('COM_JOBS_JOB_OFFERS', $this->item->number); ?></span>
			<?php echo JobsHelper::getStatus($this->item->status, $this->item->created, $this->item->expiry_date); ?>
		</h2>
	</div>

	<div class="row-fluid">
		<div class="span6">
			<h3><i class="icon-tasks"></i> <?php echo JText::_('COM_JOBS_GENERAL_INFORMATION'); ?></h3>
			<table class="table table-bordered table-striped table-hover">
				<tbody>
					<?php if ($this->item->company_name): ?>
						<tr>
							<th width="160"><?php echo JText::_('COM_JOBS_JOB_COMPANY'); ?></th>
							<td>
								<a href="#"><?php echo $this->escape($this->item->company_name); ?></a><br />
								<small><?php echo JText::sprintf('COM_JOBS_JOB_CITY_STATE', $this->item->company_city, $this->item->company_state); ?></small>
								<?php if ($this->item->company_phone): ?>
									<br /><?php echo JText::sprintf('COM_JOBS_JOB_PHONE', MaskHelper::mask($this->item->company_phone, 'phone')); ?>
								<?php endif; ?>
							</td>
						</tr>
					<?php endif; ?>
					<?php if ($this->item->area_name): ?>
						<tr>
							<th><?php echo JText::_('COM_JOBS_JOB_PROFESSIONAL_AREA'); ?></th>
							<td><?php echo $this->escape($this->item->area_name); ?></td>
						</tr>
					<?php endif; ?>
					<?php if ($this->item->level_name): ?>
						<tr>
							<th><?php echo JText::_('COM_JOBS_JOB_HIERARCHICAL_LEVEL'); ?></th>
							<td><?php echo $this->escape($this->item->level_name); ?></td>
						</tr>
					<?php endif; ?>
					<?php if ($this->item->contract_name): ?>
						<tr>
							<th><?php echo JText::_('COM_JOBS_JOB_CONTRACT_TYPE'); ?></th>
							<td><?php echo $this->escape($this->item->contract_name); ?></td>
						</tr>
					<?php endif; ?>
					<?php if ($this->item->workday_name): ?>
						<tr>
							<th><?php echo JText::_('COM_JOBS_JOB_WORKDAY'); ?></th>
							<td><?php echo $this->escape($this->item->workday_name); ?></td>
						</tr>
					<?php endif; ?>
					<tr>
						<th><?php echo JText::_('COM_JOBS_JOB_SALARY'); ?></th>
						<td><?php echo JobsHelper::getSalary($this->item->salary_min, $this->item->salary_max, $this->item->salary_hide)?></td>
					</tr>
					<?php if ($this->item->disabled): ?>
						<tr>
							<th><?php echo JText::_('COM_JOBS_DISABLED_PEOPLE'); ?></th>
							<td><?php echo JText::_('JYES'); ?></td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
			<h3><i class="icon-check"></i> <?php echo JText::_('COM_JOBS_JOB_REQUIREMENTS'); ?></h3>
			<ol>
				<?php if ($this->item->study_name): ?>
					<li><?php echo JText::sprintf('COM_JOBS_EDUCATION_LEVEL', $this->item->study_name); ?></li>
				<?php endif; ?>
			</ol>
		</div>
		<div class="span6">
			<?php if ($this->item->description): ?>
				<h3><i class="icon-book"></i> <?php echo JText::_('JGLOBAL_DESCRIPTION'); ?></h3>
				<div class="well well-small">
					<?php echo $this->item->description; ?>
				</div>
			<?php endif; ?>
			<?php if ($benefits): ?>
				<h3><i class="icon-flag"></i> <?php echo JText::_('COM_JOBS_HEADING_ADDITIONAL_BENEFITS'); ?></h3>
				<p>
					<?php foreach ($benefits as $benefit): ?>
						<span class="label"><?php echo $this->escape($benefit->offer_name); ?></span>
					<?php endforeach; ?>
				</p>
			<?php endif; ?>
		</div>
	</div>
</div>
