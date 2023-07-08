<?php
require __DIR__ . '/vendor/autoload.php';

use Gbit\Remonline\Api;
use Gbit\Remonline\Models\Order;

$api = new Api("your api key");
$Order = new Order($api);
$echo['Order'] = $Order->page(1)->get();
$echo['CustomFields']  = $Order->getCustomFields();
echo json_encode($echo);
?>
<pre>

</pre>
