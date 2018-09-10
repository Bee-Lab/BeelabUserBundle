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

Bundle should be enabled by Flex.

If you want pagination in users' administration, install also [KnpPaginatorBundle](https://github.com/KnpLabs/KnpPaginatorBundle).

### 2. Configuration

Create a `User` entity class.
Example:

```php
<?php
// src/Entity
namespace App\Entity;

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

Insert in your configuration:

```yaml
# config/packages/beelab_user_bundle.yaml

beelab_user:
    user_class: App\Entity\User
```

Add routes:

```yaml
# config/routes.yaml

beelab_user:
    resource: "@BeelabUserBundle/Controller/"
    type: annotation
```

Enable security:

```yaml
# config/package/security.yaml

security:
    encoders:
        App\Entity\User:
            # See http://symfony.com/doc/current/reference/configuration/security.html#using-the-bcrypt-password-encoder
            algorithm: bcrypt
            # Also, since bcrypt is a bit expensive, you likely want to override it in test env

    providers:
        administrators:
            entity: { class: App:User }

    firewalls:
        main:
            pattern: ^/
            form_login:
                use_referer: true
            logout: true
            anonymous: true
            switch_user: true
```

This is just an example, your real security configuration may vary.
It's important here to use the name of your entity in `encoders` and `providers`.

> ⚠️️ **Warning**: if you customize the route prefix, don't forget to explicit `login_path` and other options in your
> firewall. See [security reference](http://symfony.com/doc/current/reference/configuration/security.html#the-login-form-and-process) for more details.
>

### 3. Customizations

#### Templates

You can customize templates as explained in
[official documentation](http://symfony.com/doc/current/templating.html#overriding-bundle-templates).

All template use a default layout. If you want to use your custom layout, you can
specify it in configuration:
```yaml
# config/packages/beelab_user_bundle.yaml
beelab_user:
    layout: "::myCustomLayout.html.twig"
```

#### Controllers

You can customize controllers by extending bundle, like explained in
[official documentation](http://symfony.com/doc/current/bundles/inheritance.html#overriding-controllers).

#### UserManager

You can create you own UserManager, implementing interface provided by bundle.
Then, add an alias to your service configuration:

```yaml
# config/services.yaml

Beelab\UserBundle\Manager\UserManagerInterface: '@App\Manager\UserManager' 
```

If you need a lighter UserManager, you can use `LightUserManager`, that has less
dependencies than UserManager. For example, you can use it for Facebook integration with
[FOSFacebookBundle](https://github.com/FriendsOfSymfony/FOSFacebookBundle).
You can implement interface, and add to configuration:

```yaml
# config/services.yaml

Beelab\UserBundle\Manager\LightUserManagerInterface: '@App\Manager\LightUserManager'
```

#### Forms

You can extends bundle forms, then add to configuration:

```yaml
# config/packages/beelab_user_bundle.yaml

beelab_user:
    password_form_type: App\Form\Type\PasswordFormType
    user_form_type: App\Form\Type\UserFormType
```

#### Validation

Constraints are in `User` entity, so you can override them in your entity.
See [Doctrine docs](https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/tutorials/override-field-association-mappings-in-subclasses.html).
Controllers use three validations groups: "create", "update", and "password".
First two groups are for creating and editing a user, the last one is for password change.
You can override which group is used in each form just by overriding actions in controllers (see above).

#### Routes

Routes are annotated in controllers, so you can override them just by overriding controllers (see above).

There is a route, with default name "admin", that is used for some redirects (user switching, password change, etc.).
It should point to your backend homepage, and you can customize it in configuration:

```yaml
# config/packages/beelab_user_bundle.yaml

beelab_user:
    route: my_backend_route
```

You need to separate authentication routes from administration route, maybe because you want all your administration
under an `/admin` path, you can import routes like so:

```yaml
# config/routes.yaml

beelab_user_auth:
    resource: "@BeelabUserBundle/Controller/AuthController.php"
    type: annotation
    prefix: /

beelab_user_admin:
    resource: "@BeelabUserBundle/Controller/UserController.php"
    type: annotation
    prefix: /admin/
```

#### Filters

This bundle is ready to filter users in the list, but filter implementation is up to the
developer.
To implement filters, see [filters.md](filters.md).

### 4. Commands

Two commands are available, to create a new user and to promote an existing user.
You can see them by typing:

```bash
$ php bin/console list beelab
```

### 5. Events

For now, there is only one event: `beelab_user.change_password`.
This event is dispatched after user changes their password.
You can listen to this event, for example, to set a flash with a courtesy message.
