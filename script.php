<?php
/*----------------------------------------------------------------------------------|  www.vdm.io  |----/
				Maikol Fustes 
/-------------------------------------------------------------------------------------------------------/

	@version		1.0.x
	@build			23rd Agosto, 2019
	@created		22nd Agosto, 2019
	@package		Tempus
	@subpackage		script.php
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

JHTML::_('behavior.modal');

/**
 * Script File of Tempus Component
 */
class com_tempusInstallerScript
{
	/**
	 * Constructor
	 *
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 */
	public function __construct(JAdapterInstance $parent) {}

	/**
	 * Called on installation
	 *
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function install(JAdapterInstance $parent) {}

	/**
	 * Called on uninstallation
	 *
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 */
	public function uninstall(JAdapterInstance $parent)
	{
		// Get Application object
		$app = JFactory::getApplication();

		// Get The Database object
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Song alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_tempus.song') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$song_found = $db->getNumRows();
		// Now check if there were any rows
		if ($song_found)
		{
			// Since there are load the needed  song type ids
			$song_ids = $db->loadColumn();
			// Remove Song from the content type table
			$song_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_tempus.song') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($song_condition);
			$db->setQuery($query);
			// Execute the query to remove Song items
			$song_done = $db->execute();
			if ($song_done)
			{
				// If succesfully remove Song add queued success message.
				$app->enqueueMessage(JText::_('The (com_tempus.song) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Song items from the contentitem tag map table
			$song_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_tempus.song') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($song_condition);
			$db->setQuery($query);
			// Execute the query to remove Song items
			$song_done = $db->execute();
			if ($song_done)
			{
				// If succesfully remove Song add queued success message.
				$app->enqueueMessage(JText::_('The (com_tempus.song) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Song items from the ucm content table
			$song_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_tempus.song') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($song_condition);
			$db->setQuery($query);
			// Execute the query to remove Song items
			$song_done = $db->execute();
			if ($song_done)
			{
				// If succesfully remove Song add queued success message.
				$app->enqueueMessage(JText::_('The (com_tempus.song) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Song items are cleared from DB
			foreach ($song_ids as $song_id)
			{
				// Remove Song items from the ucm base table
				$song_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $song_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($song_condition);
				$db->setQuery($query);
				// Execute the query to remove Song items
				$db->execute();

				// Remove Song items from the ucm history table
				$song_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $song_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($song_condition);
				$db->setQuery($query);
				// Execute the query to remove Song items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Instrument alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_tempus.instrument') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$instrument_found = $db->getNumRows();
		// Now check if there were any rows
		if ($instrument_found)
		{
			// Since there are load the needed  instrument type ids
			$instrument_ids = $db->loadColumn();
			// Remove Instrument from the content type table
			$instrument_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_tempus.instrument') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($instrument_condition);
			$db->setQuery($query);
			// Execute the query to remove Instrument items
			$instrument_done = $db->execute();
			if ($instrument_done)
			{
				// If succesfully remove Instrument add queued success message.
				$app->enqueueMessage(JText::_('The (com_tempus.instrument) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Instrument items from the contentitem tag map table
			$instrument_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_tempus.instrument') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($instrument_condition);
			$db->setQuery($query);
			// Execute the query to remove Instrument items
			$instrument_done = $db->execute();
			if ($instrument_done)
			{
				// If succesfully remove Instrument add queued success message.
				$app->enqueueMessage(JText::_('The (com_tempus.instrument) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Instrument items from the ucm content table
			$instrument_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_tempus.instrument') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($instrument_condition);
			$db->setQuery($query);
			// Execute the query to remove Instrument items
			$instrument_done = $db->execute();
			if ($instrument_done)
			{
				// If succesfully remove Instrument add queued success message.
				$app->enqueueMessage(JText::_('The (com_tempus.instrument) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Instrument items are cleared from DB
			foreach ($instrument_ids as $instrument_id)
			{
				// Remove Instrument items from the ucm base table
				$instrument_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $instrument_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($instrument_condition);
				$db->setQuery($query);
				// Execute the query to remove Instrument items
				$db->execute();

				// Remove Instrument items from the ucm history table
				$instrument_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $instrument_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($instrument_condition);
				$db->setQuery($query);
				// Execute the query to remove Instrument items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Instrument_type alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_tempus.instrument_type') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$instrument_type_found = $db->getNumRows();
		// Now check if there were any rows
		if ($instrument_type_found)
		{
			// Since there are load the needed  instrument_type type ids
			$instrument_type_ids = $db->loadColumn();
			// Remove Instrument_type from the content type table
			$instrument_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_tempus.instrument_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($instrument_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Instrument_type items
			$instrument_type_done = $db->execute();
			if ($instrument_type_done)
			{
				// If succesfully remove Instrument_type add queued success message.
				$app->enqueueMessage(JText::_('The (com_tempus.instrument_type) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Instrument_type items from the contentitem tag map table
			$instrument_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_tempus.instrument_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($instrument_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Instrument_type items
			$instrument_type_done = $db->execute();
			if ($instrument_type_done)
			{
				// If succesfully remove Instrument_type add queued success message.
				$app->enqueueMessage(JText::_('The (com_tempus.instrument_type) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Instrument_type items from the ucm content table
			$instrument_type_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_tempus.instrument_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($instrument_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Instrument_type items
			$instrument_type_done = $db->execute();
			if ($instrument_type_done)
			{
				// If succesfully remove Instrument_type add queued success message.
				$app->enqueueMessage(JText::_('The (com_tempus.instrument_type) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Instrument_type items are cleared from DB
			foreach ($instrument_type_ids as $instrument_type_id)
			{
				// Remove Instrument_type items from the ucm base table
				$instrument_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $instrument_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($instrument_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Instrument_type items
				$db->execute();

				// Remove Instrument_type items from the ucm history table
				$instrument_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $instrument_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($instrument_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Instrument_type items
				$db->execute();
			}
		}

		// If All related items was removed queued success message.
		$app->enqueueMessage(JText::_('All related items was removed from the <b>#__ucm_base</b> table'));
		$app->enqueueMessage(JText::_('All related items was removed from the <b>#__ucm_history</b> table'));

		// Remove tempus assets from the assets table
		$tempus_condition = array( $db->quoteName('name') . ' LIKE ' . $db->quote('com_tempus%') );

		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__assets'));
		$query->where($tempus_condition);
		$db->setQuery($query);
		$instrument_type_done = $db->execute();
		if ($instrument_type_done)
		{
			// If succesfully remove tempus add queued success message.
			$app->enqueueMessage(JText::_('All related items was removed from the <b>#__assets</b> table'));
		}

		// little notice as after service, in case of bad experience with component.
		echo '<h2>Did something go wrong? Are you disappointed?</h2>
		<p>Please let me know at <a href="mailto:maikol.ortigueira@gmail.com">maikol.ortigueira@gmail.com</a>.
		<br />We at Maikol Fustes are committed to building extensions that performs proficiently! You can help us, really!
		<br />Send me your thoughts on improvements that is needed, trust me, I will be very grateful!
		<br />Visit us at <a href="https://maikol.eu" target="_blank">https://maikol.eu</a> today!</p>';
	}

	/**
	 * Called on update
	 *
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function update(JAdapterInstance $parent){}

	/**
	 * Called before any type of action
	 *
	 * @param   string  $type  Which action is happening (install|uninstall|discover_install|update)
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function preflight($type, JAdapterInstance $parent)
	{
		// get application
		$app = JFactory::getApplication();
		// is redundant or so it seems ...hmmm let me know if it works again
		if ($type === 'uninstall')
		{
			return true;
		}
		// the default for both install and update
		$jversion = new JVersion();
		if (!$jversion->isCompatible('3.8.0'))
		{
			$app->enqueueMessage('Please upgrade to at least Joomla! 3.8.0 before continuing!', 'error');
			return false;
		}
		// do any updates needed
		if ($type === 'update')
		{
		}
		// do any install needed
		if ($type === 'install')
		{
		}
		return true;
	}

	/**
	 * Called after any type of action
	 *
	 * @param   string  $type  Which action is happening (install|uninstall|discover_install|update)
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function postflight($type, JAdapterInstance $parent)
	{
		// get application
		$app = JFactory::getApplication();
		// set the default component settings
		if ($type === 'install')
		{

			// Get The Database object
			$db = JFactory::getDbo();

			// Create the song content type object.
			$song = new stdClass();
			$song->type_title = 'Tempus Song';
			$song->type_alias = 'com_tempus.song';
			$song->table = '{"special": {"dbtable": "#__tempus_song","key": "id","type": "Song","prefix": "tempusTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$song->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "title","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"title":"title"}}';
			$song->router = 'TempusHelperRoute::getSongRoute';
			$song->content_history_options = '{"formFile": "administrator/components/com_tempus/models/forms/song.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$song_Inserted = $db->insertObject('#__content_types', $song);

			// Create the instrument content type object.
			$instrument = new stdClass();
			$instrument->type_title = 'Tempus Instrument';
			$instrument->type_alias = 'com_tempus.instrument';
			$instrument->table = '{"special": {"dbtable": "#__tempus_instrument","key": "id","type": "Instrument","prefix": "tempusTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$instrument->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "title","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"title":"title","type_id":"type_id","alias":"alias"}}';
			$instrument->router = 'TempusHelperRoute::getInstrumentRoute';
			$instrument->content_history_options = '{"formFile": "administrator/components/com_tempus/models/forms/instrument.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","type_id"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "type_id","targetTable": "#__tempus_instrument_type","targetColumn": "id","displayColumn": "title"}]}';

			// Set the object into the content types table.
			$instrument_Inserted = $db->insertObject('#__content_types', $instrument);

			// Create the instrument_type content type object.
			$instrument_type = new stdClass();
			$instrument_type->type_title = 'Tempus Instrument_type';
			$instrument_type->type_alias = 'com_tempus.instrument_type';
			$instrument_type->table = '{"special": {"dbtable": "#__tempus_instrument_type","key": "id","type": "Instrument_type","prefix": "tempusTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$instrument_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "title","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"title":"title","group_type":"group_type","alias":"alias"}}';
			$instrument_type->router = 'TempusHelperRoute::getInstrument_typeRoute';
			$instrument_type->content_history_options = '{"formFile": "administrator/components/com_tempus/models/forms/instrument_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","group_type"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$instrument_type_Inserted = $db->insertObject('#__content_types', $instrument_type);


			// Install the global extenstion params.
			$query = $db->getQuery(true);
			// Field to update.
			$fields = array(
				$db->quoteName('params') . ' = ' . $db->quote('{"autorName":"Miguel A García Fustes","autorEmail":"maikol.ortigueira@gmail.com","group_type":"1","check_in":"-1 day","save_history":"1","history_limit":"10"}'),
			);
			// Condition.
			$conditions = array(
				$db->quoteName('element') . ' = ' . $db->quote('com_tempus')
			);
			$query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
			$db->setQuery($query);
			$allDone = $db->execute();

			echo '<a target="_blank" href="https://maikol.eu" title="Tempus">
				<img src="components/com_tempus/assets/images/vdm-component.jpg"/>
				</a>';
		}
		// do any updates needed
		if ($type === 'update')
		{

			// Get The Database object
			$db = JFactory::getDbo();

			// Create the song content type object.
			$song = new stdClass();
			$song->type_title = 'Tempus Song';
			$song->type_alias = 'com_tempus.song';
			$song->table = '{"special": {"dbtable": "#__tempus_song","key": "id","type": "Song","prefix": "tempusTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$song->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "title","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"title":"title"}}';
			$song->router = 'TempusHelperRoute::getSongRoute';
			$song->content_history_options = '{"formFile": "administrator/components/com_tempus/models/forms/song.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if song type is already in content_type DB.
			$song_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($song->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$song->type_id = $db->loadResult();
				$song_Updated = $db->updateObject('#__content_types', $song, 'type_id');
			}
			else
			{
				$song_Inserted = $db->insertObject('#__content_types', $song);
			}

			// Create the instrument content type object.
			$instrument = new stdClass();
			$instrument->type_title = 'Tempus Instrument';
			$instrument->type_alias = 'com_tempus.instrument';
			$instrument->table = '{"special": {"dbtable": "#__tempus_instrument","key": "id","type": "Instrument","prefix": "tempusTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$instrument->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "title","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"title":"title","type_id":"type_id","alias":"alias"}}';
			$instrument->router = 'TempusHelperRoute::getInstrumentRoute';
			$instrument->content_history_options = '{"formFile": "administrator/components/com_tempus/models/forms/instrument.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","type_id"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "type_id","targetTable": "#__tempus_instrument_type","targetColumn": "id","displayColumn": "title"}]}';

			// Check if instrument type is already in content_type DB.
			$instrument_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($instrument->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$instrument->type_id = $db->loadResult();
				$instrument_Updated = $db->updateObject('#__content_types', $instrument, 'type_id');
			}
			else
			{
				$instrument_Inserted = $db->insertObject('#__content_types', $instrument);
			}

			// Create the instrument_type content type object.
			$instrument_type = new stdClass();
			$instrument_type->type_title = 'Tempus Instrument_type';
			$instrument_type->type_alias = 'com_tempus.instrument_type';
			$instrument_type->table = '{"special": {"dbtable": "#__tempus_instrument_type","key": "id","type": "Instrument_type","prefix": "tempusTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$instrument_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "title","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"title":"title","group_type":"group_type","alias":"alias"}}';
			$instrument_type->router = 'TempusHelperRoute::getInstrument_typeRoute';
			$instrument_type->content_history_options = '{"formFile": "administrator/components/com_tempus/models/forms/instrument_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","group_type"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if instrument_type type is already in content_type DB.
			$instrument_type_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($instrument_type->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$instrument_type->type_id = $db->loadResult();
				$instrument_type_Updated = $db->updateObject('#__content_types', $instrument_type, 'type_id');
			}
			else
			{
				$instrument_type_Inserted = $db->insertObject('#__content_types', $instrument_type);
			}


			echo '<a target="_blank" href="https://maikol.eu" title="Tempus">
				<img src="components/com_tempus/assets/images/vdm-component.jpg"/>
				</a>
				<h3>Upgrade to Version 1.0.2 Was Successful! Let us know if anything is not working as expected.</h3>';
		}
		return true;
	}
}
