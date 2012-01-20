<?php
/**
 * SYiiMessage class file.
 *
 * This extends the Swift_Message class, adding support for rendering a Yii view file as the body
 *
 * @author Evan Johnson <thaddeusmt -AT- gmail -DOT- com>
 * @link https://github.com/thaddeusmt/yii-swiftmailer2
 * @copyright Copyright &copy; 2011 SplashLab Social  http://splashlabsocial.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 */

class SYiiMessage extends Swift_Message
{

  /**
   * Returns a new instance of itself
   *
   * @static
   * @param string $subject subject line of the email message
   * @param string $body body of the email message
   * @param string $contentType content type of the email message
   * @param string $charset character set of the email message
   * @return SYiiMessage
   */
  public static function newInstance($subject = null, $body = null, $contentType = null, $charset = null)
  {
    return new self($subject, $body, $contentType, $charset);
  }

  /**
   * Render the view passed in as the body content of the message
   *
   * @param $view view file path (yii path alias)
   * @param array $variables parameters to render in the view
   * @param string $contentType the content type - defaults to HTML
   * @return Swift_Mime_MimePart
   */
  public function setViewBody($view, $variables=array(), $contentType = 'text/html')
  {
		$body = $this->renderPartial($view, $variables);
		return parent::setBody($body, $contentType);
	}

  /**
   * Load and render the view
   *
   * @param $view view file path (yii path alias)
   * @param null $data parameters to render in the view
   * @return string the rendered view
   * @throws CException if the view cannot be found
   */
  public function renderPartial($view,$data=null)
	{
		if(($viewFile=$this->getViewFile($view))!==false)
		{
      $output=CConsoleCommand::renderFile($viewFile,$data,true);
		  return $output;
		}
		else
			throw new CException(Yii::t('yii','{controller} cannot find the requested view "{view}".',
				array('{controller}'=>get_class($this), '{view}'=>$view)));
	}

  /**
   * Resolves the path and loads the view file
   * (This is a simplified function from the console application)
   *
   * @param $viewName view file path (yii path alias)
   * @return bool|string view file - false if it cannot find it
   */
  protected function getViewFile($viewName)
	{
	  if(empty($viewName))
			return false;

	  $extension='.php';
		$viewFile=Yii::getPathOfAlias($viewName);

		if(is_file($viewFile.$extension))
			return Yii::app()->findLocalizedFile($viewFile.$extension);
		else
			return false;
	}

}
