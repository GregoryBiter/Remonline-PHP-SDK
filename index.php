<?php
require __DIR__ . '/vendor/autoload.php';
use Gbit\Roapp\RoappClient;
use Gbit\Roapp\Models\Organization;

$api = new RoappClient("1830884e0176463b802684a7cef4ccaf");
$organization = new Organization($api);
$result = $organization->get(); // Example method call, adjust as needed

?>
<pre>
<?php print_r($result); ?>
<?php print_r($organization->meta()); // Example to show meta data if available ?>
</pre>
