<?php

class uc_product_handler_field_userpoints extends views_handler_field {
    function query() {
        $this->ensure_my_table();
        $this->add_additional_fields();
    }

    function element_type() {
        if (isset($this->definition['element type'])) {
            return $this->definition['element type'];
        }
        return 'div';
    }

    function render($values) {
        // TODO deberia consultar el campo que se define como store credit: user points

        $userpoints_ubercart_payment = db_result(db_query ("SELECT value FROM {variable} WHERE name='userpoints_ubercart_payment'"));

        //s:5:"18.66";
        $precio_puntos = split('"', $userpoints_ubercart_payment);
        $titi_pts_valor = $precio_puntos[1];
        if ($titi_pts_valor <= 0){
            drupal_set_message('$titi_pts_valor cero!');
        }
        $precio_points = $values->uc_products_sell_price * $titi_pts_valor;
        return $precio_points;
    }
}