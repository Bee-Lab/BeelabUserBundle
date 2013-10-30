BeelabUserBundle
================

[![Total Downloads](https://poser.pugx.org/beelab/user-bundle/downloads.png)](https://packagist.org/packages/beelab/user-bundle)


This bundle is a simple implementation of a Symfony2 user provider.

It provides a ``User`` entity with minimal fields (e.g. no "username", no "canonical" stuff), login funcionality
and basic CRUD actions. Impersonation and password change are supported. Nothing more (no registration, no lost password).

Only Doctrine ORM is supported (no ODM, no Propel).

Documentation
-------------

[Read the documentation](Resources/doc/index.md)

License
-------

This bundle is released under the LGPL license. See the [complete license text](Resources/meta/LICENSE).
