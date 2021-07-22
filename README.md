# Instrukcja instalacji oraz obsługi wtyczki „BluePayment” dla platformy Sylius

## Podstawowe informacje
BluePayment to moduł płatności umożliwiający realizację transakcji bezgotówkowych w sklepie opartym na platformie Sylius. Jeżeli jeszcze nie masz wtyczki, możesz ją pobrać [tutaj.](https://github.com/bluepayment-plugin/sylius-plugin/archive/refs/heads/master.zip)

### Główne funkcje
Do najważniejszych funkcji modułu zalicza się:
- realizację płatności online poprzez odpowiednie zbudowanie startu transakcji
- obsługę powiadomień o statusie transakcji (notyfikacje XML)
- obsługę zakupów bez rejestracji w serwisie
- obsługę dwóch trybów działania – testowego i produkcyjnego (dla każdego z nich wymagane są osobne dane kont, po które zwróć się do nas)
- przekierowanie na paywall/bramkę Blue media, gdzie są dostępne wszystkie formy płatności 

### Wymagania
●	PHP w wersji 7.3 lub nowszej
●	Sylius w wersji 1.7.4 lub nowszej

### Opis zmian

Wersja 1.0.0
•	Pierwsza wersja dokumentu


## Instalacja
1. Pobierz wtyczkę [tutaj.](https://github.com/bluepayment-plugin/sylius-plugin/archive/refs/heads/master.zip)
2. Wykonaj polecenie:
``` bash
composer require bluepayment-plugin/sylius-plugin
```
3. Zweryfikuj, czy w pliku bundles.php znajduje się wpis:
``` bash
Bluemedia\SyliusBluepaymentPlugin\BluemediaSyliusBluepaymentPlugin::class => ['all' => true],
```
4. W `config/routes.yaml` dodaj wpis:
``` bash
bluemedia_sylius_bluepayment_payment:
    resource: "@BluemediaSyliusBluepaymentPlugin/Resources/config/payment_routing.yml"
    prefix: /bluepayment
```
5. Wykonaj polecenia:
``` bash
mkdir -p templates/bundles/SyliusAdminBundle/

cp -R vendor/bluepayment-plugin/sylius-plugin/src/Resources/views/* templates/bundles/
```
6. Przejdź do zakładki Metody płatności
7. Dodaj nową metodę płatności klikając Utwórz i wybierz metodę Blue Media płatności online
8. Przejdź do konfiguracji modułu


## Konfiguracja
### Konfiguracja podstawowych pól wtyczki

1. Kod – nazwa własna wtyczki, np. Płatności Blue Media
2. Aktywna? – wybierz TAK lub NIE, żeby określić czy kanał płatności ma być widoczny przy składaniu zamówienia
3. Kanały – wybierz kanały, dla których powinna się pojawić płatność za pomocą bramki Blue Media
4. Użyj środowiska testowego – wybierając opcję TAK, sprawisz, że wszystkie płatności będą przekierowywane na testową bramkę płatniczą, która znajduje się pod adresem https://oplacasie-accept.bm.pl. Jeżeli tego nie zrobisz, automatycznie zostanie ustawiona produkcyjna wersja bramki płatniczej, a wszystkie płatności zostaną przekierowane na adres https://oplacasie.bm.pl.

    Jeżeli wybierzesz środowisko testowe, moduł nie będzie przetwarzał żadnych faktycznych płatności.

5. Identyfikator serwisu partnera – ma wartość liczbową i jest unikalny dla każdego sklepu (otrzymasz go od Blue Media).
6. Klucz współdzielony – unikalny klucz przypisany do danego sklepu (otrzymasz go od Blue Media).
7. Żeby wyświetlić waluty – zdefiniuj je w zakładce Konfiguracja ➝ Waluty 
8. Dla wybranych języków dodaj nazwę oraz opis płatności – zostaną one wyświetlone przy składaniu zamówienia.

### Konfiguracja adresów URL

Upewnij się, że w panelach administracyjnych Blue Media https://oplacasie.bm.pl oraz https://oplacasie-accept.bm.pl poniższe pola zawierają poprawne adresy sklepu:

●	adres powrotu do płatności
	https://domena-sklepu.pl/bluepayment/payment/back
●	adres, na który jest wysyłany ITN
	https://domena-sklepu.pl/bluepayment/process-itn 

