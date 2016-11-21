## Laravel 5 package for football-data.org
============================

[![football-data.org](http://football-data.org)](http://football-data.org)

This package sole purpose is to make your interaction with football-data.org a bit easier.
Pull the package - register in football-data.org

Put in your .env file
```
FBD_TOKEN=
FBD_URL=http://api.football-data.org/v1/
```

All the responses are returned as laravel collections for easier management after the request


```
composer require xxaxxo/fbd dev-master
```


Add
```
use xxaxxo\fbd\Services\FootballData\FootballData;
```
in your controller after getting the package and feed it to the model
