BeelabUserBundle Documentation
==============================

## Installation

1. [Install BeelabUserBundle](#1-install-beelabuserbundle)
2. [Configuration](#2-configuration)
3. [Customizations](#3-customizations)
4. [Commands](#4-commands)
5. [Events](#4-events)

### 1. Install BeelabUserBundle

Run from terminal:

```bash
$ composer require beelab/user-bundle
```

Enable bundle in the kernel:

```php
<?php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = [
        // ...
        new Beelab\UserBundle\BeelabUserBundle(),
    ];
}
```

If you want pagination in users' administration, install also [KnpPaginatorBundle](https://github.com/KnpLabs/KnpPaginatorBundle).

### 2. Configuration

Create a `User` entity class.
Example:

```php
<?php
// src/AppBundle/Entity
namespace AppBundle\Entity;

use Beelab\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
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
    user_class: AppBundle\Entity\User
```

Add routes:

```yaml
# app/config/routing.yml

beelab_user:
    resource: "@BeelabUserBundle/Controller/"
    type:     annotation
```

Enable security:

```yaml
# app/config/security.yml

security:
    encoders:
        AppBundle\Entity\User:
            # See http://symfony.com/doc/current/reference/configuration/security.html#using-the-bcrypt-password-encoder
            algorithm: bcrypt
            # Also, since bcrypt is a bit expensive, you likely want to override it in test env

    providers:
        administrators:
            entity: { class: AppBundle:User }

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
It's important here to use the name of your entity in `encoders` and `providers`.

> **Warning**: if you customize the route prefix, don't forget to explicit `login_path` and other options in your
> firewall. See [security reference](http://symfony.com/doc/current/reference/configuration/security.html#the-login-form-and-process) for more details.
>

### 3. Customizations

#### Templates

You can customize templates as explained in
[official documentation](http://symfony.com/doc/current/templating.html#overriding-bundle-templates).

All template use a default layout. If you want to use your custom layout, you can
specify it in configuration:
```yaml
# app/config/config.yml

# BeelabUser Configuration
beelab_user:
    layout: "::myCustomLayout.html.twig"
```

#### Controllers

You can customize controllers by extending bundle, like explained in
[official documentation](http://symfony.com/doc/current/bundles/inheritance.html#overriding-controllers).

#### UserManager

You can create you own UserManager, extending the one included in the bundle.
Then, add to configuration:

```yaml
# app/config/config.yml

beelab_user:
    user_manager_class: AppBundle\Manager\UserManager
```

If you need a lighter UserManager, you can use `LightUserManager`, that has less
dependencies than UserManager. For example, you can use it for Facebook integration with
[FOSFacebookBundle](https://github.com/FriendsOfSymfony/FOSFacebookBundle).
You can extend it, and add to configuration:

```yaml
# app/config/config.yml

beelab_user:
    light_user_manager_class: AppBundle\Manager\LightUserManager
```

#### Forms

You can extends bundle forms, then add to configuration:

```yaml
# app/config/config.yml

beelab_user:
    password_form_type: AppBundle\Form\Type\PasswordFormType
    user_form_type:     AppBundle\Form\Type\UserFormType
```

#### Validation

Constraints are in `User` entity, so you can ovverride them in your entity.
See [Doctrine docs](http://docs.doctrine-project.org/en/latest/tutorials/override-field-association-mappings-in-subclasses.html).
Controllers use three validations groups: "create", "update", and "password".
First two groups are for creating and editing a user, the last one is for password change.
You can override which group is used in each form just by overriding actions in controllers (see above).

#### Routes

Routes are annotated in controllers, so you can override them just by overriding controllers (see above).

There is a route, with default name "admin", that is used for some redirects (user switching, password change, etc.).
It should point to your backend homepage, and you can customize it in configuration:

```yaml
# app/config/config.yml

# BeelabUser Configuration
beelab_user:
    route: my_backend_route
```

You need to separate authentication routes from administration route, maybe because you want all your administration
under an `/admin` path, you can import routes like so:

```yaml
# app/config/routing.yml

beelab_user_auth:
    resource: "@BeelabUserBundle/Controller/AuthController.php"
    type:     annotation
    prefix:   /

beelab_user_admin:
    resource: "@BeelabUserBundle/Controller/UserController.php"
    type:     annotation
    prefix:   /admin/
```

### 4. Commands

Two commands are available, to create a new user and to promote an existing user.
You can see them by typing:

```bash
$ php app/console list beelab
```

### 5. Events

For now, there is only one event: `beelab_user.change_password`.
This event is dispatched after user changes their password.
You can listen to this event, for example, to set a flash with a courtesy message.
