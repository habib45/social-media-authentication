clockwork-sms
=============

A framework agnostic PHP wrapper for clockwork SMS API

Requirements:
* A [Clockwork](http://clockworksms.com) account with API Key

##Installation
Add the following to you ```composer.json``` file:
```json
"require": {
    "mjerwin/clockwork-sms": "~0.9",
},
```
Then run:
```bash
php composer.phar update
```

Alternatively, run the following command:
```bash
php composer.phar require mjerwin/clockwork-sms:~0.9
```

##Usage

###Get Account Balance
```php
const CLOCKWORK_API_KEY = 'abcdefghijklmnopqrstuvwxyz1234567890';

$client = new \MJErwin\Clockwork\ClockworkClient(CLOCKWORK_API_KEY);

$balance = $client->getBalance();
```

###Sending a Message
```php
const CLOCKWORK_API_KEY = 'abcdefghijklmnopqrstuvwxyz1234567890';

$message = new \MJErwin\Clockwork\Message();
$message->setNumber('07700900123');
$message->setContent('Check out this message!');

$client = new \MJErwin\Clockwork\ClockworkClient(CLOCKWORK_API_KEY);

$response = $client->sendMessage($message);
```

The ```sendMessage()``` method returns an instance of ```\MJErwin\Clockwork\MessageResponse()```.

You can use the following methods to get information from the response
* ```getTo()```
* ```getMessageId()```
* ```getErrorCode()```
* ```getErrorDescription()```

###Options
When sending a message, there are a number of optional parameters that can be given:
```php
// Set the name the message will be from
$message->setFromName('MJErwin');

// Set if truncating is enabled. If true, messages that are too big will be truncated
$client->setTruncateEnabled(true);

// Set the action taken if the message contains invalid chars.
$client->setInvalidCharAction(ClockworkClient::INVALID_CHAR_ACTION_RETURN_ERROR);
```
Class constants are provided for the values 1-3 for ```setInvalidCharAction()``` and are as follows:

Value | Constant | Description
--- | --- | ---
1 | ```\MJErwin\Clockwork\ClockworkClient::INVALID_CHAR_ACTION_RETURN_ERROR``` | Return an error
2 | ```\MJErwin\Clockwork\ClockworkClient::INVALID_CHAR_ACTION_REMOVE_CHARS``` | Remove the invalid characters
3 | ```\MJErwin\Clockwork\ClockworkClient::INVALID_CHAR_ACTION_REPLACE_CHARS``` | Replace invalid characters where possible, remove the rest

See http://www.clockworksms.com/doc/clever-stuff/xml-interface/send-sms/#param-invalidcharaction for more information.