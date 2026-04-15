<?php

// Backwards compatibility aliases: map old Gbit\Remonline classes to new Gbit\Roapp classes
// Loaded via composer `autoload.files` so aliases are available on autoload.

/* Client & Exception */
if (!class_exists('\Gbit\Remonline\RemonlineClient') && class_exists('\Gbit\Roapp\RoappClient')) {
    class_alias('\Gbit\Roapp\RoappClient', '\Gbit\Remonline\RemonlineClient');
}

if (!class_exists('\Gbit\Remonline\RemonlineApiException') && class_exists('\Gbit\Roapp\RoappApiException')) {
    class_alias('\Gbit\Roapp\RoappApiException', '\Gbit\Remonline\RemonlineApiException');
}

/* Models */
$models = [
    'Assets','Booking','Cashbox','ConnectModel','Estimate','Invoice','Kit','Models','Order',
    'Organization','People','Postings','Product','Report','Sale','Service','Setting',
    'SMS','Task','User','Warehouse','Webhook'
];

foreach ($models as $m) {
    $old = "\\Gbit\\Remonline\\Models\\{$m}";
    $new = "\\Gbit\\Roapp\\Models\\{$m}";
    if (!class_exists($old) && class_exists($new)) {
        class_alias($new, $old);
    }
}
