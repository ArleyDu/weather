<?php

namespace ArleyDu\Weather\Tests;

use ArleyDu\Weather\Exceptions\HttpException;
use ArleyDu\Weather\Exceptions\InvalidArgumentException;
use ArleyDu\Weather\Weather;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Mockery\Matcher\AnyArgs;
use PHPUnit\Framework\TestCase;

/**
 * Class WeatherTest
 *
 * @package \ArleyDu
 */
class WeatherTest extends TestCase
{
    /**
     * 检查 type 参数
     *
     * @throws \ArleyDu\Weather\Exceptions\HttpException
     * @throws \ArleyDu\Weather\Exceptions\InvalidArgumentException
     */
    public function testGetWeatherWithInvalidType ()
    {
        $w = new Weather( 'mock-key' );

        // 断言会抛出此异常类
        $this->expectException( InvalidArgumentException::class );

        $w->getWeather( '北京', 'foo' );

        // 断言异常消息为 'Invalid type value(base/all): foo'
        $this->expectExceptionMessage( 'Invalid type value(base/all): foo' );

        $this->fail( 'Failed to assert getWeather throw exception with invalid argument.' );
    }

    /**
     * 检查 format 参数
     *
     * @throws \ArleyDu\Weather\Exceptions\HttpException
     * @throws \ArleyDu\Weather\Exceptions\InvalidArgumentException
     */
    public function testGetWeatherWithInvalidFormat ()
    {
        $w = new Weather( 'mock-key' );

        // 断言会抛出此异常类
        $this->expectException( InvalidArgumentException::class );

        // 因为支持的格式为 json 和 xml，所以传入 array 会抛出异常
        $w->getWeather( '北京', 'base', 'array' );

        // 断言异常消息为：'Invalid format value(json/xml): array'
        $this->expectExceptionMessage( 'Invalid format value(json/xml): array' );

        // 如果没有抛出异常，就会运行到这行，标记当前测试没有成功
        $this->fail( 'Failed to assert getWeather throw exception with invalid argument.' );
    }

    public function testGetHttpClient ()
    {
        $w = new Weather( 'mock-key' );

        // 断言返回结果为 GuzzleHttp\ClientInterface 实例
        $this->assertInstanceOf( ClientInterface::class, $w->getHttpClient() );
    }

    public function testSetGuzzleOptions ()
    {
        $w = new Weather( 'mock-key' );

        // 设置参数前，timeout 为 null
        $this->assertNull( $w->getHttpClient()->getConfig( 'timeout' ) );

        // 设置参数
        $w->setGuzzleOptions( [ 'timeout' => 5000 ] );

        // 设置参数后，timeout 为 5000
        $this->assertSame( 5000, $w->getHttpClient()->getConfig( 'timeout' ) );
    }

    public function testGetWeather ()
    {
        // json
        $response = new Response( 200, [], '{"success": true}' );
        $client = \Mockery::mock( Client::class );
        $client->allows()->get( 'https://restapi.amap.com/v3/weather/weatherInfo', [
            'query' => [
                'key' => 'mock-key',
                'city' => '北京',
                'output' => 'json',
                'extensions' => 'base'
            ],
        ] )->andReturn( $response );

        $w = \Mockery::mock( Weather::class, [ 'mock-key' ] )->makePartial();
        $w->allows()->getHttpClient()->andReturn( $client );

        $this->assertSame( [ 'success' => true ], $w->getWeather( '北京' ) );

        // xml
        $response = new Response( 200, [], '<hello>content</hello>' );
        $client = \Mockery::mock( Client::class );
        $client->allows()->get( 'https://restapi.amap.com/v3/weather/weatherInfo', [
            'query' => [
                'key' => 'mock-key',
                'city' => '北京',
                'extensions' => 'all',
                'output' => 'xml'
            ],
        ] )->andReturn( $response );

        $w = \Mockery::mock( Weather::class, [ 'mock-key' ] )->makePartial();
        $w->allows()->getHttpClient()->andReturn( $client );

        $this->assertSame( '<hello>content</hello>', $w->getWeather( '北京', 'all', 'xml' ) );
    }

    public function testGetWeatherWithGuzzleRuntimeException ()
    {
        $client = \Mockery::mock( Client::class );
        $client->allows()
            ->get( new AnyArgs() ) // 由于上面的用例已经验证过参数传递，所以这里就不关心参数了。
            ->andThrow( new \Exception( 'request timeout' ) ); // 当调用 get 方法时会抛出异常。

        $w = \Mockery::mock( Weather::class, [ 'mock-key' ] )->makePartial();
        $w->allows()->getHttpClient()->andReturn( $client );

        // 接着需要断言调用时会产生异常。
        $this->expectException( HttpException::class );
        $this->expectExceptionMessage( 'request timeout' );

        $w->getWeather( '北京' );
    }
}