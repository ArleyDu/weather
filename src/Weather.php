<?php

namespace ArleyDu\Weather;

use ArleyDu\Weather\Exceptions\HttpException;
use ArleyDu\Weather\Exceptions\InvalidArgumentException;
use GuzzleHttp\Client;

/**
 * Class Weather
 *
 * @package \\${NAMESPACE}
 */
class Weather
{
    // 高德地图的天气接口的 key
    protected $key;
    // 用户自定义 guzzle 实例的参数
    protected $guzzleOptions = [];

    /**
     * Weather constructor.
     *
     * @param $key
     */
    public function __construct ( $key )
    {
        $this->key = $key;
    }

    /**
     * 返回用于 http 请求的 guzzle 实例
     *
     * @return \GuzzleHttp\Client
     */
    public function getHttpClient ()
    {
        return new Client( $this->guzzleOptions );
    }

    /**
     * 设置用户自定义 guzzle 实例的参数
     *
     * @param array $options
     */
    public function setGuzzleOptions ( array $options )
    {
        $this->guzzleOptions = $options;
    }

    /**
     * 获取天气信息
     *
     * @param        $city
     * @param string $type   base：实况天气，all：预报天气
     * @param string $format json/xml
     *
     * @return mixed|string
     * @throws \ArleyDu\Weather\Exceptions\HttpException
     * @throws \ArleyDu\Weather\Exceptions\InvalidArgumentException
     */
    public function getWeather ( $city, $type = 'base', $format = 'json' )
    {
        $url = 'https://restapi.amap.com/v3/weather/weatherInfo';

        // 1、对 `formar` 和 `type` 参数进行检查，不在范围内的抛出异常
        if ( !in_array( strtolower( $format ), [ 'json', 'xml' ] ) ) {
            throw new InvalidArgumentException( 'Invalid response format(json/xml)' . $format );
        }

        if ( !in_array( strtolower( $type ), [ 'base', 'all' ] ) ) {
            throw new InvalidArgumentException( 'Invalid type value(base/all)' . $type );
        }

        // 2、封装 query 参数，并使用 array_filter 对空值进行过滤
        $query = array_filter( [
            'key' => $this->key,
            'city' => $city,
            'extensions' => $type,
            'output' => $format
        ] );

        try {
            // 3、调用 getHttpClient 获取实例，并调用该实例的 `get` 方法
            //传递参数为两个：$url，['query'=>$query]
            $response = $this->getHttpClient()->get( $url, [
                'query' => $query
            ] )->getBody()->getContents();

            // 4、返回值根据 `$format` 返回不同格式
            // 当 `$format` 为 json 时，返回数组格式，否则为 xml
            return 'json' === $format ? json_decode( $response, true ) : $response;
        } catch ( \Exception $exception ) {
            // 5、当调用出现异常时捕获并抛出，消息为捕获到的异常消息
            // 并将调用异常作为 $exception 传入
            throw new HttpException( $exception->getMessage(), $exception->getCode(), $exception );
        }
    }
}