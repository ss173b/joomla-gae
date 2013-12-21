<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Mail
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

require_once 'google/appengine/api/app_identity/AppIdentityService.php';
require_once 'google/appengine/api/mail/Message.php';

use google\appengine\api\app_identity\AppIdentityService;
use google\appengine\api\mail\Message;

/**
 * Email Class.  Provides a common interface to send email from the Joomla! Platform
 *
 * @package     Joomla.Platform
 * @subpackage  Mail
 * @since       11.1
 */
class JMailGae extends JMail
{
	/* @var $acceptedHeaders array List of Headers accepted by Google AppEngine */
	protected $acceptedHeaders = array(
		'In-Reply-To',
		'List-Id',
		'List-Unsubscribe',
		'On-Behalf-Of',
		'References',
		'Resent-Date',
		'Resent-From',
		'Resent-To',
	);

	/**
	 * @var    array  JMailGae instances container.
	 * @since  11.3
	 */
	protected static $instances = array();

	/**
	 * Returns the global email object, only creating it
	 * if it doesn't already exist.
	 *
	 * NOTE: If you need an instance to use that does not have the global configuration
	 * values, use an id string that is not 'Joomla'.
	 *
	 * @param   string  $id  The id string for the JMailGae instance [optional]
	 *
	 * @return  JMailGae  The global JMailGae object
	 *
	 * @since   11.1
	 */
	public static function getInstance($id = 'Joomla')
	{
		if (empty(self::$instances[$id]))
		{
			self::$instances[$id] = new static();
		}

		return self::$instances[$id];
	}

	/**
	 * Send the mail
	 *
	 * @return  mixed  True if successful; JError if using legacy tree (no exception thrown in that case).
	 *
	 * @since   11.1
	 * @throws  RuntimeException
	 */
	public function Send()
	{
		if (JFactory::getConfig()->get('mailonline', 1))
		{
			@$result = $this->_Send();

			if ($result == false)
			{
				if (class_exists('JError'))
				{
					JError::raiseNotice(500, $this->ErrorInfo);

					return false;
				}
				else
				{
					throw new RuntimeException(sprintf('%s::Send failed: "%s".', $this->ErrorInfo));
				}
			}

			return $result;
		}
		else
		{
			JFactory::getApplication()->enqueueMessage(JText::_('JLIB_MAIL_FUNCTION_OFFLINE'));

			return false;
		}
	}

	/**
	 * Sends email via Google Mail API
	 * This method overrides PHPMailer's send()
	 *
	 * @return bool
	 */
	protected function _Send()
	{
		// PHP Mailer pre-processing
		if(!$this->PreSend())
			return false;

		try
		{
			$message = new Message();

			// Set Sender
			$message->setSender($this->Sender);

			// Add Reply-To
			if (count($this->ReplyTo))
				$message->setReplyTo(current($this->ReplyTo));

			// Add To
			foreach($this->to as $to)
				$message->addTo($to[0]);

			// Add CC
			foreach($this->cc as $cc)
				$message->addCc($cc[0]);

			// Add BCC
			foreach($this->bcc as $bcc)
				$message->addBcc($bcc[0]);

			// Set Subject
			$message->setSubject($this->Subject);

			// Set Text Body
			if ($this->AltBody)
				$message->setTextBody($this->AltBody);

			// Set Text Body
			if ($this->isHtml())
				$message->setHtmlBody($this->Body);

			// Add custom headers
			foreach(explode("\n", $this->MIMEHeader) as $header)
			{
				list($headerName, $headerValue) = explode(': ', $header, 2);

				// Check if header can be accepted by GAE Mail API
				if (in_array($headerName, $this->acceptedHeaders))
				{
					$message->addHeader($headerName, $headerValue);
				}
			};

			// Add attachements
			// todo: implement and test
			// $message->addAttachmentArray($this->attachment);

			// Send the email
			$message->send();

			return true;
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}
	}
}
