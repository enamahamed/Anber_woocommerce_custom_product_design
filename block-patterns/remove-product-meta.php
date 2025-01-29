<?php
register_block_pattern(
    'theme/remove-product-meta',
    array(
        'title'       => __( 'Remove Product Meta', 'theme' ),
        'categories'  => array( 'woocommerce' ),
        'content'     => '
            <!-- wp:template-part {"slug":"single-product","theme":"theme"} /-->
        ',
    )
);
