# 1 Billion Row Challenge in PHP

This is my solution for the 1 billion rows challenge (https://1brc.dev), in PHP.

Create measurements.txt file : 
```php
php createMeasurements.php {number of reccords}
```

If you do have php installed locally but have docker, use `./phpd` instead of `php` to run the command dockerized.

## Results

The script run via the `php:8.3-cli` docker image on Ubuntu 24.04 that itself run in a VM on Windows 10.

- V1 : 112 seconds for 100M lines (893 lines per milliseconds) (Memory 787.5 Kb)