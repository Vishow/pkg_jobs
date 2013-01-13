<?php
/**
 * @package     Jobs
 * @subpackage  com_jobs
 * @copyright   Copyright (C) 2012 AtomTech, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

// Load JavaScript.
JHtml::script('com_jobs/jquery.meio.mask.min.js', false, true);
JHtml::script('com_jobs/jquery.custom.js', false, true);
?>
<script type="text/javascript">
	Joomla.submitbutton = function (task) {
		if (task == 'company.cancel' || document.formvalidator.isValid(document.id('company-form'))) {
			<?php echo $this->form->getField('description')->save(); ?>
			Joomla.submitform(task, document.getElementById('company-form'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
		}
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_jobs&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="company-form" class="form-validate">
	<div class="row-fluid">
		<!-- Begin Companies -->
		<div class="span10 form-horizontal">
			<fieldset>
				<ul class="nav nav-tabs">
					<li class="active"><a href="#details" data-toggle="tab"><?php echo empty($this->item->id) ? JText::_('COM_JOBS_NEW_COMPANY') : JText::sprintf('COM_JOBS_EDIT_COMPANY', $this->item->id); ?></a></li>
					<li><a href="#address" data-toggle="tab"><?php echo JText::_('COM_JOBS_FIELDSET_ADDRESS'); ?></a></li>
					<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('JGLOBAL_FIELDSET_PUBLISHING'); ?></a></li>
					<?php $fieldSets = $this->form->getFieldsets('params');
					foreach ($fieldSets as $name => $fieldSet): ?>
						<li><a href="#params-<?php echo $name; ?>" data-toggle="tab"><?php echo JText::_($fieldSet->label); ?></a></li>
					<?php endforeach; ?>
					<?php $fieldSets = $this->form->getFieldsets('metadata');
					foreach ($fieldSets as $name => $fieldSet): ?>
						<li><a href="#metadata-<?php echo $name; ?>" data-toggle="tab"><?php echo JText::_($fieldSet->label); ?></a></li>
					<?php endforeach; ?>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="details">
						<div class="row-fluid">
							<div class="span6">
								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
									<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
								</div>
								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('company_name'); ?></div>
									<div class="controls"><?php echo $this->form->getInput('company_name'); ?></div>
								</div>
								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('cnpj'); ?></div>
									<div class="controls"><?php echo $this->form->getInput('cnpj'); ?></div>
								</div>
								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('sector_id'); ?></div>
									<div class="controls"><?php echo $this->form->getInput('sector_id'); ?></div>
								</div>
							</div>
							<div class="span6">
								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('company_size'); ?></div>
									<div class="controls"><?php echo $this->form->getInput('company_size'); ?></div>
								</div>
								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('phone'); ?></div>
									<div class="controls"><?php echo $this->form->getInput('phone'); ?></div>
								</div>
								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('email'); ?></div>
									<div class="controls"><?php echo $this->form->getInput('email'); ?></div>
								</div>
								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('website'); ?></div>
									<div class="controls"><?php echo $this->form->getInput('website'); ?></div>
								</div>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('description'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('description'); ?></div>
						</div>
					</div>
					<?php echo $this->loadTemplate('address'); ?>
					<div class="tab-pane" id="publishing">
						<div class="row-fluid">
							<div class="span6">
								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('alias'); ?></div>
									<div class="controls"><?php echo $this->form->getInput('alias'); ?></div>
								</div>
								<?php if ($this->item->id): ?>
									<div class="control-group">
										<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
										<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
									</div>
								<?php endif; ?>
								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('created_by'); ?></div>
									<div class="controls"><?php echo $this->form->getInput('created_by'); ?></div>
								</div>
								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('created_by_alias'); ?></div>
									<div class="controls"><?php echo $this->form->getInput('created_by_alias'); ?></div>
								</div>
								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('created'); ?></div>
									<div class="controls"><?php echo $this->form->getInput('created'); ?></div>
								</div>
							</div>
							<div class="span6">
								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('version'); ?></div>
									<div class="controls"><?php echo $this->form->getInput('version'); ?></div>
								</div>
								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('modified_by'); ?></div>
									<div class="controls"><?php echo $this->form->getInput('modified_by'); ?></div>
								</div>
								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('modified'); ?></div>
									<div class="controls"><?php echo $this->form->getInput('modified'); ?></div>
								</div>
								<?php if ($this->item->hits): ?>
									<div class="control-group">
										<div class="control-label"><?php echo $this->form->getLabel('hits'); ?></div>
										<div class="controls"><?php echo $this->form->getInput('hits'); ?></div>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
					<?php echo $this->loadTemplate('params'); ?>
					<?php echo $this->loadTemplate('metadata'); ?>
				</div>
			</fieldset>
			<input type="hidden" name="task" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
		<!-- End Companies -->
		<!-- Begin Sidebar -->
		<div class="span2">
			<h4><?php echo JText::_('JDETAILS'); ?></h4>
			<hr />
			<fieldset class="form-vertical">
				<div class="control-group">
					<div class="controls"><?php echo $this->form->getValue('name'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('state'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('access'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('access'); ?></div>
				</div>
			</fieldset>
		</div>
		<!-- End Sidebar -->
	</div>
</form>
