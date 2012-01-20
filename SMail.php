<?php
/**
 * SMail class file.
 *
 * @author Evan Johnson <thaddeusmt -AT- gmail -DOT- com>
 * @link https://github.com/thaddeusmt/yii-swiftmailer2
 * @copyright Copyright &copy; 2011 SplashLab Social  http://splashlabsocial.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 */

class SMail extends CApplicationComponent
{
  /**
   * @var bool whether to actually send mail or not (for debugging).  Defaults to false
   */
  public $disableSend = false;

  /**
   * @var string email address - email address to send all emails to (for debugging).  Defaults to null
   */
  public $overrideToAddress = null;

  /**
   * @var bool whether or not to log emails as files to the runtime directory. Defaults to false
   */
  public $log = false;

  /**
   * @var string directory to save logged emails to Defaults to false
   */
  public $logDirectory = false;

  /**
   * @var string Defaults to 'mail'
   * More options: http://swiftmailer.org/docs/overview.html#transports
   */
  public $defaultTransport = 'mail';

  /**
   * @var string options specific to the transport type being used.
   * To set options for STMP, set this attribute to an array where the keys are the option names and the values are their
   * values.  Possible options for SMTP are:
   * <ul>
   * 	<li>host</li>
   * 	<li>username</li>
   * 	<li>password</li>
   * 	<li>port</li>
   * 	<li>encryption</li>
   * 	<li>timeout</li>
   * 	<li>extensionHandlers</li>
   * </ul>
   * See the SwiftMailer documentation for the option definitions. http://swiftmailer.org/docs/overview.html#transports
   */
  public $transportOptions;

  /**
   * @var Swift_MailTransport the default mail transport method
   */
  private $_transport;

  /**
   * @var SYiiMail the private Mailer instance
   */
  private $_mailer;

  /**
   * Calls the registerScripts() method
   */
  public function init()
  {
    Yii::import('ext.swiftmailer2.*');
    require dirname(__FILE__).'/lib/classes/Swift.php';
		Yii::registerAutoloader(array('Swift','autoload'));
		require dirname(__FILE__).'/lib/swift_init.php';
    parent::init();
  }

  /**
   * This returns the global configured SYiiMail object, which extends the base Swift_Mailer
   * You can call all of the usual Swift_Mailer methods
   * @return SYiiMail SwiftMailer singleton
   */
  public function getMailer()
  {
    if ($this->_mailer===null) {
      $options = array(
        'disableSend'=>$this->disableSend,
        'log'=>$this->log,
        'overrideToAddress'=>$this->overrideToAddress,
        'logDirectory'=>$this->logDirectory,
      );
      $this->_mailer = new SYiiMail($this->getTransport(), $options);
    }
    return $this->_mailer;
  }

  /**
   * Easy way to request a new SYiiMessage message instance
   *
   * @param string $subject subject line of the email message
   * @param string $body body of the email message
   * @param string $contentType content type of the email message
   * @param string $charset character set of the email message
   * @return SYiiMessage new SYiiMessage object (which extends Swift_Message)
   */
  public function newMessage($subject = null, $body = null, $contentType = null, $charset = null)
  {
		return SYiiMessage::newInstance($subject,$body,$contentType,$charset);
	}

  /**
   * Get the global default mail transport method as defined in the app config
   *
   * @return Swift_MailTransport
   */
  public function getTransport()
  {
    if ($this->_transport===null) {
      switch ($this->defaultTransport) {
        case 'mail':
          $this->_transport = Swift_MailTransport::newInstance();
          if ($this->transportOptions !== null)
            $this->_transport->setExtraParams($this->transportOptions);
          break;
        case 'sendmail':
          $this->_transport = Swift_SendmailTransport::newInstance();
          if ($this->transportOptions !== null)
            $this->_transport->setExtraParams($this->transportOptions);
          break;
        case 'smtp':
          $this->_transport = Swift_SmtpTransport::newInstance();
          foreach ($this->transportOptions as $option => $value)
            $this->_transport->{'set'.ucfirst($option)}($value); //sets option with the setter method
          break;
      }
    }
    return $this->_transport;
  }

  /**
   * Send a SYiiMessage using the global default mailer and transport
   *
   * @param SYiiMessage $message
   * @param null $failedRecipients
   * @return int
   */
  public function send(SYiiMessage $message, &$failedRecipients = null)
  {
		return $this->getMailer()->send($message, $failedRecipients);
	}


}
