<?php

/**
 * Implementation of hook_views_data().
 */
function uc_product_preciopoints_views_data() {
    $data['uc_products']['userpoints'] = array(
        'title' => t('userpoints'),
        'help' => t('The cost of the product to the store in userpoints.'),
        'field' => array(
            'handler' => 'uc_product_handler_field_userpoints',
        ),
    );

    return $data;
}

/**
 * Implementation of hook_views_handlers().
 */
function uc_product_preciopoints_views_handlers() {
    return array(
        'info' => array(
            'path' => drupal_get_path('module', 'uc_product_preciopoints') . '/views',
        ),
        'handlers' => array(
            'uc_product_handler_field_userpoints' => array(
                'parent' => 'views_handler_field',
            ),
        ),
    );
}

?>
