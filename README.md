# zf2-circlical-trans

Magic glue to connect ZfcTwig, the ZF2 Translator, and Twig's {% trans %}.  Usage is very simple; with this package included, modify your app config to add the "trans" extension this codebase provides.


### Requirements


|Item              |  Version     |
|------------------|--------------|
|PHP               | 5.5+         |
|Zend Framework    | 2.3.*        |


### Installation

Add this line to composer, and update:

```js
"saeven/zf2-circlical-trans": "dev-master",
```

### Configuration

In your module's application.config.php, make sure you've got these modules loaded:

    'ZfcTwig',
    'CirclicalTwigTrans'

By loading CirclicalTwigTrans, you will be setting an alias from 'translator' to 'MvcTranslator'.  If you have an existing translator alias in your system, please remove it.

### Usage

Use 

```twig
{% trans "This is a sentence" %} 
```
to translate that string.

You can also do pluralization with:

```
{% set name = "Morpheus" %}
{% trans %}
   Hi {{ name }}, I took one blue pill.
{% plural pill_count %}
   Hi {{ name }}, I took {{ pill_count }} blue pills.
{% endtrans %}
```

You can test it with the ZF2 Skeleton, by translating "Home" to "fr_CA" which becomes "Acceuil" (good test).

#### Known Limitations

Doesn't support text domains, yet.
