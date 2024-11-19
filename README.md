# Metricalo task

* need to have docker in order to run it

```
docker compose up -d
```

* composer install

```
docker exec metricalo_php composer install
```
### API Url
```
localhost:8092
```

### Postman Collection with one endpoint:

```
metricalo.json 
endpoint: localhost:8092/action/{aci|shift4}
```

### Console command
arg can be: {aci|shift4}
```
bin/console start:payment shift4
bin/console start:payment aci
```


## PHPUNIT
there is only unit test for AciProcessor, figure I don't need to have 100% coverage :)
```
bin/phpunit
```
