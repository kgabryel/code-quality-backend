# kgabryel/code-quality

Współdzielone konfiguracje [PHPStan](https://phpstan.org), [PHP CS Fixer](https://cs.symfony.com),
[Rector](https://getrector.com), [PHP_CodeSniffer](https://github.com/PHPCSStandards/PHP_CodeSniffer)
i [Infection](https://infection.github.io).

---

## Spis treści

- [Główne założenia](#główne-założenia)
- [Instalacja i konfiguracja](#instalacja-i-konfiguracja)
    - [Komendy](#komendy)
    - [Przebieg konfiguracji (analysis)](#przebieg-konfiguracji-analysis)
    - [Przebieg konfiguracji (test)](#przebieg-konfiguracji-test)
    - [Instalowane zależności](#instalowane-zależności)
    - [Generowane pliki konfiguracyjne](#generowane-pliki-konfiguracyjne)
    - [Generowane skrypty pomocnicze (.sh)](#generowane-skrypty-pomocnicze-sh)
        - [Wariant lokalny (local)](#wariant-lokalny-local)
        - [Skrypty zbiorcze](#skrypty-zbiorcze)
        - [Wariant kontenerowy (docker)](#wariant-kontenerowy-docker)
- [PHPStan](#phpstan)
    - [Konfiguracja](#konfiguracja)
    - [Reguły](#reguły)
    - [Rozszerzenia](#rozszerzenia)
    - [Dostosowanie](#dostosowanie)
- [PHP CS Fixer](#php-cs-fixer)
    - [Konfiguracja](#konfiguracja-1)
    - [Preset](#preset)
    - [Reguły](#reguły-1)
    - [Dostosowanie](#dostosowanie-1)
- [Rector](#rector)
    - [Konfiguracja](#konfiguracja-2)
    - [Preset](#preset-1)
    - [Zestawy reguł](#zestawy-reguł)
    - [Dostosowanie](#dostosowanie-2)
- [PHP_CodeSniffer](#php_codesniffer)
    - [Konfiguracja](#konfiguracja-3)
    - [Reguły](#reguły-2)
    - [Dostosowanie](#dostosowanie-3)
- [PHP Parallel Lint](#php-parallel-lint)
- [PHPUnit](#phpunit)
- [Infection](#infection)
    - [Konfiguracja](#konfiguracja-4)
    - [Mutatory](#mutatory)
    - [Dostosowanie](#dostosowanie-4)
- [Narzędzia Composera](#narzędzia-composera)
- [Ustawienia Symfony](#ustawienia-symfony)
    - [Ścieżki](#ścieżki)
    - [Dodatkowe pakiety](#dodatkowe-pakiety)
    - [Rector](#rector-1)
    - [Infection](#infection-1)
- [Ustawienia Laravel](#ustawienia-laravel)
    - [Ścieżki](#ścieżki-1)
    - [Dodatkowe pakiety](#dodatkowe-pakiety-1)
    - [Rector](#rector-2)
    - [Infection](#infection-2)
- [Ustawienia Base](#ustawienia-base)
    - [Ścieżki](#ścieżki-2)
    - [Rector](#rector-3)
    - [Infection](#infection-3)

---

## Główne założenia

`kgabryel/code-quality` powstał z myślą o jednym standardzie jakości kodu w projektach PHP - zamiast rozproszonych
plików konfiguracyjnych, które trudno trzymać w synchronizacji, pakiet agreguje, unifikuje i automatyzuje działanie
kluczowych narzędzi: PHPStan, PHP CS Fixer, Rector, PHP_CodeSniffer, PHP Parallel Lint, Infection oraz zestawu
narzędzi Composera.

---

## Instalacja i konfiguracja

```bash
composer require --dev kgabryel/code-quality
php vendor/bin/code-quality analysis
php vendor/bin/code-quality test
```

Pakiet wymaga PHP `>= 8.4`. Skrypt prowadzi przez cały proces konfiguracji krok po kroku: od wyboru frameworka
i instalacji zależności, przez generowanie plików konfiguracyjnych, po opcjonalne tworzenie skryptów pomocniczych
(`.sh`).

### Komendy

| Komenda    | Opis                                                                                                 |
|------------|------------------------------------------------------------------------------------------------------|
| `analysis` | Instaluje i konfiguruje narzędzia do statycznej analizy i formatowania kodu oraz narzędzia Composera |
| `test`     | Instaluje i konfiguruje narzędzia testowe (PHPUnit, Infection)                                       |

### Przebieg konfiguracji (analysis)

1. Wersja PHP (`8.4`, `8.5` lub `8.6`) - determinuje zestaw reguł PHP w konfiguracji Rectora.

2. Wybór frameworka (`Laravel`, `Symfony` lub `Plain PHP / other framework`) - determinuje zestaw
   instalowanych zależności, analizowane katalogi oraz położenie plików cache.

3. Wybór narzędzi analitycznych - można dowolnie łączyć pozycje z listy: `PHP CS Fixer`,
   `PHP Code Sniffer + Slevomat`, `PHP Parallel Lint`, `PHPStan`, `Rector`. Domyślnie zaznaczone są wszystkie.

4. Wybór narzędzi Composera - `Composer Normalize`, `Composer Require Checker (missing deps)`,
   `Composer Unused (unused deps)`. Domyślnie zaznaczone są wszystkie.

5. Instalacja zależności - pytanie o automatyczne pobranie i instalację pakietów. Odpowiedź przecząca nie przerywa
   konfiguracji - zamiast instalacji CLI wypisze gotowe polecenie `composer require --dev` do samodzielnego
   uruchomienia.

6. Decyzja o generowaniu skryptów - pytanie o wygenerowanie pomocniczych skryptów powłoki (`.sh`). Odpowiedź
   przecząca pomija kroki 7 i 8.

7. Środowisko skryptów - wybór miejsca uruchamiania generowanych skryptów: `local` (bezpośrednio na maszynie lokalnej)
   lub `docker` (polecenia zostaną automatycznie opakowane w instrukcję `docker exec`).

8. Tworzenie skryptów pomocniczych - generowanie plików wywołujących polecenia zainstalowanych narzędzi. Podobnie jak
   przy plikach konfiguracyjnych, istniejący już skrypt o tej samej nazwie wymaga potwierdzenia nadpisania, a
   poszczególne pliki można pominąć.

### Przebieg konfiguracji (test)

1. Wybór frameworka - identycznie jak w komendzie `analysis`.

2. Wybór narzędzi testowych - `PhpUnit`, `Infection`. Domyślnie zaznaczone są oba.

3. Instalacja zależności - identycznie jak w komendzie `analysis`.

4. Skrypty pomocnicze - decyzja o generowaniu, środowisko, wybór skryptów (`PhpUnit`, `PHPUnit (coverage report)`,
   `Infection` - lista zależy od zaznaczonych narzędzi) oraz nazwa kontenera dla środowiska `Docker`.

Komenda `test` nie generuje konfiguracji PHPUnit - projekt zachowuje własny `phpunit.xml`. Jedynym generowanym plikiem
jest `infection.json5`.

### Instalowane zależności

Skrypt instaluje tylko zależności potrzebne dla wybranych narzędzi i frameworka. Wszystkie pakiety trafiają do
`require-dev` jednym poleceniem `composer require --dev` - lista jest deduplikowana i sortowana alfabetycznie.

| Narzędzie                   | Zależności                                                                                                                                                                      |
|-----------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| PHPStan (wspólne)           | `phpstan/phpstan` `phpstan/extension-installer` `phpstan/phpstan-deprecation-rules` `phpstan/phpstan-strict-rules` `tomasvotruba/cognitive-complexity` `shipmonk/phpstan-rules` |
| PHPStan (Symfony)           | `phpstan/phpstan-symfony` `phpstan/phpstan-doctrine`                                                                                                                            |
| PHPStan (Laravel)           | `larastan/larastan`                                                                                                                                                             |
| PHP CS Fixer                | `friendsofphp/php-cs-fixer`                                                                                                                                                     |
| PHP Code Sniffer + Slevomat | `squizlabs/php_codesniffer` `slevomat/coding-standard`                                                                                                                          |
| PHP Parallel Lint           | `php-parallel-lint/php-parallel-lint`                                                                                                                                           |
| Rector (wspólne)            | `rector/rector`                                                                                                                                                                 |
| Rector (Laravel)            | `driftingly/rector-laravel`                                                                                                                                                     |
| Composer Normalize          | `ergebnis/composer-normalize`                                                                                                                                                   |
| Composer Require Checker    | `maglnet/composer-require-checker`                                                                                                                                              |
| Composer Unused             | `icanhazstring/composer-unused`                                                                                                                                                 |
| PhpUnit                     | `phpunit/phpunit`                                                                                                                                                               |
| Infection                   | `infection/infection`                                                                                                                                                           |

Zestawy PHPStan i PHPUnit uzupełniają się nawzajem: jeśli w projekcie jest już `phpunit/phpunit`, do zestawu PHPStan
trafia dodatkowo `phpstan/phpstan-phpunit` - i odwrotnie, instalacja PHPUnit w projekcie z PHPStan dołączy ten pakiet
automatycznie.

### Generowane pliki konfiguracyjne

| Narzędzie       | Plik                     | Cache                 |
|-----------------|--------------------------|-----------------------|
| PHP CS Fixer    | `.php-cs-fixer.dist.php` | `.php-cs-fixer.cache` |
| PHP_CodeSniffer | `phpcs.xml.dist`         | `.phpcs-cache`        |
| PHPStan         | `phpstan.dist.neon`      | `.phpstan-cache/`     |
| Rector          | `rector.php`             | `.rector-cache/`      |
| Infection       | `infection.json5`        | `.infection/`         |

Pliki trafiają do katalogu głównego projektu. W projektach Symfony katalogi cache lądują w `var/` (z wyjątkiem
`.phpcs-cache`, który zawsze zostaje w katalogu głównym); w pozostałych - bezpośrednio w katalogu głównym.
`PHP Parallel Lint` i `PhpUnit` nie generują plików konfiguracyjnych.

Po wygenerowaniu plików CLI wypisuje ścieżki cache, które warto dodać do `.gitignore` - pomijając te objęte już
domyślnymi wykluczeniami frameworka (Symfony: `var/`; Laravel: `bootstrap/cache/`, `storage/framework/cache/`).

### Generowane skrypty pomocnicze (.sh)

W zależności od wybranych modułów oraz środowiska skrypt wygeneruje w katalogu bazowym pliki powłoki `.sh` do
automatycznego sprawdzania i naprawiania kodu.

#### Wariant lokalny (local)

Generuje czyste polecenia wywołujące narzędzia bezpośrednio z `vendor/bin`. Komenda `analysis`:

| Skrypt                        | Narzędzie                | Polecenie                                             |
|-------------------------------|--------------------------|-------------------------------------------------------|
| `cs-fixer.sh`                 | PHP CS Fixer             | `php vendor/bin/php-cs-fixer fix`                     |
| `phpcs.sh`                    | PHP_CodeSniffer          | `php vendor/bin/phpcs`                                |
| `phpcbf.sh`                   | PHP_CodeSniffer          | `php vendor/bin/phpcbf`                               |
| `phpstan.sh`                  | PHPStan                  | `php vendor/bin/phpstan analyse --memory-limit=512M`  |
| `rector.sh`                   | Rector                   | `php vendor/bin/rector process`                       |
| `parallel-lint.sh`            | PHP Parallel Lint        | `php vendor/bin/parallel-lint src tests`              |
| `composer-require-checker.sh` | Composer Require Checker | `php vendor/bin/composer-require-checker`             |
| `composer-unused.sh`          | Composer Unused          | `php vendor/bin/composer-unused`                      |
| `composer-normalize.sh`       | Composer Normalize       | `composer normalize`                                  |

Komenda `test`:

| Skrypt             | Narzędzie | Polecenie                                                                                                                 |
|--------------------|-----------|---------------------------------------------------------------------------------------------------------------------------|
| `phpunit.sh`       | PHPUnit   | `php -d memory_limit=512M vendor/bin/phpunit`                                                                             |
| `code-coverage.sh` | PHPUnit   | `XDEBUG_MODE=coverage php -d memory_limit=512M vendor/bin/phpunit --coverage-html code-coverage`                          |
| `infection.sh`     | Infection | `XDEBUG_MODE=coverage php -d memory_limit=1024M vendor/bin/infection --initial-tests-php-options="-d memory_limit=1024M"` |

#### Skrypty zbiorcze

Oprócz skryptów pojedynczych narzędzi dostępne są skrypty zbiorcze, uruchamiające kilka poleceń po kolei:

| Skrypt              | Pozycja na liście                | Składowe                                                                                 |
|---------------------|----------------------------------|------------------------------------------------------------------------------------------|
| `check.sh`          | `Check code`                     | PHP Parallel Lint → PHP_CodeSniffer (check) → PHPStan                                    |
| `reformat.sh`       | `Reformat code`                  | PHP CS Fixer → PHP_CodeSniffer (fix) → Rector                                            |
| `composer-deps.sh`  | `Composer - dependency analysis` | Composer Require Checker → Composer Unused → Composer Normalize                          |
| `composer-check.sh` | `composer check`                 | `composer validate --strict --no-check-publish` → `composer audit` → `composer outdated` |

Do skryptu zbiorczego trafiają tylko składowe odpowiadające wybranym narzędziom, a sama pozycja pojawia się na
liście, gdy zaznaczono przynajmniej jedno z nich. Wyjątkiem jest `composer check` - opiera się wyłącznie o wbudowane
polecenia Composera i jest dostępny zawsze. Każda składowa dostaje nagłówek oraz komunikat o wyniku, a niepowodzenie
jednego narzędzia nie przerywa kolejnych:

```shell
# Przykład zawartości pliku check.sh (Base, wszystkie narzędzia)
#!/usr/bin/env sh

echo "===== PHP Parallel Lint ====="
php vendor/bin/parallel-lint src tests && echo "✓ PHP Parallel Lint passed" || echo "✗ PHP Parallel Lint failed"

echo "===== PHP Code Sniffer (check) ====="
php vendor/bin/phpcs && echo "✓ PHP Code Sniffer (check) passed" || echo "✗ PHP Code Sniffer (check) failed"

echo "===== PHPStan ====="
php vendor/bin/phpstan analyse --memory-limit=512M && echo "✓ PHPStan passed" || echo "✗ PHPStan failed"
```

#### Wariant kontenerowy (docker)

Jeśli wybrano środowisko Docker i podano nazwę kontenera (np. `my-app-container`), zawartość każdego z powyższych
skryptów zostaje automatycznie opakowana w komendę proxy:

```shell
# Przykład zawartości pliku phpstan.sh w wariancie Docker
docker exec -i my-app-container sh -c 'php vendor/bin/phpstan analyse --memory-limit=512M'
```

---

## PHPStan

### Konfiguracja

Komenda `analysis` generuje plik `phpstan.dist.neon`, który dołącza preset z `vendor` i uzupełnia go o katalog cache
oraz analizowane ścieżki:

```neon
# phpstan.dist.neon (Symfony)
includes:
    - vendor/kgabryel/code-quality/resources/presets/phpstan/symfony.neon

parameters:
    tmpDir: var/.phpstan-cache/
    paths:
        - src
        - tests
```

Warianty `laravel` i `base` konfiguruje się tak samo - różnią się jedynie nazwą dołączanego presetu oraz ścieżkami.
Przyrostek `.dist` pozwala trzymać lokalne nadpisania w pliku `phpstan.neon`, który ma pierwszeństwo przed wersją
`.dist`.

### Reguły

| Parametr                        | Wartość | Opis                                                                                |
|---------------------------------|---------|-------------------------------------------------------------------------------------|
| `level`                         | `10`    | Najwyższy poziom analizy PHPStan                                                    |
| `treatPhpDocTypesAsCertain`     | `false` | Typy z PHPDoc nie są traktowane jako pewnik - analiza nadal je weryfikuje           |
| `checkBenevolentUnionTypes`     | `true`  | Ścisłe sprawdzanie "łagodnych" unii typów (np. `int\|string` z funkcji wbudowanych) |
| `cognitive_complexity.class`    | `50`    | Maksymalna złożoność kognitywna klasy                                               |
| `cognitive_complexity.function` | `8`     | Maksymalna złożoność kognitywna funkcji                                             |

### Rozszerzenia

Dzięki `phpstan/extension-installer` zainstalowane rozszerzenia rejestrują się automatycznie - bez ręcznych wpisów
`includes`:

| Rozszerzenie                                                                                        | Opis                                                          |
|-----------------------------------------------------------------------------------------------------|---------------------------------------------------------------|
| [`phpstan/phpstan-strict-rules`](https://github.com/phpstan/phpstan-strict-rules)                   | Dodatkowy zestaw restrykcyjnych reguł analizy                 |
| [`phpstan/phpstan-deprecation-rules`](https://github.com/phpstan/phpstan-deprecation-rules)         | Zgłasza użycia API oznaczonego jako `@deprecated`             |
| [`tomasvotruba/cognitive-complexity`](https://github.com/TomasVotruba/cognitive-complexity)         | Limity złożoności kognitywnej klas i funkcji                  |
| [`shipmonk/phpstan-rules`](https://github.com/shipmonk-rnd/phpstan-rules)                           | Dodatkowe ścisłe reguły ponad standardowy zestaw              |
| [`phpstan/phpstan-symfony`](https://github.com/phpstan/phpstan-symfony)                             | (Symfony) Rozumienie kontenera i API Symfony                  |
| [`phpstan/phpstan-doctrine`](https://github.com/phpstan/phpstan-doctrine)                           | (Symfony) Rozumienie encji i repozytoriów Doctrine            |
| [`larastan/larastan`](https://github.com/larastan/larastan)                                         | (Laravel) Rozumienie fasad, Eloquenta i kontenera Laravela    |
| [`phpstan/phpstan-phpunit`](https://github.com/phpstan/phpstan-phpunit)                             | (z PHPUnit) Reguły i typowanie dla testów PHPUnit             |

### Dostosowanie

**Dodawanie i nadpisywanie** - parametry podane po `includes` mają pierwszeństwo przed presetem:

```neon
includes:
    - vendor/kgabryel/code-quality/resources/presets/phpstan/symfony.neon

parameters:
    tmpDir: var/.phpstan-cache/
    level: 8                  # nadpisanie
    paths:
        - src
    ignoreErrors:
        - '#unknown class#'   # dodanie
```

**Nadpisania lokalne** - plik `phpstan.neon` (np. poza repozytorium) ma pierwszeństwo przed `phpstan.dist.neon`:

```neon
# phpstan.neon
includes:
    - phpstan.dist.neon

parameters:
    level: 8
```

---

## PHP CS Fixer

### Konfiguracja

Komenda `analysis` generuje plik `.php-cs-fixer.dist.php`. Konfiguracja opiera się na klasach presetów - plik
importuje klasę właściwą dla frameworka i rozpakowuje jej ustawienia do `PhpCsFixer\Config`:

```php
<?php
// .php-cs-fixer.dist.php (Symfony)

declare(strict_types=1);

use Kgabryel\CodeQuality\Preset\CsFixer\SymfonyConfig as Preset;

$finder = new PhpCsFixer\Finder()
    ->in([
        ...Preset::getPaths(__DIR__),               // katalogi analizowane dla frameworka
    ]);

return new PhpCsFixer\Config()
    ->setRiskyAllowed(Preset::isRiskyAllowed())     // zezwolenie na reguły risky
    ->setRules([
        ...Preset::getSets(),                       // kompletny zestaw reguł
    ])
    ->setFinder($finder)
    ->setCacheFile(__DIR__ . '/var/.php-cs-fixer.cache');
```

### Reguły

| Reguła                                   | Wartość                                     | Opis                                                                                                                                                                                       |
|------------------------------------------|---------------------------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `@PhpCsFixer`                            | `true`                                      | Preset - pełny, restrykcyjny zestaw reguł projektu PHP CS Fixer                                                                                                                            |
| `braces_position`                        | `next_line_unless_newline_at_signature_end` | Klamra otwierająca klas i funkcji w nowej linii (poza sygnaturami wieloliniowymi)                                                                                                          |
| `class_attributes_separation`            | custom                                      | Brak pustych linii między stałymi; pusta linia między właściwościami tylko gdy mają atrybut lub PHPDoc (`only_if_meta`)                                                                    |
| `concat_space`                           | `'one'`                                     | Spacje wokół operatora konkatenacji: `$a . $b`                                                                                                                                             |
| `declare_strict_types`                   | `true`                                      | Wymaga `declare(strict_types=1)` na początku każdego pliku                                                                                                                                 |
| `global_namespace_import`                | `import_classes`                            | Klasy z globalnej przestrzeni nazw importowane przez `use` zamiast prefiksu `\`                                                                                                            |
| `method_argument_space`                  | custom                                      | Wieloliniowe wywołania i sygnatury rozbijane po jednym argumencie na linię (`ensure_fully_multiline`), atrybuty parametrów w tej samej linii (`same_line`)                                  |
| `multiline_whitespace_before_semicolons` | `no_multi_line`                             | Średnik zawsze na tej samej linii co ostatni token (bez multiline whitespace)                                                                                                              |
| `ordered_attributes`                     | custom                                      | Atrybuty PHPUnit w ustalonej kolejności: `Small` → `Medium` → `Large` → `CoversClass` → `Test` → `TestDox` → `DataProvider` → `DataProviderExternal`                                       |
| `ordered_class_elements`                 | custom + `alpha`                            | Elementy klasy w określonej kolejności: stałe, właściwości, konstruktor, destruktor, metody magiczne, metody zwyczajne; metody PHPUnit dla testów; wewnątrz grup widoczności alfabetycznie |
| `php_unit_test_class_requires_covers`    | `false`                                     | Wyłącza wymóg atrybutu `#[CoversClass]` / adnotacji `@covers` dla klas testowych PHPUnit                                                                                                   |
| `phpdoc_align`                           | `'left'`                                    | Adnotacje PHPDoc wyrównane do lewej                                                                                                                                                        |
| `strict_comparison`                      | `true`                                      | Zamienia `==` i `!=` na `===` i `!==`                                                                                                                                                      |
| `strict_param`                           | `true`                                      | Wymusza tryb strict w funkcjach takich jak `in_array`                                                                                                                                      |
| `yoda_style`                             | `false`                                     | Naturalna kolejność porównań, bez stylu yoda                                                                                                                                               |

### Dostosowanie

**Dodawanie i nadpisywanie** - klucz podany po rozwinięciu ma pierwszeństwo nad presetem:

```php
->setRules([
    ...Preset::getSets(),
    'yoda_style' => true,   // nadpisanie
    'final_class' => true,  // nowa reguła
])
```

**Usuwanie:**

```php
$rules = Preset::getSets();
unset($rules['declare_strict_types']);

return new PhpCsFixer\Config()
    // ...
    ->setRules($rules)
```

**Ścieżki:**

```php
$finder = new PhpCsFixer\Finder()
    ->in([
        ...Preset::getPaths(__DIR__),
        __DIR__ . '/bin',            // dodatkowy katalog
    ])
    ->exclude('Legacy');             // wykluczenie
```

---

## Rector

### Konfiguracja

Komenda `analysis` generuje plik `rector.php`. Podobnie jak w PHP CS Fixer, konfiguracja opiera się na klasach
presetów - plik importuje klasę właściwą dla frameworka i rozpakowuje jej ustawienia do `RectorConfig`:

```php
<?php
// rector.php (Laravel)

declare(strict_types=1);

use Kgabryel\CodeQuality\Preset\Rector\LaravelConfig as Preset;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;

return RectorConfig::configure()
    ->withPaths([
        ...Preset::getPaths(__DIR__),      // katalogi analizowane dla frameworka
    ])
    ->withImportNames()                    // FQCN zamieniane na importy use
    ->withPhpSets(php84: true)             // reguły dla wybranej wersji PHP
    ->withSets([
        ...Preset::getSets(),              // zestawy reguł Rectora
    ])
    ->withComposerBased(
        ...Preset::getComposerBased(),     // zestawy aktywowane na podstawie composer.json
    )
    ->withAttributesSets(
        ...Preset::getAttributesSets(),    // migracja adnotacji do atrybutów
    )
    ->withCache(__DIR__ . '/.rector-cache/')
    ->withSkip([
        ...Preset::getsSkip(),             // reguły wyłączone z analizy
    ]);
```

Argument `withPhpSets()` odpowiada wersji PHP wybranej podczas konfiguracji (`php84`, `php85` lub `php86`). Warianty
`Symfony` i `Base` konfiguruje się tak samo - wystarczy zmienić importowaną klasę na `SymfonyConfig` lub `BaseConfig`.

### Preset

| Metoda                 | Opis                                                       |
|------------------------|------------------------------------------------------------|
| `getPaths($baseDir)`   | Zwraca listę analizowanych katalogów frameworka            |
| `getSets()`            | Zwraca zestawy reguł Rectora                               |
| `getComposerBased()`   | Zestawy aktywowane na podstawie pakietów w `composer.json` |
| `getAttributesSets()`  | Zestawy migracji adnotacji do atrybutów                    |
| `getsSkip()`           | Reguły wyłączone z analizy                                 |

### Zestawy reguł

| Zestaw                      | Opis                                               |
|-----------------------------|----------------------------------------------------|
| `SetList::CODE_QUALITY`     | Poprawki podnoszące jakość kodu                    |
| `SetList::DEAD_CODE`        | Usuwanie martwego kodu                             |
| `SetList::TYPE_DECLARATION` | Uzupełnianie deklaracji typów                      |
| `SetList::EARLY_RETURN`     | Zamiana zagnieżdżonych warunków na wczesne returny |
| `SetList::PRIVATIZATION`    | Zawężanie widoczności elementów klas               |
| `SetList::CODING_STYLE`     | Reguły stylistyczne                                |

**Wyłączone**

| Reguła                                           | Opis                                                                                    |
|--------------------------------------------------|-----------------------------------------------------------------------------------------|
| `ClassPropertyAssignToConstructorPromotionRector` | Automatyczna promocja właściwości do konstruktora wyłączona                             |
| `NewlineBetweenClassLikeStmtsRector`             | Automatyczne wstawianie pustych linii między elementami klasy (obsługuje PHP CS Fixer) |

### Dostosowanie

**Pomijanie** - dodatkowe reguły i katalogi dopisuje się po rozwinięciu presetu:

```php
->withSkip([
    ...Preset::getsSkip(),
    ReadonlyPropertyRector::class,  // dodatkowa reguła do pominięcia
    __DIR__ . '/src/Legacy',        // katalog wyłączony z analizy
])
```

**Dodawanie zestawów:**

```php
->withSets([
    ...Preset::getSets(),
    SetList::NAMING,                // nowy zestaw
])
```

---

## PHP_CodeSniffer

### Konfiguracja

Komenda `analysis` generuje plik `phpcs.xml.dist`, który dołącza preset [Slevomat Coding
Standard](https://github.com/slevomat/coding-standard) z `vendor` i uzupełnia go o sprawdzane katalogi oraz plik
cache. `phpcs` sprawdza kod, a `phpcbf` automatycznie poprawia wykryte naruszenia.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!-- phpcs.xml.dist (Base) -->

<ruleset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/squizlabs/php_codesniffer/phpcs.xsd">

    <arg name="basepath" value="."/>
    <arg name="cache" value=".phpcs-cache"/>
    <arg name="colors"/>
    <arg name="extensions" value="php"/>

    <rule ref="vendor/kgabryel/code-quality/resources/presets/phpcs/base.xml"/>

    <file>src/</file>
    <file>tests/</file>
</ruleset>
```

Warianty `symfony` i `laravel` konfiguruje się tak samo - różnią się nazwą dołączanego presetu, sprawdzanymi
katalogami oraz wykluczeniami (Laravel pomija `bootstrap/cache/*`).

### Reguły

| Reguła                                                            | Opis                                                            |
|-------------------------------------------------------------------|-----------------------------------------------------------------|
| `SlevomatCodingStandard.Arrays.AlphabeticallySortedByKeys`        | Klucze tablic posortowane alfabetycznie                         |
| `SlevomatCodingStandard.Arrays.MultiLineArrayEndBracketPlacement` | Nawias zamykający wieloliniową tablicę w osobnej linii          |
| `SlevomatCodingStandard.Classes.RequireMultiLineMethodSignature`  | Sygnatury metod dłuższe niż 125 znaków rozbijane na wiele linii |
| `SlevomatCodingStandard.ControlStructures.EarlyExit`              | Wymusza wczesne wyjścia zamiast zagnieżdżonych warunków         |
| `SlevomatCodingStandard.Functions.ArrowFunctionDeclaration`       | Spójne formatowanie deklaracji arrow function                   |
| `SlevomatCodingStandard.Functions.RequireMultiLineCall`           | Wywołania dłuższe niż 125 znaków rozbijane na wiele linii       |

### Dostosowanie

**Dodawanie** - kolejne reguły i wykluczenia dopisuje się obok presetu:

```xml
<rule ref="vendor/kgabryel/code-quality/resources/presets/phpcs/base.xml"/>
<rule ref="SlevomatCodingStandard.TypeHints.DeclareStrictTypes"/>

<file>src/</file>
<exclude-pattern>src/Legacy/*</exclude-pattern>
```

**Usuwanie** - wykluczenie reguły wewnątrz `<rule ref>`:

```xml
<rule ref="vendor/kgabryel/code-quality/resources/presets/phpcs/base.xml">
    <exclude name="SlevomatCodingStandard.Arrays.AlphabeticallySortedByKeys"/>
</rule>
```

---

## PHP Parallel Lint

Nie generuje pliku konfiguracyjnego - narzędzie działa wyłącznie przez skrypt `parallel-lint.sh`, który równolegle
sprawdza poprawność składni wszystkich plików PHP. Zestaw katalogów zależy od frameworka - pełna lista jest w
sekcjach [Ustawienia Symfony](#ścieżki), [Ustawienia Laravel](#ścieżki-1) i [Ustawienia Base](#ścieżki-2).

---

## PHPUnit

Komenda `test` instaluje PHPUnit, ale nie generuje jego konfiguracji - projekt zachowuje własny `phpunit.xml`. Pakiet
dostarcza natomiast skrypty `phpunit.sh` i `code-coverage.sh` oraz - jeśli w projekcie jest PHPStan - dołącza
automatycznie `phpstan/phpstan-phpunit`.

Raport pokrycia w formacie HTML trafia do katalogu `code-coverage` (Symfony: `var/code-coverage`). Skrypty pokrycia
wymagają sterownika PCOV lub Xdebug - jego brak jest sygnalizowany ostrzeżeniem na końcu konfiguracji.

---

## Infection

### Konfiguracja

Komenda `test` generuje plik `infection.json5` z kompletną konfiguracją testów mutacyjnych - ścieżkami, wykluczeniami
i zestawem mutatorów dobranym pod framework:

```json5
// infection.json5 (Symfony)
{
  "$schema": "vendor/infection/infection/resources/schema.json",
  "tmpDir": "var/.infection/",
  "source": {
    "directories": [
      "src"
    ],
    "excludes": [
      "Kernel.php"
    ]
  },
  "logs": {
    "text": "var/.infection/infection.txt",
    "html": "var/.infection/infection.html"
  },
  "mutators": {
    "@default": true,
    "CastInt": false,
    "CastString": false,
    "MethodCallRemoval": {
      "ignore": [
        "App\\Entity\\*::*",
        "*::__construct"
      ]
    }
  }
}
```

### Mutatory

| Mutator                                   | Wartość  | Opis                                                                                    |
|-------------------------------------------|----------|-----------------------------------------------------------------------------------------|
| `@default`                                | `true`   | Domyślny zestaw mutatorów Infection                                                     |
| `CastInt`, `CastString`                   | `false`  | Mutacje rzutowań `(int)` i `(string)` wyłączone                                         |
| `MethodCallRemoval`                       | `ignore` | Nie usuwa wywołań konstruktorów; w Symfony pomija też metody encji, w Laravelu - modeli |

### Dostosowanie

Konfiguracja to zwykły plik `infection.json5` - zmiany wprowadza się bezpośrednio w nim:

```json5
"mutators": {
  "@default": true,
  "@number": true,      // włączenie mutacji liczbowych
  "TrueValue": false    // wyłączenie kolejnego mutatora
}
```

---

## Narzędzia Composera

Narzędzia Composera nie generują plików konfiguracyjnych - działają wyłącznie przez skrypty pomocnicze:

| Narzędzie                                                                               | Skrypt                        | Opis                                                                        |
|-----------------------------------------------------------------------------------------|-------------------------------|-----------------------------------------------------------------------------|
| [`ergebnis/composer-normalize`](https://github.com/ergebnis/composer-normalize)         | `composer-normalize.sh`       | Normalizuje `composer.json` - spójna kolejność i format wpisów              |
| [`maglnet/composer-require-checker`](https://github.com/maglnet/ComposerRequireChecker) | `composer-require-checker.sh` | Wykrywa użycia symboli spoza zadeklarowanych zależności (brakujące pakiety) |
| [`icanhazstring/composer-unused`](https://github.com/composer-unused/composer-unused)   | `composer-unused.sh`          | Wykrywa zadeklarowane, ale nieużywane zależności                            |

Niezależnie od wybranych narzędzi dostępny jest też skrypt `composer-check.sh`, oparty wyłącznie o wbudowane
polecenia Composera:

| Polecenie                                       | Opis                                                          |
|-------------------------------------------------|---------------------------------------------------------------|
| `composer validate --strict --no-check-publish` | Walidacja poprawności `composer.json`                         |
| `composer audit`                                | Sprawdzenie znanych podatności w zależnościach                |
| `composer outdated`                             | Lista pakietów z dostępnymi aktualizacjami                    |

---

## Ustawienia Symfony

Wariant wybierany automatycznie, gdy w projekcie jest `symfony/framework-bundle`. Katalogi cache wszystkich narzędzi
trafiają do `var/` (z wyjątkiem `.phpcs-cache`, który zawsze zostaje w katalogu głównym), a podpowiedź `.gitignore`
pomija ścieżki zaczynające się od `var/` - katalog jest już ignorowany w standardowym projekcie Symfony.

### Ścieżki

| Narzędzie         | Katalogi                                                 |
|-------------------|----------------------------------------------------------|
| PHP CS Fixer      | `bin` `config` `public` `src` `tests`                    |
| PHP_CodeSniffer   | `bin` `config` `public` `src` `tests`                    |
| PHP Parallel Lint | `bin` `config` `public` `src` `tests`                    |
| PHPStan           | `src` `tests`                                            |
| Rector            | `config` `public` `src` `tests`                          |
| Infection         | `src` (z wyłączeniem `Kernel.php`)                       |

### Dodatkowe pakiety

| Narzędzie | Pakiety                                              |
|-----------|------------------------------------------------------|
| PHPStan   | `phpstan/phpstan-symfony` `phpstan/phpstan-doctrine` |

### Rector

| Ustawienie           | Wartości                                 |
|----------------------|------------------------------------------|
| Dodatkowe zestawy    | `DoctrineSetList::DOCTRINE_CODE_QUALITY` |
| `withComposerBased`  | `phpunit` `doctrine` `symfony` `twig`    |
| `withAttributesSets` | `phpunit` `doctrine` `symfony`           |

### Infection

| Ustawienie                                 | Wartości                           |
|--------------------------------------------|------------------------------------|
| Analizowane katalogi                       | `src`                              |
| Wykluczenia                                | `Kernel.php`                       |
| Ignorowane wywołania (`MethodCallRemoval`) | `App\Entity\*::*` `*::__construct` |

---

## Ustawienia Laravel

Wariant wybierany automatycznie, gdy w projekcie jest `laravel/framework`. Katalogi cache trafiają do katalogu
głównego projektu, a podpowiedź `.gitignore` pomija ścieżki zaczynające się od `bootstrap/cache/`
i `storage/framework/cache/`.

### Ścieżki

| Narzędzie         | Katalogi                                                                                   |
|-------------------|--------------------------------------------------------------------------------------------|
| PHP CS Fixer      | `app` `config` `database` `routes` `tests`                                                 |
| PHP_CodeSniffer   | `app` `bootstrap` `config` `database` `routes` `tests` (z wyłączeniem `bootstrap/cache/*`) |
| PHP Parallel Lint | `app` `bootstrap` `config` `database` `routes` `tests` (z wyłączeniem `bootstrap/cache`)   |
| PHPStan           | `app` `tests`                                                                              |
| Rector            | `app` `bootstrap/app.php` `config` `database` `routes` `tests`                             |
| Infection         | `app`                                                                                      |

### Dodatkowe pakiety

| Narzędzie | Pakiety                     |
|-----------|-----------------------------|
| PHPStan   | `larastan/larastan`         |
| Rector    | `driftingly/rector-laravel` |

### Rector

| Ustawienie           | Wartości                                                                    |
|----------------------|-----------------------------------------------------------------------------|
| Dodatkowe zestawy    | `LaravelSetList::LARAVEL_CODE_QUALITY` `LaravelSetList::LARAVEL_COLLECTION` |
| `withComposerBased`  | `phpunit` `laravel`                                                         |
| `withAttributesSets` | `phpunit`                                                                   |

### Infection

| Ustawienie                                 | Wartości                           |
|--------------------------------------------|------------------------------------|
| Analizowane katalogi                       | `app`                              |
| Ignorowane wywołania (`MethodCallRemoval`) | `App\Models\*::*` `*::__construct` |

---

## Ustawienia Base

Wariant dla opcji `Plain PHP / other framework` - wybierany, gdy nie wykryto ani Symfony, ani Laravela. Katalogi
cache trafiają do katalogu głównego projektu, a podpowiedź `.gitignore` obejmuje wszystkie wygenerowane ścieżki
cache - wariant nie zakłada żadnych domyślnych wykluczeń.

### Ścieżki

| Narzędzie         | Katalogi      |
|-------------------|---------------|
| PHP CS Fixer      | `src` `tests` |
| PHP_CodeSniffer   | `src` `tests` |
| PHP Parallel Lint | `src` `tests` |
| PHPStan           | `src` `tests` |
| Rector            | `src` `tests` |
| Infection         | `src`         |

### Rector

Wariant nie dodaje własnych zestawów reguł.

| Ustawienie           | Wartości  |
|----------------------|-----------|
| `withComposerBased`  | `phpunit` |
| `withAttributesSets` | `phpunit` |

### Infection

| Ustawienie                                 | Wartości         |
|--------------------------------------------|------------------|
| Analizowane katalogi                       | `src`            |
| Ignorowane wywołania (`MethodCallRemoval`) | `*::__construct` |