BeelabUserBundle
================

[![Total Downloads](https://poser.pugx.org/beelab/user-bundle/downloads.png)](https://packagist.org/packages/beelab/user-bundle)
[![Build Status](https://travis-ci.org/Bee-Lab/BeelabUserBundle.png?branch=master)](https://travis-ci.org/Bee-Lab/BeelabUserBundle)
[![Test Coverage](https://codeclimate.com/github/Bee-Lab/BeelabUserBundle/badges/coverage.svg)](https://codeclimate.com/github/Bee-Lab/BeelabUserBundle/coverage)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/e4f35c31-3c00-4646-a23c-03476ccd64c2/big.png)](https://insight.sensiolabs.com/projects/e4f35c31-3c00-4646-a23c-03476ccd64c2)
[![knpbundles.com](http://knpbundles.com/Bee-Lab/BeelabUserBundle/badge)](http://knpbundles.com/Bee-Lab/BeelabUserBundle)

This bundle is a simple implementation of a Symfony user provider.

It provides a ``User`` entity with minimal fields (e.g. no "username", no "canonical" stuff), login functionality
and basic CRUD actions. Impersonation and password change are supported. Nothing more (no registration, no lost password).

If you use this bundle and you need a "lost password" functionality, please take a look to [BeelabUserPasswordBundle](https://github.com/Bee-Lab/BeelabUserPasswordBundle).


Only Doctrine ORM is supported (no ODM, no Propel).

Documentation
-------------

[Read the documentation](Resources/doc/index.md)

License
-------

This bundle is released under the LGPL license. See the [complete license text](Resources/meta/LICENSE).
