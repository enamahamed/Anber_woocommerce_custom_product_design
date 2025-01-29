
<?php
defined('ABSPATH') || exit;

// Get order details
if ($order) {
    $order_id = $order->get_id();
?>

<div class="custom-thank-you-page">
    <h2>Thank You for Your Order!</h2>
   
</div>

<?php } else { ?>
    <p>Something went wrong. Please contact support.</p>
<?php } ?>
