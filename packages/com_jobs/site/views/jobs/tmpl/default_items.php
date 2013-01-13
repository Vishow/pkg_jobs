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

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.framework');

// Create some shortcuts.
$params    = &$this->item->params;
$n         = count($this->items);
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

// Check for at least one editable job.
$isEditable = false;

if (!empty($this->items))
{
	foreach ($this->items as $item)
	{
		if ($item->params->get('access-edit'))
		{
			$isEditable = true;
			break;
		}
	}
}

// Load Stylesheet.
JHtml::stylesheet('com_jobs/select2.css', false, true, false);
JHtml::stylesheet('com_jobs/frontend.css', false, true, false);

// Add JavaScript Frameworks.
JHtml::_('jquery.framework');

// Load JavaScript.
JHtml::script('com_jobs/select2.min.js', false, true);
?>
<script type="text/javascript">
	jQuery.noConflict();

	(function ($) {
		$(function () {
			$('select').select2();
		});
	})(jQuery);
</script>
<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm" class="form-inline">
	<div class="well">
		<div class="row-fluid">
			<div class="span4">
				<?php echo $this->lists['area']; ?>
			</div>
			<div class="span4">
				<?php echo $this->lists['state']; ?>
			</div>
			<div class="span3">
				<label for="searchword" class="element-invisible"><?php echo JText::_('COM_JOBS_SEARCH_KEYWORD'); ?></label>
				<input type="text" name="searchword" id="searchword" class="input-block-level" placeholder="<?php echo JText::_('COM_JOBS_SEARCH_KEYWORD'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_JOBS_SEARCH_KEYWORD'); ?>" />
			</div>
			<div class="span1">
				<div class="btn-group">
					<button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
					<button class="btn hasTooltip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('searchword').value='';document.id('area').value='all';document.id('state').value='all';this.form.submit();"><i class="icon-remove"></i></button>
				</div>
			</div>
		</div>
	</div>

	<?php if (empty($this->items)): ?>
		<?php if ($this->params->get('show_no_jobs', 1)): ?>
			<p><?php echo JText::_('COM_JOBS_MSG_NO_JOBS'); ?></p>
		<?php endif; ?>
	<?php else: ?>
		<table class="jobs table table-striped table-hover">
			<thead>
				<tr>
					<th width="50" class="center nowrap hidden-phone">
						<?php echo JHtml::_('jobs.sort', 'JDATE', 'a.created', $listDirn, $listOrder); ?>
					</th>
					<th class="title">
						<?php echo JHtml::_('jobs.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
					</th>
					<th width="20%" class="hidden-phone">
						<?php echo JHtml::_('jobs.sort', 'COM_JOBS_HEADING_COMPANY', 'c.name', $listDirn, $listOrder); ?>
					</th>
					<th width="20%" class="hidden-phone">
						<?php echo JHtml::_('jobs.sort', 'COM_JOBS_HEADING_AREA', 'ar.name', $listDirn, $listOrder); ?>
					</th>
					<th width="7%" class="center nowrap hidden-phone">
						<?php echo JHtml::_('jobs.sort', 'COM_JOBS_HEADING_ADDRESS_STATE', 'c.address_state', $listDirn, $listOrder); ?>
					</th>
					<?php if ($isEditable): ?>
						<th width="5%" class="nowrap center">
							<?php echo JText::_('COM_JOBS_EDIT_ITEM'); ?>
						</th>
					<?php endif; ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->items as $i => $item): ?>
					<?php if ($this->items[$i]->state == 0): ?>
						<tr class="system-unpublished row<?php echo $i % 2; ?>">
					<?php else: ?>
						<tr class="row<?php echo $i % 2; ?>" >
					<?php endif; ?>
						<td class="small center hidden-phone">
							<?php echo JHtml::_('date', $item->created, $this->escape($this->params->get('date_format', JText::_('COM_JOBS_DATE_FORMAT')))); ?>
						</td>
						<td class="nowrap">
							<?php if (in_array($item->access, $this->user->getAuthorisedViewLevels())): ?>
								<a href="<?php echo JRoute::_(JobsHelperRoute::getJobRoute($item->slug)); ?>"><?php echo $this->escape($item->title); ?></a>
							<?php else: ?>
								<?php
								echo $this->escape($item->title) . ' : ';

								$menu      = JFactory::getApplication()->getMenu();
								$active    = $menu->getActive();
								$itemId    = $active->id;
								$link      = JRoute::_('index.php?option=com_users&view=login&Itemid=' . $itemId);
								$returnURL = JRoute::_(JobsHelperRoute::getJobRoute($item->slug));
								$fullURL   = new JURI($link);
								$fullURL->setVar('return', base64_encode($returnURL));
								?>
								<a href="<?php echo $fullURL; ?>" class="register"><?php echo JText::_('COM_JOBS_REGISTER_TO_READ_MORE'); ?></a>
							<?php endif; ?>
							<br>
							<?php echo JobsHelper::getStatus($item->status, $item->created, $item->expiry_date); ?>
							<?php if ($item->disabled): ?>
								<span class="label label-info"><?php echo JText::_('COM_JOBS_DISABLED_PEOPLE'); ?></span>
							<?php endif; ?>
							<?php if ($item->state == 0): ?>
								<span class="label label-warning">
									<?php echo JText::_('JUNPUBLISHED'); ?>
								</span>
							<?php endif; ?>
						</td>
						<td class="small nowrap hidden-phone">
							<?php echo JHtml::_('string.truncate', $item->company_name, 40, true, false); ?>
						</td>
						<td class="small hidden-phone">
							<?php echo $this->escape($item->area_name); ?>
						</td>
						<td class="small center nowrap hidden-phone">
							<span class="label"><?php echo $this->escape($item->state_name) ?></span>
						</td>
						<?php if ($isEditable): ?>
							<td class="center edit">
								<?php if ($item->params->get('access-edit')): ?>
									<?php echo JHtml::_('icon.edit', $item, $params); ?>
								<?php endif; ?>
							</td>
						<?php endif; ?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
	<input type="hidden" name="task" value="search" />
	<input type="hidden" name="limitstart" value="" />
	<input type="hidden" name="filter_order" value="" />
	<input type="hidden" name="filter_order_Dir" value="" />
</form>
