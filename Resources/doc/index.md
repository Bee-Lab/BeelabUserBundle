BeelabUserBundle Documentation
==============================

This version of the bundle requires Symfony 2.3.

## Installation

1. [Download BeelabUserBundle](#1-download-beelabuserbundle)
2. [Enable the Bundle](#2-enable-the-bundle)
3. [Usage](#3-usage)
4. [Layout](#4-layout)

### 1. Download BeelabUserBundle

Run from terminal:
``` bash
$ php composer.phar require beelab/user-bundle:1.*
```

### 2. Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Beelab\UserBundle,
        // ...
    );
}
```

Create a User entity class.
Example:
``` php
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
    // your properties and methods...
}

```

Insert in your configuration:
``` yaml
# BeelabUser Configuration
beelab_user:
    user_class: Acme\DemoBundle\Entity\User
```

### 3. Usage

To be written... (TODO)
