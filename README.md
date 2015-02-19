# zf2-circlical-trans

Magic glue to create the expected experience when developing with ZfcTwig, the MvcTranslator (e.g., from within your controllers), and your twig templates using a custom {% trans %} that this module provides.  Usage is very simple! With this package included, "trans" becomes available in your templates.

> New! This module supports text domains!

Inspired by ZfcTwig and its extensions project at https://github.com/twigphp/Twig-extensions


### Requirements


|Item               |  Version     |
|-------------------|--------------|
|PHP                | 5.5+         |
|Zend Framework     | 2.3.*        |
|Gettext PHP Module | *            | 


### Installation

Add this line to composer, and update:

```js
"saeven/zf2-circlical-trans": "dev-master",
```

### Configuration


#### application.config.php

In your module's application.config.php, make sure you've got these modules loaded:

    'ZfcTwig',
    'CirclicalTwigTrans'

By loading CirclicalTwigTrans, you will be setting an alias from 'translator' to 'MvcTranslator'.  If you have an existing translator alias in your system, please remove it.

#### Your Application's Module.php

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

Traditional gettext imposes a certain file structure.  Yea, yea, messing with your app is a PITA, but it's a small price to pay when the performance benefits of true gettext are considered. This also enables domain support.  You want this.

My language setup in my Application Module looks like so:
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

The default.mo indicates that that file, contains text strings for the 'default' text domain.  

You need to tweak your translator configuration to support this file structure, it's very simple:

```php
'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'          => 'gettext',
                'base_dir'      => __DIR__ . '/../language',
                'pattern'       => '%s/LC_MESSAGES/default.mo',
                'text_domain'   => 'default',
            ),
            array(
                'type'          => 'gettext',
                'base_dir'      => __DIR__ . '/../language',
                'pattern'       => '%s/LC_MESSAGES/errors.mo',
                'text_domain'   => 'errors',
            ),
        ),
    ),
```

*The MvcTranslator will behave properly, it does support domains*


### Usage

I tried to implement all the right flavors of trans, adding direct support for domain overrides from within the template.  These syntax structures all work:


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


You can test it with the ZF2 Skeleton, by translating "Home" to "fr_CA" which becomes "Acceuil" (good test).

## Enjoy!

Let me know if you find any issues.  I use this module as well in production, so definitely want to hunt bugs down!
