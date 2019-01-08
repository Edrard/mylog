# My Log
My Log, extend of Monolog to run static

Simple Example
```

use edrard\Log\MyLog;


MyLog::info('info');

MyLog::init('logs','log',array(new NativeMailerHandler('test@example.com', '[ERROR]', 'outgoing@example.com', Logger::ERROR, false) );
MyLog::critical('Critical');
MyLog::warning('Warning');
MyLog::error('Error');


MyLog::error('Error',array(),'log2');
MyLog::info('info',array(),'log2');

```