GoogleTranslateBundle
=====================

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/41d3d242-a0fe-424c-8cb1-65327f89df11/big.png)](https://insight.sensiolabs.com/projects/41d3d242-a0fe-424c-8cb1-65327f89df11)

[![Build Status](https://secure.travis-ci.org/eko/GoogleTranslateBundle.png?branch=master)](http://travis-ci.org/eko/GoogleTranslateBundle)

Features
--------

 * Detect language used for a string
 * Translate a string from a source language to a target one
 * Translate a string into a target language by using language auto-detection (consume 1 more API call)
 * Retrieve all languages available on API and obtain language names in a given language
 * Profile detector / translate / languages list API calls in the Symfony profiler!

Installation
------------

Add the bundle to your `composer.json` file:

```json
{
    "require" :  {
        "eko/googletranslatebundle": "dev-master"
    }
}
```

Add this to app/AppKernel.php

```php
<?php
    public function registerBundles()
    {
        $bundles = array(
            ...
            new Eko\GoogleTranslateBundle\EkoGoogleTranslateBundle(),
        );

        ...

        return $bundles;
    }
```

Configuration
-------------

### Edit app/config.yml

The following configuration lines are required:

```yaml
eko_google_translate:
    api_key: <your key api string>
```

Usages
------

### Detect a string language

Retrieve the detector service and call the `detect()` method:

```php
$detector = $this->get('eko.google_translate.detector');
$value = $detector->detect('Hi, this is my string to detect!');
// This will return 'en'
```

### Translate a string

Retrieve the translator service and call the `translate()` method:

```php
$translator = $this->get('eko.google_translate.translator');
$value = $translator->translate('Hi, this is my text to detect!', 'fr', 'en');
// This will return 'Salut, ceci est mon texte à détecter!'
```

### Translate a string from unknown language (use detector)

Retrieve the translator service and call the `translate()` method without the source (third) parameter:

```php
$translator = $this->get('eko.google_translate.translator');
$value = $translator->translate('Hi, this is my text to detect!', 'fr');
// This will return 'Salut, ceci est mon texte à détecter!'
```

### Obtain all languages codes available

Retrieve the languages service and call the `get()` method without any argument:

```php
$languages = $this->get('eko.google_translate.languages')->get();
// This will return:
// array(
//     array('language' => 'en'),
//     array('language' => 'fr'),
//     ...
// )
```

### Obtain all languages codes available with their names translated

Retrieve the languages service and call the `get()` method with a target language argument:

```php
$languages = $this->get('eko.google_translate.languages')->get('fr');
// This will return:
// array(
//     array('language' => 'en', 'name' => 'Anglais'),
//     array('language' => 'fr', 'name' => 'Français'),
//     ...
// )
```


Notice: this will consume a detector API call.