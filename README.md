# Mailgun

[![Latest Version on Packagist](https://img.shields.io/packagist/v/cronixweb/mailgun.svg?style=flat-square)](https://packagist.org/packages/cronixweb/mailgun)
[![Total Downloads](https://img.shields.io/packagist/dt/cronixweb/mailgun.svg?style=flat-square)](https://packagist.org/packages/cronixweb/mailgun)

A Wrapper class to send emails via Mailgun API. Supports both html & templates

## Installation

You can install the package via composer:

```bash
composer require cronixweb/mailgun
```



## Usage

### Initialize Mailgun
You can provide API key directly or provide environment variable name to retrieve API KEY. By default, it will use `MAILGUN_API_KEY` environment variable to retrieve the api key.

```php
$mailgun = \CronixWeb\Mailgun\Mailgun::init(apiKey: 'API_KEY');
```

```php
$mailgun = \CronixWeb\Mailgun\Mailgun::init(envKey: 'MAILGUN_API_KEY');
```

### Send HTML email

```php
$mailgun->domain('mg.yourdomain.com')
    ->from('from@yourdomain.com','My App')
    ->to('johndoe@gmail.com','John Doe')
    ->subject('Test email with Mailgun')
    ->html('<h1>Hello World!</h1>')
    ->send();
```

### Add Multiple Recipients

```php
$mailgun
    ->to('johndoe@gmail.com','John Doe')
    ->to('tigernexon@gmail.com','Tiger Nexon')
    ->send();
```

```php
$mailgun
    ->to([
        [
            'email' => 'johndoe@gmail.com',
            'name' => 'John Doe'
        ],
        [
            'email' => 'tigernexon@gmail.com',
            'name' => 'Tiger Nexon'
        ]
    ])
    ->send();
```

### Send template email

```php
$mailgun->domain('mg.yourdomain.com')
    ->from('from@yourdomain.com','My App')
    ->to('johndoe@gmail.com','John Doe')
    ->subject('Test email with Mailgun')
    ->template('template-id')
    ->variables(['name'=>'John'])
    ->send();
```

### Send Attachments

```php
$mailgun->domain('mg.yourdomain.com')
    ->from('from@yourdomain.com','My App')
    ->to('johndoe@gmail.com','John Doe')
    ->subject('Test email with Mailgun')
    ->template('template-id')
    ->attachment('path/to/attachment.pdf','Invoice.pdf')
    ->attachment('path/to/attachment.pdf','Shipment.pdf')
    ->send();
```

### Enable Debugging

You can view the response of api request sent to mailgun by enabling debug flag. You can enable the flag with `debug()` method.

```php
$response = $mailgun->domain('mg.yourdomain.com')
    ->from('from@yourdomain.com','My App')
    ->to('johndoe@gmail.com','John Doe')
    ->subject('Test email with Mailgun')
    ->template('template-id')
    ->debug(true)
    ->send();
```


## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Akash Pate](https://github.com/akashpate1)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
