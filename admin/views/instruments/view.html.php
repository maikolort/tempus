<?php
/*----------------------------------------------------------------------------------|  www.vdm.io  |----/
				Maikol Fustes 
/-------------------------------------------------------------------------------------------------------/

	@version		1.0.x
	@build			23rd Agosto, 2019
	@created		22nd Agosto, 2019
	@package		Tempus
	@subpackage		view.html.php
	@author			Miguel A García Fustes <https://maikol.eu>	
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html
  ____  _____  _____  __  __  __      __       ___  _____  __  __  ____  _____  _  _  ____  _  _  ____ 
 (_  _)(  _  )(  _  )(  \/  )(  )    /__\     / __)(  _  )(  \/  )(  _ \(  _  )( \( )( ___)( \( )(_  _)
.-_)(   )(_)(  )(_)(  )    (  )(__  /(__)\   ( (__  )(_)(  )    (  )___/ )(_)(  )  (  )__)  )  (   )(  
\____) (_____)(_____)(_/\/\_)(____)(__)(__)   \___)(_____)(_/\/\_)(__)  (_____)(_)\_)(____)(_)\_) (__) 

/------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Tempus View class for the Instruments
 */
class TempusViewInstruments extends JViewLegacy
{
	/**
	 * Instruments view display method
	 * @return void
	 */
	function display($tpl = null)
	{
		if ($this->getLayout() !== 'modal')
		{
			// Include helper submenu
			TempusHelper::addSubmenu('instruments');
		}

		// Assign data to the view
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->user = JFactory::getUser();
		$this->listOrder = $this->escape($this->state->get('list.ordering'));
		$this->listDirn = $this->escape($this->state->get('list.direction'));
		$this->saveOrder = $this->listOrder == 'ordering';
		// set the return here value
		$this->return_here = urlencode(base64_encode((string) JUri::getInstance()));
		// get global action permissions
		$this->canDo = TempusHelper::getActions('instrument');
		$this->canEdit = $this->canDo->get('core.edit');
		$this->canState = $this->canDo->get('core.edit.state');
		$this->canCreate = $this->canDo->get('core.create');
		$this->canDelete = $this->canDo->get('core.delete');
		$this->canBatch = $this->canDo->get('core.batch');

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			$this->addToolbar();
			$this->sidebar = JHtmlSidebar::render();
			// load the batch html
			if ($this->canCreate && $this->canEdit && $this->canState)
			{
				$this->batchDisplay = JHtmlBatch_::render();
			}
		}
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		JToolBarHelper::title(JText::_('COM_TEMPUS_INSTRUMENTS'), 'compass');
		JHtmlSidebar::setAction('index.php?option=com_tempus&view=instruments');
		JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');

		if ($this->canCreate)
		{
			JToolBarHelper::addNew('instrument.add');
		}

		// Only load if there are items
		if (TempusHelper::checkArray($this->items))
		{
			if ($this->canEdit)
			{
				JToolBarHelper::editList('instrument.edit');
			}

			if ($this->canState)
			{
				JToolBarHelper::publishList('instruments.publish');
				JToolBarHelper::unpublishList('instruments.unpublish');
				JToolBarHelper::archiveList('instruments.archive');

				if ($this->canDo->get('core.admin'))
				{
					JToolBarHelper::checkin('instruments.checkin');
				}
			}

			// Add a batch button
			if ($this->canBatch && $this->canCreate && $this->canEdit && $this->canState)
			{
				// Get the toolbar object instance
				$bar = JToolBar::getInstance('toolbar');
				// set the batch button name
				$title = JText::_('JTOOLBAR_BATCH');
				// Instantiate a new JLayoutFile instance and render the batch button
				$layout = new JLayoutFile('joomla.toolbar.batch');
				// add the button to the page
				$dhtml = $layout->render(array('title' => $title));
				$bar->appendButton('Custom', $dhtml, 'batch');
			}

			if ($this->state->get('filter.published') == -2 && ($this->canState && $this->canDelete))
			{
				JToolbarHelper::deleteList('', 'instruments.delete', 'JTOOLBAR_EMPTY_TRASH');
			}
			elseif ($this->canState && $this->canDelete)
			{
				JToolbarHelper::trash('instruments.trash');
			}

			if ($this->canDo->get('core.export') && $this->canDo->get('instrument.export'))
			{
				JToolBarHelper::custom('instruments.exportData', 'download', '', 'COM_TEMPUS_EXPORT_DATA', true);
			}
		}

