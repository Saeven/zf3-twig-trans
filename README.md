# zf3-twig-trans

[![Build Status](https://travis-ci.org/Saeven/zf3-twig-trans.svg?branch=master)](https://travis-ci.org/Saeven/zf3-circlical-user)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/e9b80ae1c4c94159abe7bcb49b851cac)](https://www.codacy.com/app/saeven/zf3-twig-trans?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Saeven/zf3-twig-trans&amp;utm_campaign=Badge_Grade)
[![Codacy Badge](https://api.codacy.com/project/badge/Coverage/e9b80ae1c4c94159abe7bcb49b851cac)](https://www.codacy.com/app/saeven/zf3-twig-trans?utm_source=github.com&utm_medium=referral&utm_content=Saeven/zf3-twig-trans&utm_campaign=Badge_Coverage)
[![Total Downloads](https://poser.pugx.org/saeven/zf2-circlical-trans/downloads)](https://packagist.org/packages/saeven/zf2-circlical-trans)

Magic glue to create the expected experience when developing with ZfcTwig, the MvcTranslator (e.g., from within your controllers), and your twig templates using a custom {% trans %} that this module provides.  Usage is very simple! With this package included, "trans" becomes available in your templates.

> New! This module supports text domains!

Inspired by ZfcTwig and its extensions project at https://github.com/twigphp/Twig-extensions


## Requirements


|Item               |  Version     |
|-------------------|--------------|
|PHP                | 7+           |
|Zend Framework     | 3.*          |
|Gettext PHP Module | *            | 


## Installation

```js
composer require "saeven/zf3-twig-trans"
```

## Configuration


#### Loading the module: application.config.php

In your module's application.config.php, make sure you've got these modules loaded:

    'ZfcTwig',
    'CirclicalTwigTrans'

By loading CirclicalTwigTrans, you will be setting an alias from 'translator' to 'MvcTranslator'.  If you have an existing translator alias in your system, please remove it.

#### Managing Locale: Your Application's Module.php

It's assumed that you are managing locale in your app's bootstrap.  For example, in your Application module's onBootstrap:

```php
public function onBootstrap(MvcEvent $e)
{

    $translator = $e->getApplication()->getServiceManager()->get('translator');
    $translator
        ->setLocale( 'fr_CA' )
        ->setFallbackLocale( 'en_US' );
}
```

#### Proper Language File Setup

gettext imposes a certain file structure; language folders for a module would look like so:

```
module/
    Application/
        language/
            en_US/
                LC_MESSAGES/
                    default.mo
                    default.po
                    errors.mo
                    errors.po
            fr_CA/
                LC_MESSAGES/
                    default.mo
                    default.po
                    errors.mo
                    errors.po
```

The .mo files are truly the ones that matter.  The .po files, are the source files that are used to compile the .mo files with msgfmt.

The nomenclature, default.mo, indicates that that file contains text strings for the 'default' text domain.  In other words, the name of the files is vital to proper functionality.  

You need to tweak your translator configuration to support this file structure, it's very simple.  Per module:

```php
'translator' => [
    
    'translation_file_patterns' => [
        [
            'locale'        => 'en_US',
            'type'          => 'gettext',
            'base_dir'      => __DIR__ . '/../language',
            'pattern'       => '%s/LC_MESSAGES/default.mo',
            'text_domain'   => 'default',
        ],
        [
            'locale'        => 'en_US',
            'type'          => 'gettext',
            'base_dir'      => __DIR__ . '/../language',
            'pattern'       => '%s/LC_MESSAGES/errors.mo',
            'text_domain'   => 'errors',
        ],
    ],
],
```

Very important: there's a critical difference between Zend's translator implementations, and gettext's implementation.  The Zend translator
will allow you to use multiple .mo files for a same domain, but gettext does not support this behavior.  To ensure that both the Twig translations, and
your in-app translations (e.g., `$translator->translate('foo')`) work properly, your domain names must be unique.  A good practice is to name your domain, 
after your module.

## Usage

Included tests support all flavors of trans, adding direct support for domain overrides from within the template.  These syntax structures all work:


#### Translate 'Sorry' from text-domain 'errors'
```twig
{% trans from "errors" %}Sorry{% endtrans %}
```

#### Translate 'Home' from the 'default' domain
```twig
{% trans %}Home{% endtrans %}
```


#### Translate "A 404 error occurred" from the 'default' domain
```twig
{% trans "A 404 error occurred" %}
```

#### Translate with pluralization from the 'default' domain
```twig
{% set birds = 422 %}
{% trans %}
    There is one bird
{% plural birds %}
    There are {{ birds }} birds
{% endtrans %}
```


#### Translate with pluralization from the 'errors' domain
```twig
{% set birds = 422 %}
{% trans from "errors" %}
    There is one bird
{% plural birds %}
    There are {{ birds }} birds
{% endtrans %}
```

#### Translate with notes
```twig
{% trans %}
    Hello User!
{% notes %}
    This is used in the greeting area.
{% endtrans %}
```

#### Controllers
Usage in controllers doesn't change.

```php
/** @var Zend\I18n\Translator\Translator $tr */
$tr = $sm->get('translator');

$tr->translate( 'Home' );
$tr->translate( 'Sorry', 'errors' );


$num = 422;
sprintf( $tr->translatePlural( "There is one bird", "There are %d birds", 422 ), $num );
sprintf( $tr->translatePlural( "There is one bird", "There are %d birds", 422, 'errors' ), $num );
```


You can test it with the ZF3 Skeleton, by translating "Home" to "fr_CA" which becomes "Acceuil" (good test).

## Enjoy!

Let me know if you find any issues.  I use this module as well in production, so definitely want to hunt bugs down!
