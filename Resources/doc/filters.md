Filters
=======

You can filter the list of users.
In this guide, we'll use [LexikFormFilterBundle](https://github.com/lexik/LexikFormFilterBundle).
Of course, you can use whatever software you like.

First, define a form in the `filter_form_type` option:

```yaml
# app/config/config.yml

# BeelabUser Configuration
beelab_user:
    // ...
    filter_form_type: AppBundle\Form\UserFilterType
```

Form example:

```php
<?php

namespace AppBundle\Form;

use Lexik\Bundle\FormFilterBundle\Filter\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', Type\TextFilterType::class)
            ->add('active', Type\CheckboxFilterType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\User',
            'csrf_protection' => false,
            'validation_groups' => ['filter'],
            'method' => 'GET',
        ]);
    }
}
```

Then, listen to `beelab_user.filter` event.

Listener example:

```php
<?php

namespace AppBundle\Listener;

use Beelab\UserBundle\Event\FormEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class UserFilterListener
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onFilter(FormEvent $event)
    {
        $request = $event->getRequest();
        $form = $event->getForm();
        if ($request->query->has('submit-filter') && $form->handleRequest($request)->isValid()) {
            $request->getSession()->set('filter.utente', $request->query->get($form->getName()));
            $response = new RedirectResponse($this->router->generate('user'));
            $event->setResponse($response);
        } elseif ($request->query->has('reset-filter')) {
            $request->getSession()->set('filter.utente', null);
            $response = new RedirectResponse($this->router->generate('user'));
            $event->setResponse($response);
        }
    }
}

```

Then, listen to `beelab_user.filter_apply` event.

Listener example:

```php
<?php

namespace AppBundle\Listener;

use Beelab\UserBundle\Event\FormEvent;
use Beelab\UserBundle\Manager\UserManager;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdaterInterface;

class UserApplyFilterListener
{
    private $userManager;
    private $queryBuilderUpdater;

    public function __construct(UserManager $userManager, FilterBuilderUpdaterInterface $queryBuilderUpdater)
    {
        $this->userManager = $userManager;
        $this->queryBuilderUpdater = $queryBuilderUpdater;
    }

    public function onFilter(FormEvent $event)
    {
        $request = $event->getRequest();
        $form = $event->getForm();
        $queryBuilder = $this->userManager->getQueryBuilder();
        if (!is_null($values = $request->getSession()->get('filter.utente'))) {
            if ($form->submit($values)->isValid()) {
                $this->queryBuilderUpdater->addFilterConditions($form, $queryBuilder);
            }
        }
    }
}

```

That's it. Now you only need to override template `BeelabUserBundle/User/index.html.twig`,
adding the filter form.

[Go back to main documentations](index.md)
