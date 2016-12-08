<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

module_load_include('inc', 'views', 'handlers/views_handler_filter');

class views_handler_filter_product_suggested extends views_handler_filter {
        
    function query() {
        global $user;
        $query_tids = "SELECT tid FROM {term_node} WHERE nid=" . arg(1);
        //$in = ' (' . implode(", ", $replace) . ')';
        $this->query->add_where($this->options['group'], "term_node.tid in ($query_tids)");
    }

}
