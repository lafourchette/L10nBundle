L10nBundle
==========

[![Build Status](https://travis-ci.org/lafourchette/L10nBundle.svg?branch=master)](https://travis-ci.org/lafourchette/L10nBundle) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/lafourchette/L10nBundle/badges/quality-score.png?s=77af0dc7eed34c47d0b264469ab2031c21d6f968)](https://scrutinizer-ci.com/g/lafourchette/L10nBundle/) [![Code Coverage](https://scrutinizer-ci.com/g/lafourchette/L10nBundle/badges/coverage.png?s=d6df93262b412bb71ecb7fd1365077ad919e660a)](https://scrutinizer-ci.com/g/lafourchette/L10nBundle/)

Overview
--------

The aim of L10nBundle is to provide a very simple way to deal with L10N, like I18N with [Symfony translator](http://symfony.com/doc/current/components/translation/usage.html "Using the Translator").

L10N data can be store wherever you want.
Currently, L10nBundle supports YaML file and MongoDb.


Configuration
-------------

### Global
    l10n:
        localization_fallback: en # Fallback localization, used when no explicit Localization is asked or localization not found. Value may be string or integer
        locale_fallback: en_US # Fallback locale, to manage L10N values
        manager: yaml # The name of the manager you want to use currently yaml or mongodb

You can use the parameters of your symfony configuration in your values.
Example:

    #parameters.yml
    parameters:
        my_environment_extension: local

    #l10n.yml
    l10n:
        my_url:
            gb: www.domain.%my_environment_extension%

The value will be resolved like this:

    my_url => www.domain.local

### yaml
    yaml:
        data_file: /path/to/main.yaml

#### You can import files

    imports:
        - { resource: "../../src/Bundle/MyBundle/Resources/translations/l10n.yml" }

#### Configuration is under l10n key

    l10n:
        registration:
            label:
                fr: FRA
                es: SPA
            name:
                fr: french name
                es: spanish name

You can use the keys `registration.label` and `registration.name`.

#### Simple translation is provided
Use an array as value:

    l10n:
        registration.label:
            fr:
                - {locale: fr, value: FRA_fr}
                - {locale: en, value: FRA_en}

### mongodb
    mongodb:
        host: 127.0.0.1
        port: 27017
        username: test
        password: T3$t
        database: test_db
        
    
Usage
-----

Example in a controler

    $l10n = $this->getContainer()->get('l10n_bundle.business.l10n_provider');
    $l10n->getL10n('key', 'idLoc');

In a twig template you can use the l10n filter

    {{ 'key'|l10n }}
