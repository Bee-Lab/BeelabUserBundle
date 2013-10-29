BeelabUserBundle Documentation
==============================

## Installation

1. [Install BeelabUserBundle](#1-install-beelabuserbundle)
2. [Configuration](#3-configuration)
3. [Customizations](#4-customizations)
4. [Commands](#5-commands)

### 1. Install BeelabUserBundle

Run from terminal:

```bash
$ php composer.phar require beelab/user-bundle:1.*
```

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

### 2. Configuration

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

This is just an example, your real security configuration may vary.
It's important here to use the name of your entity in ``encoders`` and ``providers``.

### 3. Customizations

#### Templates

You can customize templates as explained in
[official documentation](http://symfony.com/doc/current/book/templating.html#overriding-bundle-templates).

#### Controllers

You can customize controllers by extending bundle, like explained in
[official documentation](http://symfony.com/doc/current/cookbook/bundles/inheritance.html#overriding-controllers).

#### UserManager

You can create you own UserManager, extending the one included in the bundle.
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

#### Validation

Constraints are in ``User`` entity, so you can ovverride them in your entity.
See [Doctrine docs](http://docs.doctrine-project.org/en/latest/tutorials/override-field-association-mappings-in-subclasses.html).
Controllers use three validations groups: "create", "update", and "password".
First two groups are for creating and editing a user, the last one is for password change.
You can override which group is used in each form just by overriding actions in controllers (see above).

#### Routes

Routes are annotated in controllers, so you can override them just by overriding controllers (see above).

### 4. Commands

Two commands are available, to create a new user and to promote an existing user.
You can see them by typing:

```bash
$ php app/console list beelab
```
