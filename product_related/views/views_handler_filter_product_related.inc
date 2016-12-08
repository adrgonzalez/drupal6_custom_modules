<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

module_load_include('inc', 'views', 'handlers/views_handler_filter');

class views_handler_filter_product_related extends views_handler_filter {

    function query() {

        $nid = node_load(arg(1));

        $nid_autor = 0;
        $array = $nid->field_autor[0];
        if (!empty($array)) {
            foreach ($array as $i => $value) {
                $nid_autor = $array[$i];
            }
        }
        global $user;
        $this->query->add_where($this->options['group'], "node_data_field_autor.field_autor_nid = $nid_autor");
    }

}