		if ($this->canDo->get('core.import') && $this->canDo->get('instrument.import'))
		{
			JToolBarHelper::custom('instruments.importData', 'upload', '', 'COM_TEMPUS_IMPORT_DATA', false);
		}

		// set help url for this view if found
		$help_url = TempusHelper::getHelpUrl('instruments');
		if (TempusHelper::checkString($help_url))
		{
				JToolbarHelper::help('COM_TEMPUS_HELP_MANAGER', false, $help_url);
		}

		// add the options comp button
		if ($this->canDo->get('core.admin') || $this->canDo->get('core.options'))
		{
			JToolBarHelper::preferences('com_tempus');
		}

		if ($this->canState)
		{
			JHtmlSidebar::addFilter(
				JText::_('JOPTION_SELECT_PUBLISHED'),
				'filter_published',
				JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true)
			);
			// only load if batch allowed
			if ($this->canBatch)
			{
				JHtmlBatch_::addListSelection(
					JText::_('COM_TEMPUS_KEEP_ORIGINAL_STATE'),
					'batch[published]',
					JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('all' => false)), 'value', 'text', '', true)
				);
			}
		}

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_ACCESS'),
			'filter_access',
			JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'))
		);

		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			JHtmlBatch_::addListSelection(
				JText::_('COM_TEMPUS_KEEP_ORIGINAL_ACCESS'),
				'batch[access]',
				JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text')
			);
		}

		// Set Type Id Title Selection
		$this->type_idTitleOptions = JFormHelper::loadFieldType('Tiposinstrumento')->options;
		// We do some sanitation for Type Id Title filter
		if (TempusHelper::checkArray($this->type_idTitleOptions) &&
			isset($this->type_idTitleOptions[0]->value) &&
			!TempusHelper::checkString($this->type_idTitleOptions[0]->value))
		{
			unset($this->type_idTitleOptions[0]);
		}
		// Only load Type Id Title filter if it has values
		if (TempusHelper::checkArray($this->type_idTitleOptions))
		{
			// Type Id Title Filter
			JHtmlSidebar::addFilter(
				'- Select '.JText::_('COM_TEMPUS_INSTRUMENT_TYPE_ID_LABEL').' -',
				'filter_type_id',
				JHtml::_('select.options', $this->type_idTitleOptions, 'value', 'text', $this->state->get('filter.type_id'))
			);

			if ($this->canBatch && $this->canCreate && $this->canEdit)
			{
				// Type Id Title Batch Selection
				JHtmlBatch_::addListSelection(
					'- Keep Original '.JText::_('COM_TEMPUS_INSTRUMENT_TYPE_ID_LABEL').' -',
					'batch[type_id]',
					JHtml::_('select.options', $this->type_idTitleOptions, 'value', 'text')
				);
			}
		}
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		if (!isset($this->document))
		{
			$this->document = JFactory::getDocument();
		}
		$this->document->setTitle(JText::_('COM_TEMPUS_INSTRUMENTS'));
		$this->document->addStyleSheet(JURI::root() . "administrator/components/com_tempus/assets/css/instruments.css", (TempusHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/css');
	}

	/**
	 * Escapes a value for output in a view script.
	 *
	 * @param   mixed  $var  The output to escape.
	 *
	 * @return  mixed  The escaped value.
	 */
	public function escape($var)
	{
		if(strlen($var) > 50)
		{
			// use the helper htmlEscape method instead and shorten the string
			return TempusHelper::htmlEscape($var, $this->_charset, true);
		}
		// use the helper htmlEscape method instead.
		return TempusHelper::htmlEscape($var, $this->_charset);
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 */
	protected function getSortFields()
	{
		return array(
			'a.sorting' => JText::_('JGRID_HEADING_ORDERING'),
			'a.published' => JText::_('JSTATUS'),
			'a.title' => JText::_('COM_TEMPUS_INSTRUMENT_TITLE_LABEL'),
			'g.title' => JText::_('COM_TEMPUS_INSTRUMENT_TYPE_ID_LABEL'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
