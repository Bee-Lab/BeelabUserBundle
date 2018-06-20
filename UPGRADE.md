Upgrading from v2.4.0 to v3.0.0
===============================

- If you customized controllers, check new controllers and adapt your ones.
  Controllers now extend Symfony's AbstractContoller instead of Controller and
  use `render` method instead of `Template` annotation.
  
- If you extended UserManager or LightUserManager, check new type hintings and
  return types. Also, customization configuration changed, and options to
  specify classes has been removed. You need to define alias and use autowiring
  now.   

Upgrading from v1.4.0 to v1.5.0
===============================

- Routes has been adjusted to be more meaningful, adding method constraints.
  So, templates received minor modification, mainly in form tags: if you overrode templates, you need to check such changes
  and possibly adapt your customizations.

- Route "user" has been moved, it was on "/user/" URL, now is on "/user" (without trailing slash).

Upgrading from v1.2.0 to v1.3.0
===============================

- From version 1.3.0, the dependency from KnpPaginatorBundle is optional. So, if you do not require that bundle, you
  can remove all references from your project. If you customized controller or template, please check the differences
  in User module.
