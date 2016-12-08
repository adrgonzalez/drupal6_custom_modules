<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

module_load_include('inc', 'views', 'handlers/views_handler_filter');

class views_handler_filter_product_buyed extends views_handler_filter {

    function query() {

        /*
         * 
         * quitar en visitados los q no estan registrados
         * 
         * nid
         * 
         * todos los uid q tengan ese nid <- in ($query_tids)
         * 
         * todos los nids de esos uids, ordenados por cantidad vistos, esto va en in ($query_tids)
         * 
         * 
         */

        $uids[] = array();
        $results = db_query("SELECT distinct uc_order_products.nid FROM uc_orders
                        inner join uc_order_products on uc_order_products.order_id = uc_orders.order_id
                        where uc_orders.order_status = 'completed'
                        order by uc_orders.modified desc");
        while ($result = db_fetch_object($results)) {
            if ($result) {
                //drupal_set_message("resu: $result_nid->nid");
                $nids[] = $result->nid;
            }
        }
        $nids_aux = implode(",", $nids);

        $this->query->add_where($this->options['group'], " node.nid in ($nids_aux)");
    }

}
