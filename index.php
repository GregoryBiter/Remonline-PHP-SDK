<?php
require __DIR__ . '/vendor/autoload.php';

use Gbit\Remonline\RemonlineClient;
use Gbit\Remonline\Models\Organization;

$api = new RemonlineClient("1830884e0176463b802684a7cef4ccaf");
$organization = new Organization($api);
$result = $organization->get(); // Example method call, adjust as needed

?>
<pre>
<?php print_r($result); ?>
<?php print_r($organization->meta()); // Example to show meta data if available ?>
</pre>
