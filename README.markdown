# yii-swiftmailer2

Basic [Yii Framework](http://www.yiiframework.com/) extension that wraps the [SwiftMailer](http://swiftmailer.org/) PHP library in some useful functions, and makes it easy to configure and access as a Component via Yii's app configuration system.

## Why another SwiftMailer extension for Yii?

It's true, there are three other SwiftMailer extensions in the [Yii Extensions](http://www.yiiframework.com/extensions/?category=9) repository right now. I had been using the classic [mail](http://www.yiiframework.com/extension/mail/) one myself, for quite a while. Here is why I made another one, and what it does differently:

 1. Provides an easy way render emails using Views, no matter where they are (call view with full path alias for extensions, modules, etc)
 2. Turn off email sending config setting (useful for testing environments)
 3. Override all email sending to a specific address config setting (useful for testing environments, so you don't SPAM your users)
 4. Message debug logging config setting (full text of emails including headers is logged to the `/runtime/email` directory, good for testing and debugging)

### Installation:

Drop it in to your `/extensions` directory.
(Make sure the `/runtime/email` directory is writable for logging to work.)
 
### Configuration:

    <?php // components config array for your application
    'components'=>array(
      'mail'=>array(
        'class' => 'ext.yii-swiftmailer2.SMail', // path to the extension
        //'log' => false, // log emails to /runtime/email -  - defaults to false
	    //'logDirectory' => 'email', // name of directory in the /runtime folder - defaults to 'email'
        //'disableSend'=>false, // turn off email sending - defaults to false
	    //'overrideToAddress'=>'catchall-test@youremail.com', // override all email sent from system to this address, for testings/debugging
        //'defaultTransport' => 'smtp', // defaults to 'mail'
        //'transportOptions' => array(
          //'host'=>'HOSTNAME',
          //'username'=>'USERNAME',
          //'password'=>'PASSWORD',
          //'port'=>'465',
          //'encryption'=>'ssl',
        //),
      ),
	),
    ?>
 
### Basic usage example:

    <?php 
    $message = Yii::app()->mail->newMessage(); // create a new email object
    $message->addTo('to@email.com'); // to email address ( can be called multiple times )
    $message->setFrom('from@email.com', 'Name of Address Owner'); // from email address
    $message->setBody('Email body content.'); // 
    $message->setSubject('Email subject line!'); // set your subject line
    if (!Yii::app()->mail->send($message)) {     // send the message using the global transport config settings
      Yii::log("Email send failed.", 'error', 'emailError');
    }
	?>

### Usage example with a View:

    <?php 
    $message = Yii::app()->mail->newMessage(); // create a new email object
    $message->addTo('to@email.com'); // to email address ( can be called multiple times )
    $message->setFrom('from@email.com', 'Name of Address Owner'); // from email address
    $message->setViewBody(
	  'application.views.mail.base', // declare the view to be called by renderPartial()
	  array(
        'message'=>'Message text', // pass in your variables to the view like normal
        'object'=>$object
      )
	);
    $message->setSubject('Email subject line!'); // set your subject line
    if (!Yii::app()->mail->send($message)) {     // send the message using the global transport config settings
      Yii::log("Email send failed.", 'error', 'emailError');
    }
	?>


Version: pre-beta :)  
Author: Evan Johnson  
http://splashlabsocial.com  

Last updated: 1.20.2012