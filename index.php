<?php
require __DIR__ . '/vendor/autoload.php';

use GBIT\Remonline\Api;
use GBIT\Remonline\Models\Order;

$reapi = new Api("your api key");
$Order = new Order($api);
$echo['Order'] = $Order->page(1)->get();
$echo['CustomFields']  = $Order->getCustomFields();
echo json_encode($echo);
?>
<pre>

</pre>
