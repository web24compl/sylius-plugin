@TODO przy instrukcji:

1. Wykonać komendę `composer install`
2. Wykonać komendę:
`cp -R src/Resources/views/SyliusAdminBundle/* tests/Application/templates/bundles/SyliusAdminBundle/`
3. Dodać do config/routes.yml wpis:
```
bluemedia_sylius_bluepayment_plugin:
    resource: "@BluemediaSyliusBluepaymentPlugin/Resources/config/routing.yml"
    prefix: /bluepayment
```
