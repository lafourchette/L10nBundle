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

    l10n:
        localization_fallback: en # Fallback localization, used when no explicit Localization is asked or localization not found. Value may be string or integer
        locale_fallback: en_US # Fallback locale, to manage I10N values
        manager: l10n_bundle.manager.l10n_yaml # The service's name of the manager you want to use
        
    # specific options for managers
    
    # yaml
        yaml:
            data_file: /path/to/data.yaml
    
            # mongodb
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

