<?php

namespace Maximaster\ProductionCalendar;

use Exception;

class Rules
{
    /**
     * Неизвестный тип дня, т.е. для него не прописано спец. правила
     */
    const UNKNOWN = 0;

    /**
     * Обычный рабочий день
     */
    const REGULAR = 1;

    /**
     * Обычный выходной день
     */
    const REGULAR_REST = 2;

    /**
     * Праздник
     */
    const HOLIDAY = 3;

    /**
     * Предпраздничный день
     */
    const PRE_HOLIDAY = 4;

    /**
     * Список всех типов
     */
    public static $TYPES = [self::UNKNOWN, self::REGULAR, self::REGULAR_REST, self::HOLIDAY, self::PRE_HOLIDAY];

    /**
     * @var array Список дней и их типов. Формат [год][месяц][день] = тип
     */
    protected $days = [];

    /**
     * @var array Номера (ISO-8601: пн=1, вс=7) дней которые считаются рядовыми выходными
     */
    protected $weekRestDays = [];

    /**
     * Проверяет корректность типа дня
     * @param int $type
     * @return bool
     */
    public static function isCorrectType($type)
    {
        return in_array($type, self::$TYPES);
    }

    /**
     * Задает тип для определённого дня
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $type
     * @return $this
     * @throws Exception
     */
    public function addDay($year, $month, $day, $type)
    {
        if (!self::isCorrectType($type)) {
            throw new Exception("Wrong day type: {$type}");
        }

        $this
            ->normalizeYear($year)
            ->normalizeMonth($month)
            ->normalizeDay($day);


        if (!isset($this->days[$year])) {
            $this->days[$year] = [];
        }

        if (!isset($this->days[$year][$month])) {
            $this->days[$year][$month] = [];
        }

        $this->days[$year][$month][$day] = $type;
        return $this;
    }

    /**
     * Устанавливает номера дней которые считаются рядовыми выходными
     * @param array $weekRestDays
     * @return $this
     */
    public function setWeekRestDays($weekRestDays)
    {
        $this->weekRestDays = $weekRestDays;
        return $this;
    }

    /**
     * @return array
     */
    public function getWeekRestDays()
    {
        return $this->weekRestDays;
    }

    /**
     * Приводит год в принятый в приложении вид
     * @param int $year
     * @return $this
     * @throws Exception
     */
    public function normalizeYear(&$year)
    {
        if (!is_numeric($year)) {
            throw $this->normalizeException(compact('year'));
        }

        switch ($len = strlen($year)) {
            case 4:
                return $this;

            case 1:
                static $curYearPart;
                if ($curYearPart === null) {
                    $curYearPart = substr(date('Y'), 0, 2);
                }
                $year = "{$curYearPart}{$year}";
                return $this;
        }

        throw $this->normalizeException(compact('year'));
    }

    /**
     * @param array $data
     * @return Exception
     */
    public function normalizeException($data)
    {
        return new Exception('Can\'t normalize this '.key($data).' input: `'.reset($data).'`');
    }

    private function normalizeMonth(&$month)
    {
        if (!is_numeric($month)) {
            throw $this->normalizeException(compact('month'));
        }

        switch ($len = strlen($month)) {
            case 2:
                return $this;

            case 1:
                $month = "0{$month}";
                return $this;
        }

        throw $this->normalizeException(compact('month'));
    }

    private function normalizeDay(&$day)
    {
        try {
            $this->normalizeMonth($day);
        } catch (Exception $e) {
            throw $this->normalizeException(compact('day'));
        }
    }

    /**
     * Получает тип дня
     * @param int $day
     * @param int $month
     * @param int $year
     * @return int Тип дня
     * @throws Exception
     */
    public function getDay($day, $month = null, $year = null)
    {
        if ($year === null) {
            list($day, $month, $year) = explode('/', date('d/m/Y', strtotime($day)));
        }

        $this
            ->normalizeYear($year)
            ->normalizeMonth($month)
            ->normalizeDay($day);

        if (isset($this->days[$year][$month][$day])) {
            return $this->days[$year][$month][$day];
        }

        return self::UNKNOWN;
    }
}
