<?php
/*----------------------------------------------------------------------------------|  www.vdm.io  |----/
				Maikol Fustes 
/-------------------------------------------------------------------------------------------------------/

	@version		1.0.x
	@build			23rd Agosto, 2019
	@created		22nd Agosto, 2019
	@package		Tempus
	@subpackage		songs.php
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
 * Songs Controller
 */
class TempusControllerSongs extends JControllerAdmin
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_TEMPUS_SONGS';

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JModelLegacy  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Song', $prefix = 'TempusModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	public function exportData()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// check if export is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('song.export', 'com_tempus') && $user->authorise('core.export', 'com_tempus'))
		{
			// Get the input
			$input = JFactory::getApplication()->input;
			$pks = $input->post->get('cid', array(), 'array');
			// Sanitize the input
			JArrayHelper::toInteger($pks);
			// Get the model
			$model = $this->getModel('Songs');
			// get the data to export
			$data = $model->getExportData($pks);
			if (TempusHelper::checkArray($data))
			{
				// now set the data to the spreadsheet
				$date = JFactory::getDate();
				TempusHelper::xls($data,'Songs_'.$date->format('jS_F_Y'),'Songs exported ('.$date->format('jS F, Y').')','songs');
			}
		}
		// Redirect to the list screen with error.
		$message = JText::_('COM_TEMPUS_EXPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_tempus&view=songs', false), $message, 'error');
		return;
	}


	public function importData()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// check if import is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('song.import', 'com_tempus') && $user->authorise('core.import', 'com_tempus'))
		{
			// Get the import model
			$model = $this->getModel('Songs');
			// get the headers to import
			$headers = $model->getExImPortHeaders();
			if (TempusHelper::checkObject($headers))
			{
				// Load headers to session.
				$session = JFactory::getSession();
				$headers = json_encode($headers);
				$session->set('song_VDM_IMPORTHEADERS', $headers);
				$session->set('backto_VDM_IMPORT', 'songs');
				$session->set('dataType_VDM_IMPORTINTO', 'song');
				// Redirect to import view.
				$message = JText::_('COM_TEMPUS_IMPORT_SELECT_FILE_FOR_SONGS');
				$this->setRedirect(JRoute::_('index.php?option=com_tempus&view=import', false), $message);
				return;
			}
		}
		// Redirect to the list screen with error.
		$message = JText::_('COM_TEMPUS_IMPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_tempus&view=songs', false), $message, 'error');
		return;
	}
}
