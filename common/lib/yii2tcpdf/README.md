
TCPDF TH
============================================================

Version: 1.0.1

Release date: 2016-03-13

Author:	Eakkabin Jaikeawma (CoachMaxz)

------------------------------------------------------------

Install 
============================================================

```
$ composer require drivesoftz/yii2-tcpdf ~1.0
```

Example 
============================================================

```php
use TCPDF;

$items = array(
    'header' => array(
        array('#', 'text', 15, 'C', ''),
        array('ID', 'text', 35, 'L', ''),
        array('Name', 'text', 55, 'L', ''),
        array('Price', 'number', 25, 'R', ' Baht.'),
        array('Amount', 'number', 25, 'R', ' items.'),
        array('Total', 'number', 25, 'R', ' Baht.'),
    ),
    'items' => array(
        array('1', 'PRO5900001', 'Yii 2 Framework Book', 250, 3, 750)
    )
);
        
(new TCPDF('P'))->table('product-table.pdf', $items);
```

<span class="right">![yii2-tcpdf](img/2016-02-27_10-52-00.png)</span>
