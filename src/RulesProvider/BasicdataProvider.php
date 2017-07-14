<?php

namespace Maximaster\ProductionCalendar\RulesProvider;

use Exception;
use Maximaster\ProductionCalendar\Rules;

/**
 * Class Basicdata
 * Получает данные от сервиса basicdata.ru
 * @package Maximaster\ProductionCalendar\DataProvider
 */
class BasicdataProvider implements ProviderInterface
{
    const URL = 'http://basicdata.ru/api/json/calend/';

    /**
     * Соответствие значений сервиса, внутренним значениям класса Rules
     */
    protected $rulesMapping = [
        0 => Rules::REGULAR,
        2 => Rules::HOLIDAY,
        3 => Rules::PRE_HOLIDAY,
    ];

    public static $defaultCurlOpts = [
        CURLOPT_TIMEOUT => 1,
        CURLOPT_CONNECTTIMEOUT => 1,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FAILONERROR => true,
    ];

    protected $curlOpts = [];

    protected $apiData = [];

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        $rules = new Rules;

        $data = $this->fetchData();
        foreach ($data as $year => $yearMonths) {
            foreach ($yearMonths as $month => $days) {
                foreach ($days as $dayNum => $day) {
                    $rules->addDay($year, $month, $dayNum, $this->rulesMapping[ $day['isWorking'] ]);
                }
            }
        }

        $rules->setWeekRestDays([6, 7]);

        return $rules;
    }

    /**
     * Полностью задает массив curl-опций
     * @param array $curlOpts Массив CURLOPT_* констант и их значения для конфигурации запроса
     */
    public function setCurlOpts($curlOpts)
    {
        $this->curlOpts = $curlOpts;
    }

    /**
     * Перезаписывает ряд curl-опций
     * @param array $curlOpts Массив CURLOPT_* констант и их значения для конфигурации запроса
     */
    public function addCurlOpts($curlOpts)
    {
        $this->curlOpts = array_merge($this->curlOpts, $curlOpts);
    }

    /**
     * Получает данные от сервиса
     * @return array
     * @throws Exception
     */
    protected function fetchData()
    {
        $ch = curl_init(self::URL);
        curl_setopt_array($ch, $this->curlOpts);

        $rawResponse = $response = curl_exec($ch);
        if ($response === false) {
            $errMsg = curl_error($ch);
            curl_close($ch);
            throw new Exception("curl fails: {$errMsg}");
        }

        $response = json_decode($response, true);
        if (!is_array($response) || !isset($response['data'])) {
            curl_close($ch);
            throw new Exception("wrong response: `{$rawResponse}`");
        }

        curl_close($ch);

        return $response['data'];
    }

    /**
     * {@inheritdoc}
     */
    public function __construct(ProviderInterface $parentProvider = null)
    {
        $this->curlOpts = self::$defaultCurlOpts;
    }
}
