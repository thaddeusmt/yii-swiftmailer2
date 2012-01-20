<?php
/**
 * SYiiMail class file.
 *
 * This extends the Swift_Mailer class, adding some send override and logging abilities
 *
 * @author Evan Johnson <thaddeusmt -AT- gmail -DOT- com>
 * @link https://github.com/thaddeusmt/yii-swiftmailer2
 * @copyright Copyright &copy; 2011 SplashLab Social  http://splashlabsocial.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 */
class SYiiMail extends Swift_Mailer
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
   * @param Swift_Transport $transport the transport method to use
   * @param array $options sets the class properties passed in as and array
   */
  public function __construct(Swift_Transport $transport, $options)
  {
    foreach($options as $key=>$option) {
      $this->$key = $option;
    }
    parent::__construct($transport);
  }

  /**
   * Send the given Message like it would be sent in a mail client.
   *
   * If $log is set, the message is also logged to the filesystem
   * If $overrideToAddress is set, email gets sent to the specified address instead of the intended recipients
   * If $disableSend is set, the message is not sent
   *
   * @param Swift_Mime_Message $message
   * @param null $failedRecipients
   * @return int The return value is the number of recipients who were accepted for delivery.
   */
  public function send(Swift_Mime_Message $message, &$failedRecipients = null)
  {
    // if logging turned on (and transport not set to File), do the file transport
    if ($this->log) {
      $this->saveToFile($message);
    }

    // override the TO message, so you can test without spamming your users
    if ($this->overrideToAddress) {
      $message->setTo($this->overrideToAddress);
    }

    // else, do the default transport
    if ($this->disableSend) {
      return 1; // still return SUCCESS - that it "sent" without an error
    } else {
      return parent::send($message, $failedRecipients);
    }
  }

  /**
   * Save the complete message to the filesystem for testing and debugging
   *
   * @param Swift_Mime_Message $message the email message to log
   * @throws CException if the file system is not writeable
   */
  protected function saveToFile(Swift_Mime_Message $message) {
    if (!$this->logDirectory) {
      $this->logDirectory = Yii::app()->getRuntimePath(). DIRECTORY_SEPARATOR ."email";
    }
    if(($logPath=realpath($this->logDirectory))===false || !is_dir($logPath) || !is_writable($logPath)) {
      mkdir($this->logDirectory,0777,true);
    }
    $fileName = 'SwiftMailer_' . $_SERVER['REQUEST_TIME'] . '_' . mt_rand() . '.tmp';
    $file = $this->logDirectory . DIRECTORY_SEPARATOR . $fileName;
    if (!is_writable(dirname($file))) {
      throw new CException('Email log directory "'.dirname($file).'" does not exist or is not writable');
    }
    if (!file_put_contents($file, $message->toString())) {
      throw new CException('Unable to log mail');
    }
  }

}
