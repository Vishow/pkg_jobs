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
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');

// Create shortcut to parameters.
$params = $this->state->get('params');
?>
<script type="text/javascript">
	Joomla.submitbutton = function (task) {
		if (task == 'job.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			<?php echo $this->form->getField('description')->save(); ?>
			Joomla.submitform(task);
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
		}
	}
</script>
<div class="jobs edit item-page<?php echo $this->pageclass_sfx; ?>">
	<?php if ($params->get('show_page_heading', 1)): ?>
		<div class="page-header">
			<h1>
				<?php echo $this->escape($params->get('page_heading')); ?>
			</h1>
		</div>
	<?php endif; ?>

	<form action="<?php echo JRoute::_('index.php?option=com_jobs&j_id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-vertical">
		<fieldset>
			<ul class="nav nav-tabs">
				<li class="active"><a href="#editor" data-toggle="tab"><?php echo JText::_('JEDITOR'); ?></a></li>
				<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_JOBS_FIELDSET_PUBLISHING'); ?></a></li>
				<?php $fieldSets = $this->form->getFieldsets('metadata');
				foreach ($fieldSets as $name => $fieldSet): ?>
					<li><a href="#metadata-<?php echo $name; ?>" data-toggle="tab"><?php echo JText::_('COM_JOBS_FIELDSET_METADATA'); ?></a></li>
				<?php endforeach; ?>
			</ul>

			<div class="tab-content">
				<div class="tab-pane active" id="editor">
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('title'); ?></div>
					</div>

					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('alias'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('alias'); ?></div>
					</div>

					<?php echo $this->form->getInput('description'); ?>
				</div>
				<div class="tab-pane" id="publishing">
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('created_by_alias'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('created_by_alias'); ?></div>
					</div>
					<?php if ($this->item->params->get('access-change')): ?>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('state'); ?></div>
						</div>
					<?php endif; ?>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('access'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('access'); ?></div>
					</div>
				</div>
				<?php $fieldSets = $this->form->getFieldsets('metadata');

				foreach ($fieldSets as $name => $fieldSet): ?>
					<div class="tab-pane" id="metadata-<?php echo $name; ?>">
					<?php if (isset($fieldSet->description) && trim($fieldSet->description)):
						echo '<p class="alert alert-info">' . $this->escape(JText::_($fieldSet->description)) . '</p>';
					endif; ?>
						<?php if ($name == 'jmetadata'): // Include the real fields in this panel. ?>
							<div class="control-group">
								<div class="control-label"><?php echo $this->form->getLabel('metadesc'); ?></div>
								<div class="controls"><?php echo $this->form->getInput('metadesc'); ?></div>
							</div>
							<div class="control-group">
								<div class="control-label"><?php echo $this->form->getLabel('metakey'); ?></div>
								<div class="controls"><?php echo $this->form->getInput('metakey'); ?></div>
							</div>
							<div class="control-group">
								<div class="control-label"><?php echo $this->form->getLabel('xreference'); ?></div>
								<div class="controls"><?php echo $this->form->getInput('xreference'); ?></div>
							</div>
						<?php endif; ?>
						<?php foreach ($this->form->getFieldset($name) as $field): ?>
							<div class="control-group">
								<div class="control-label"><?php echo $field->label; ?></div>
								<div class="controls"><?php echo $field->input; ?></div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />
			</div>
			<?php echo JHtml::_('form.token'); ?>
		</fieldset>
		<div class="form-actions">
			<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('job.save')">
				<i class="icon-ok"></i> <?php echo JText::_('JSAVE'); ?>
			</button>
			<button type="button" class="btn" onclick="Joomla.submitbutton('job.cancel')">
				<i class="icon-cancel"></i> <?php echo JText::_('JCANCEL'); ?>
			</button>
		</div>
	</form>
</div>
