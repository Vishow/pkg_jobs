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
?>
<script type="text/javascript">
	Joomla.submitbutton = function (task) {
		if (task == 'benefit.cancel' || document.formvalidator.isValid(document.id('benefit-form'))) {
			Joomla.submitform(task, document.getElementById('benefit-form'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
		}
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_jobs&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="benefit-form" class="form-validate">
	<div class="row-fluid">
		<!-- Begin Benefits -->
		<div class="span10 form-horizontal">
			<fieldset>
				<ul class="nav nav-tabs">
					<li class="active"><a href="#details" data-toggle="tab"><?php echo empty($this->item->id) ? JText::_('COM_JOBS_NEW_BENEFIT') : JText::sprintf('COM_JOBS_EDIT_BENEFIT', $this->item->id); ?></a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="details">
						<?php if ($this->item->id): ?>
							<div class="control-group">
								<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
								<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
							</div>
						<?php endif; ?>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('ordering'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('ordering'); ?></div>
						</div>
					</div>
				</div>
			</fieldset>
			<input type="hidden" name="task" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
		<!-- End Benefits -->
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
			</fieldset>
		</div>
		<!-- End Sidebar -->
	</div>
</form>
