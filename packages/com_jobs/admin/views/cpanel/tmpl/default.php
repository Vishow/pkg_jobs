<?php
/**
 * @package     Jobs
 * @subpackage  com_jobs
 * @copyright   Copyright (C) 2012 AtomTech, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Get the current user object.
$user = JFactory::getUser();
?>
<div class="row-fluid">
	<div class="span2">
		<div class="sidebar-nav">
			<ul class="nav nav-list">
				<li class="nav-header"><?php echo JText::_('COM_JOBS_HEADER_SUBMENU'); ?></li>
				<li class="active"><a href="<?php echo $this->baseurl; ?>/index.php?option=com_jobs"><?php echo JText::_('COM_JOBS_LINK_DASHBOARD'); ?></a></li>
				<li><a href="<?php echo $this->baseurl; ?>/index.php?option=com_jobs&amp;view=jobs"><?php echo JText::_('COM_JOBS_LINK_JOBS'); ?></a></li>
				<li><a href="<?php echo $this->baseurl; ?>/index.php?option=com_jobs&amp;view=companies"><?php echo JText::_('COM_JOBS_LINK_COMPANIES'); ?></a></li>
				<li><a href="<?php echo $this->baseurl; ?>/index.php?option=com_jobs&amp;view=sectors"><?php echo JText::_('COM_JOBS_LINK_SECTORS'); ?></a></li>
				<li><a href="<?php echo $this->baseurl; ?>/index.php?option=com_jobs&amp;view=benefits"><?php echo JText::_('COM_JOBS_LINK_BENEFITS'); ?></a></li>
				<li><a href="<?php echo $this->baseurl; ?>/index.php?option=com_jobs&amp;view=areas"><?php echo JText::_('COM_JOBS_LINK_AREAS'); ?></a></li>
				<li><a href="<?php echo $this->baseurl; ?>/index.php?option=com_jobs&amp;view=levels"><?php echo JText::_('COM_JOBS_LINK_LEVELS'); ?></a></li>
				<li><a href="<?php echo $this->baseurl; ?>/index.php?option=com_jobs&amp;view=contracts"><?php echo JText::_('COM_JOBS_LINK_CONTRACTS'); ?></a></li>
				<li><a href="<?php echo $this->baseurl; ?>/index.php?option=com_jobs&amp;view=studies"><?php echo JText::_('COM_JOBS_LINK_STUDIES'); ?></a></li>
			</ul>
		</div>
	</div>
	<div class="span6">
		<?php
		foreach ($this->modules as $module)
		{
			$output = JModuleHelper::renderModule($module, array('style' => 'well'));
			$params = new JRegistry;
			$params->loadString($module->params);
			echo $output;
		}
		?>
	</div>
	<div class="span4">
		<?php
		foreach ($this->iconmodules as $iconmodule)
		{
			$output = JModuleHelper::renderModule($iconmodule, array('style' => 'well'));
			$params = new JRegistry;
			$params->loadString($iconmodule->params);
			echo $output;
		}
		?>
	</div>
</div>
