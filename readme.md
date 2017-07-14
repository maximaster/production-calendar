# Production Calendar

Предоставляет возможность проверить является ли день выходным|праздничным|рабочим.
Данные предоставлены [basicdata.ru], однако, библиотека позволяет подключить любой источник данных.

# Установка

```bash
composer require maximaster/production-calendar
```

# Примеры использования

Для полного списка доступных функций обратитесь к phpDoc пубичных методов класса Calendar.

## isFree($day)
Проверяет, является ли день "свободным", т.е. либо праздником, либо рядовым выходным
```php
use Maximaster\ProductionCalendar\Calendar;
use Maximaster\ProductionCalendar\RulesProvider\BasicdataProvider;

$calendar = Calendar::fromProvider(new BasicdataProvider);
if ($calendar->isFreeDay('01.01.2017')) {
```

## isDay($day, $types)
Проверяет, относится ли день к определённому типу (или одному из типов, если передан массив). Доступные типы см. константы класса Rules
```php
use Maximaster\ProductionCalendar\Rules;
if ($calendar->isDay('01.01.2017', [Rules::HOLIDAY, Rules::PRE_HOLIDAY])) {
```

## getDayType($day)
Возвращает тип дня
```php
$calendar->getDayType('01.01.2017'); // Rules::REGULAR_REST
```

## getMonthWorkDaysCount($year, $month)
## getMonthWorkDaysCount($dayOfMonth)
Возвращает количество рабочих дней в указанном месяце
```php
$calendar->getMonthWorkDaysCount(2017, 6); // 21
```

# Кеширование
Позволяет кешировать результаты любого источника с помощью CacheProvider, в том числе встроенного. Пример:
```php
Calendar::fromProvider(new CacheProvider(new BasicdataProvider));
```
Для использования необходимо подключить пакет desarrolla2/cache

# Использование как сервиса в Symfony (2.8)
app/config/services.yml
```yml
  app.calendar.basicdata_provider:
    public: false
    class: Maximaster\ProductionCalendar\RulesProvider\BasicdataProvider

  app.calendar.cached_basicdata_provider:
    public: false
    class: Maximaster\ProductionCalendar\RulesProvider\CacheProvider
    arguments: ["@app.calendar.basicdata_provider"]

  app.calendar:
    class: Maximaster\ProductionCalendar\Calendar
    factory: ['Maximaster\ProductionCalendar\Calendar', fromProvider]
    arguments: ["@app.calendar.cached_basicdata_provider"]
```
любой код с доступом к контейнеру
```php
$calendar = $this->getContainer()->get('app.calendar');
```

[basicdata.ru]:http://basicdata.ru