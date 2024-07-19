# 1 Billion Row Challenge in PHP

This is my solution for the 1 billion rows challenge (https://1brc.dev), in PHP.

Create measurements.txt file : 
```php
php createMeasurements.php {number of reccords}
```

If you do have php installed locally but have docker, use `./phpd` instead of `php` to run the command dockerized.

## Results

The script run via the `php:8.3-cli` docker image on Ubuntu 24.04 that itself run in a VM on Windows 10.  
CPU speed is 3.5Ghz per real core.

| version | Description                                                                                       | Total time          | lines / ms | Peak memory usage    |
|---------|---------------------------------------------------------------------------------------------------|---------------------|------------|----------------------|
| 1       | The first, naive version                                                                          | 112s for 100M rows | 893        | 2.1 Mb real (792 Kb) |
| 2       | Replace usage of `fgetcsv` by `fgets`                                                             | 37s for 100M rows  | 2660       | 2.1 Mb real (792 Kb) |
| 3       | Optimize the body of the loop that reads the data to have less array access | 25s for 100M rows   | 4000       | 2.1 Mb real (804 Kb) |
