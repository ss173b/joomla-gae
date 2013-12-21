<?php
defined('_JEXEC') or die;

/**
 * Example Content Plugin
 *
 * @package     Gmort.Gae.PLugin
 * @subpackage  Content.joomla
 *
 */
class PlgContentGaeconfig extends JPlugin
{


	/**
	 * When preparing the com_config.application configuration form, merge some custom xml for the database in order to enable gaemysql and gaemysqli.
	 *
	 * @param   JForm   $form  The context for the content passed to the plugin.
	 * @param   array    $data      the data currently loaded for this form, no need for it at the moment.
	 * @return  boolean
	 *
	 * @since   3.1
	 */
	public function onContentPrepareForm($form,$data)
	{

		// The form we want to process
		$formName = 'com_config.application';

		// Our custom XML definition for the database
		$fileName = __DIR__.'/forms/'.$formName.'.xml';

		// Only process com_config application forms and only process it if the override file exists
		if ($form->getName() == $formName
		&& is_file($fileName))
		{
			// Attempt to load the XML file as a simpleXml object
			$gaeXml = simplexml_load_file($fileName);

			// Replace XML nodes[aka Joomla Form fields] in this form with ones that have been defined in our replacement file
			$form->load($gaeXml);


			// Add option for GAE Mailer
			// todo: fixme https://github.com/joomla/joomla-cms/pull/2708
			$fieldMailer = $form->getXML()->xpath('//field[@name="mailer"]');
			$optionGaeMailer = $fieldMailer[0]->addChild('option', 'COM_CONFIG_FIELD_VALUE_GAE_MAIL');
			$optionGaeMailer->addAttribute('value', 'gae');

			// Make GAE Mailer default
			$form->getField('mailer')->default = 'gae';


			// Load our language files
			$app = JFactory::getApplication();
			$app->getLanguage()->load('plg_gaeconfig', __DIR__);

		}


		// We don't have to return anything since the form was passed to us by reference and we already modified it in place
		return true;
	}
}
