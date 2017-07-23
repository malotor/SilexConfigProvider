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
debug: false
# Database connections
database:
    driver:   sqlite
```


```yaml
# config_dev.yml
imports:
  - { resource: config.yml }
debug: true
config_path : %base_path%
```


```php
$app = new Application();
$app['base_path'] = '/home/user/config';

$app->register(new ConfigProvider(__DIR__ . 'config_dev.yml'), array());
#Import
echo $app['config']['database']['driver'] # sqlite
#Override
echo $app['config']['debug'] # true
#Replacement
echo $app['config']['debug'] # /home/user/config

```


## Install the project

    $ docker-compose build
    $ docker-compose run --rm composer install
    
## Run the tests

    $ docker-compose up -d tests
    $ sh scripts/phpunit --converage-html ./coverage