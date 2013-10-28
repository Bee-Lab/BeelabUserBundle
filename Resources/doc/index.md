BeelabUserBundle Documentation
==============================

## Installation

1. [Download BeelabUserBundle](#1-download-beelabuserbundle)
2. [Enable the bundle](#2-enable-the-bundle)
3. [Configuration](#3-configuration)
4. [Customizations](#4-customizations)

### 1. Download BeelabUserBundle

Run from terminal:

```bash
$ php composer.phar require beelab/user-bundle:1.*
```

### 2. Enable the bundle

Enable bundle in the kernel:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Beelab\UserBundle\BeelabUserBundle(),
    );
}
```

Create a ``User`` entity class.
Example:

```php
<?php
// src/Acme/DemoBundle/Entity

namespace Acme\DemoBundle\Entity

use Beelab\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Beelab\UserBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    // add your properties and methods, if any...
}
```

### 3. Configuration

Insert in main configuration:

```yaml
# app/config/config.yml

# BeelabUser Configuration
beelab_user:
    user_class: Acme\DemoBundle\Entity\User
```

Add routes:

```yaml
# app/config/routing.yml

beelab_user:
    resource: "@BeelabUserBundle/Controller/"
    type:     annotation
    prefix:   /
```

Enable security:

```yaml
# app/config/security.yml

security:
    encoders:
        Acme\DemoBundle\Entity\User:
            algorithm:        sha1
            encode_as_base64: false
            iterations:       1

    providers:
        administrators:
            entity: { class: AcmeDemoBundle:User }


    firewalls:
        main:
            pattern:    ^/
            form_login:
                use_referer: true
            logout: true
            anonymous: true
            switch_user: true
```

### 4. Customizations

#### Templates

You can customize templates as explained in official documentation:
http://symfony.com/doc/current/book/templating.html#overriding-bundle-templates

#### Controllers

You can customize controllers by extending bundle, like explained in official documentation:
http://symfony.com/doc/current/cookbook/bundles/inheritance.html#overriding-controllers

#### UserManager

You can creete you own UserManager, extending the one included in this bundle.
Then, add to configuration:

```yaml
# aoo/config/config.yml

beelab_user:
    user_manager_class: Acme\DemoBundle\Manager\UserManager 
```

#### Forms

You can extends bundle forms, then add to configuration:

```yaml
# aoo/config/config.yml

beelab_user:
    password_form_type: Acme\DemoBundle\Form\Type\PasswordFormType
    user_form_type:     Acme\DemoBundle\Form\Type\UserFormType
 
```
