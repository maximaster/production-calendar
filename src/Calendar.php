<?php

namespace Maximaster\ProductionCalendar;

use Exception;
use Maximaster\ProductionCalendar\RulesProvider\ProviderInterface;

class Calendar
{
    /**
     * @var Rules
     */
    protected $rules;

    /**
     * @var string
     */
    protected $innerFormat = 'd.m.Y';

    public function __construct(Rules $rules)
    {
        $this->rules = $rules;
    }

    /**
     * Возвращает, является ли день "свободным", т.е. либо праздником, либо рядовым выходным
     * @param string
     * @return bool
     */
    public function isFreeDay($day)
    {
        return $this->isDay($day, [Rules::HOLIDAY, Rules::REGULAR_REST], true);
    }

    /**
     * Проверяет, является ли день рядовым выходным
     * @param string $day
     * @return bool
     */
    public function isRegularRestDay($day)
    {
        return in_array((int)date('N', strtotime($day)), $this->rules->getWeekRestDays());
    }

    /**
     * Проверяет день на соответствие определённому типу по заданным правилам
     * @param string $day
     * @param array|int $types Тип или типы
     * @param bool $validateType Осуществлять ли проверку корректности данных в $types
     * @return bool
     * @throws Exception
     */
    public function isDay($day, $types, $validateType = true)
    {
        if (!is_array($types)) {
            $types = [$types];
        }

        if ($validateType &&
            count($types) !== count(array_filter(array_map(
                ['Maximaster\ProductionCalendar\Rules', 'isCorrectType'], $types)
            ))
        ) {
            throw new Exception("Incorrect day type(s): ".var_export($types, true));
        }

        return in_array($this->getDayType($day), $types);
    }

    /**
     * Возвращает тип дня согласно заданному набору правил
     * @param mixed $day
     * @return int
     */
    public function getDayType($day)
    {
        list($dayNum, $month, $year) = self::getNormalizedDay($day);
        $dayType = $this->rules->getDay($dayNum, $month, $year);
        if ($dayType === Rules::UNKNOWN) {
            $dayType = $this->isRegularRestDay($day) ? Rules::REGULAR_REST : Rules::REGULAR;
        }

        return $dayType;
    }

    public function getNormalizedDay($day)
    {
        return explode('.', date($this->innerFormat, strtotime($day)));
    }

    public static function fromProvider(ProviderInterface $provider)
    {
        return new self($provider->get());
    }
}
