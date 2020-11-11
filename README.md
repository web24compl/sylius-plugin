## Sylius BlueMedia Payments Plugin 

Repozytorium zawiera kod oficjalnego rozszerzenia Blue Media S.A. umożliwiającego integrację ecommerce opartych o rozwiązanie Sylius.

## Spis treści
- [Wymagania](#wymagania)
- [Instalacja i konfiguracja](#instalacja)
- [Konfiguracja](#instalacja)

## Wymagania
- PHP w wersji 7.3 lub nowszej.
- Sylius w wersji 1.7.4 lub nowszej.

## Instalacja
Wykonaj polecenie:
``` bash
composer require bluepayment-plugin/sylius-plugin
```

W `config/routes.yaml` dodaj wpis:
``` bash
bluemedia_sylius_bluepayment_payment:
    resource: "@BluemediaSyliusBluepaymentPlugin/Resources/config/payment_routing.yml"
    prefix: /bluepayment
```

Wykonaj polecenia:
``` bash
mkdir -p templates/bundles/SyliusAdminBundle/

cp -R vendor/bluepayment-plugin/sylius-plugin/src/Resources/views/* templates/bundles/
```

## Konfiguracja
Aktualna instrukcja konfiguracji rozszerzenia znajduje się w katalogu `docs` repozytorium.
