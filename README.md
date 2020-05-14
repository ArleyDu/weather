# weather

基于 [高德开放平台](https://lbs.amap.com/dev/id/newuser) 的 PHP 天气信息组件

## 安装

```shell
composer require arleydu/weather -vvv
```

## 配置

在使用本扩展之前，你需要去 [高德开放平台](https://lbs.amap.com/dev/id/newuser) 注册账号，然后创建应用，获取应用的 API Key。

## 使用

```php
use ArleyDu\Weather\Weather;

$key = 'xxxxxxxxxxxxxxxxxxxxxxxxxxx';

$weather = new Weather($key);
```

## 获取实时天气

```php
$response = $weather->getWeather('北京');
```

### 示例

```json
{
    "status": "1",
    "count": "1",
    "info": "OK",
    "infocode": "10000",
    "lives": [
        {
            "province": "北京",
            "city": "北京市",
            "adcode": "110000",
            "weather": "晴",
            "temperature": "28",
            "winddirection": "东南",
            "windpower": "≤3",
            "humidity": "35",
            "reporttime": "2020-05-14 17:28:35"
        }
    ]
}
```

## 获取近期天气预报

示例

```json
{
    "status": "1",
    "count": "1",
    "info": "OK",
    "infocode": "10000",
    "forecasts": [
        {
            "city": "北京市",
            "adcode": "110000",
            "province": "北京",
            "reporttime": "2020-05-14 17:28:35",
            "casts": [
                {
                    "date": "2020-05-14",
                    "week": "4",
                    "dayweather": "多云",
                    "nightweather": "多云",
                    "daytemp": "30",
                    "nighttemp": "15",
                    "daywind": "东南",
                    "nightwind": "东南",
                    "daypower": "4",
                    "nightpower": "4"
                },
                {
                    "date": "2020-05-15",
                    "week": "5",
                    "dayweather": "多云",
                    "nightweather": "阴",
                    "daytemp": "26",
                    "nighttemp": "15",
                    "daywind": "西南",
                    "nightwind": "西南",
                    "daypower": "≤3",
                    "nightpower": "≤3"
                },
                {
                    "date": "2020-05-16",
                    "week": "6",
                    "dayweather": "晴",
                    "nightweather": "晴",
                    "daytemp": "29",
                    "nighttemp": "14",
                    "daywind": "西北",
                    "nightwind": "西北",
                    "daypower": "5",
                    "nightpower": "5"
                },
                {
                    "date": "2020-05-17",
                    "week": "7",
                    "dayweather": "多云",
                    "nightweather": "多云",
                    "daytemp": "26",
                    "nighttemp": "17",
                    "daywind": "北",
                    "nightwind": "北",
                    "daypower": "4",
                    "nightpower": "4"
                }
            ]
        }
    ]
}
```

## 获取 xml 格式返回值

第三个参数为返回值类型，可选 `json` 与 `xml`，默认 `json`：

```php
$response = $weather->getWeather('北京', 'all', 'xml');
```

### 示例

```xml
<response>
    <status>1</status>
    <count>1</count>
    <info>OK</info>
    <infocode>10000</infocode>
    <lives type="list">
        <live>
            <province>北京</province>
            <city>北京市</city>
            <adcode>110000</adcode>
            <weather>晴</weather>
            <temperature>28</temperature>
            <winddirection>东南</winddirection>
            <windpower>≤3</windpower>
            <humidity>35</humidity>
            <reporttime>2020-05-14 17:28:35</reporttime>
        </live>
    </lives>
</response>
```

## 参数说明

```php
array|string geteather(string $city, string $type = 'base', string $format = 'json')
```

- `$city` - 城市名，比如：“深圳”；
- `$type` - 返回内容类型：`base`: 返回实况天气 / `all`: 返回预报天气；
- `$format` - 输出的数据格式，默认为 json 格式，当 output 设置为 “`xml`” 时，输出的为 XML 格式的数据。

## 在 laravel 中使用

在 Laravel 中使用也是同样的安装方式，配置写在 `config/services.php` 中：

```php
		.
    .
    .
     'weather' => [
        'key' => env('WEATHER_API_KEY'),
    ],
```

然后在 `.env` 中配置 `WEATHER_API_KEY` ：

```php
WEATHER_API_KEY=xxxxxxxxxxxxxxxxxxxxx
```

可以用两种方式来获取 `Overtrue\Weather\Weather` 实例：

#### 方法参数注入

```php
		.
    .
    .
    public function edit(Weather $weather) 
    {
        $response = $weather->getWeather('北京');
    }
    .
    .
    .
```

#### 服务名访问

```php
		.
    .
    .
    public function edit() 
    {
        $response = app('weather')->getWeather('北京');
    }
    .
    .
    .
```

## 参考

- [高德开放平台天气接口](https://lbs.amap.com/api/webservice/guide/api/weatherinfo/)

## License

MIT