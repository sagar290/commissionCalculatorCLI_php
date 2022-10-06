# Commission Calculator

A simple commission calculator to calculate the commission for a given amount by csv.


## Requirements
* php 7.4 or higher
* composer
* a csv file with the following structure


CSV File Structure
* date
* user_id
* user_type
* operation_type
* amount
* currency

``` csv
2014-12-31,4,private,withdraw,1200.00,EUR
2015-01-01,4,private,withdraw,1000.00,EUR
2016-01-05,4,private,withdraw,1000.00,EUR
2016-01-05,1,private,deposit,200.00,EUR
2016-01-06,2,business,withdraw,300.00,EUR
2016-01-06,1,private,withdraw,30000,JPY
2016-01-07,1,private,withdraw,1000.00,EUR
2016-01-07,1,private,withdraw,100.00,USD
2016-01-10,1,private,withdraw,100.00,EUR
2016-01-10,2,business,deposit,10000.00,EUR
2016-01-10,3,private,withdraw,1000.00,EUR
2016-02-15,1,private,withdraw,300.00,EUR
2016-02-19,5,private,withdraw,3000000,JPY
```
Note: `don't use headings in the csv file`

## Installation
```bash 
composer install
```


## Usage

```bash 

php cli.php input.csv

```

## Output

```bash
0.6
3
0
0.06
1.5
0
3
0.27
0
3
0
0
68.77

```

## Testing

```bash
composer test
```