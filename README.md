# simple-api-client
[![GitHub release][ico-release]][link-release]
[![GitHub license][ico-license]][link-license]
[![Travis][ico-testing]][link-testing]
[![HHVM][ico-hhvm]][link-hhvm]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Scrutinizer Coverage][ico-code-coverage]][link-code-coverage]

The simple-api-client library provides an easy way to build wrappers for public REST APIs.

## Install

Via Composer

``` bash
$ composer require atog/simple-api-client
```

## Usage
### Classes
#### Client
The `Client` Class contains the basic Informations about the API liek the `$domain`. It has to extend the abstract class `Atog\Api\Client`
```php
class Client extends \Atog\Api\Client
{
	protected $domain = 'http://example.com/api/v1';
}
```

#### Endpoints
Endpoints provide the functions to query the resources of the API.
```php
class TestEndpoint extends \Atog\Api\Endpoint
{
	protected $endpoint = 'foo';

	public function find($slug)
    {
		$response = $this->client->get($this->getEndpointUrl($slug, true)); // https://example.com/api/v1/foo/$slug gets called
		
        // return new model instance with fetched content if response is okay
		if ($response->isOk()) {
        	return $this->model->newInstance($response->getContent());
        }
    }
}
```
#### Models
The Models are the datastructures of the resources. Attributes can easly be changed with get and set mutators *(much like the Eloquent Mutators from Laravel: https://laravel.com/docs/5.2/eloquent-mutators)*
```php
class TestModel extends Model
{
    public function getNameAttribute($value)
    {
    	return strtoupper($value);
    }

    public function setFirstNameAttribute($value)
    {
    	$this->attributes['first_name'] = strtolower($value);
    }

    public function setContactsAttribute($value)
    {
    	$this->attributes['contacts'] = $this->makeCollection($value, Contacts::class);
    }
}
```

### Initialization
The first parameter defines all endpoints you are using. The second provides some options about the models or cUrl. In the models config we can bind a model to an endpoint

```php
// new Client(array $endpoints, array $config = [])
$client = new Client(
    [
        TestEndpoint::class
    ],
    [
        'models' => [
            'TestEndpoint' => TestModel::class
        ],
        'curl' => [
            CURLOPT_TIMEOUT => 60
        ]
    ]
);
$foo = $client->testEndpoint->find(1); // GET http://example.com/api/v1/foo/1
```
## Change log

### 1.0.0
* Initial Release

## Testing

``` bash
$ composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

[ico-release]: https://img.shields.io/github/release/PascalKleindienst/simple-api-client.svg?style=flat-square
[ico-license]: https://img.shields.io/github/license/PascalKleindienst/simple-api-client.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/PascalKleindienst/simple-api-client.svg?style=flat-square
[ico-code-coverage]: https://img.shields.io/scrutinizer/coverage/g/PascalKleindienst/simple-api-client.svg?style=flat-square
[ico-testing]: https://img.shields.io/travis/PascalKleindienst/simple-api-client.svg?style=flat-square
[ico-hhvm]: https://img.shields.io/hhvm/atog/simple-api-client.svg?style=flat-square

[link-release]: https://github.com/PascalKleindienst/simple-api-client/releases
[link-license]: https://github.com/PascalKleindienst/simple-api-client/blob/master/LICENSE
[link-code-quality]: https://scrutinizer-ci.com/g/PascalKleindienst/simple-api-client/?branch=master
[link-code-coverage]: https://scrutinizer-ci.com/g/PascalKleindienst/simple-api-client/?branch=master
[link-testing]: https://travis-ci.org/PascalKleindienst/simple-api-client
[link-hhvm]: http://hhvm.h4cc.de/package/atog/simple-api-client