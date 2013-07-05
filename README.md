GoogleTranslateBundle
=====================

[![Build Status](https://secure.travis-ci.org/eko/GoogleTranslateBundle.png?branch=master)](http://travis-ci.org/eko/GoogleTranslateBundle)

Features
--------

 * Detect language used for a string
 * Translate a string from a source language to a target one
 * Translate a string into a target language by using language auto-detection (consume 1 more API call)

Installation
------------

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

Notice: this will consume a detector API call.