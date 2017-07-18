# Silex Config Provider

This provider add these featres:

- Support for YAML config files
- Allow "import" in files
- Allow variables replacement

# Requirements

- Silex 2.0
- PHP7
- Phpunit 6


#Register the provider


```yaml
# config.yml
debug: true
mock: false
# Database connections
database:
    driver:   sqlite
```


```php
$app = new Application();
$app->register(new ConfigProvider(__DIR__ . 'config.yml'), array());

echo $app['config']['database']['driver'] # sqlite
```


## Install the project

    $ docker-compose build
    $ docker-compose run --rm composer install
    
## Run the tests

    $ docker-compose up -d tests
    $ sh scripts/phpunit --converage-html ./coverage