<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

module_load_include('inc', 'views', 'handlers/views_handler_filter');

class views_handler_filter_product_consulted extends views_handler_filter {

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
        $results = db_query("SELECT uid FROM {product_visited} where nid=" . arg(1));
        while ($result = db_fetch_object($results)) {
            if ($result) {
                //drupal_set_message("resu: $result->uid");
                $results_nids = db_query("SELECT nid FROM {product_visited} where uid=" . $result->uid .
                        ' order by counter');
                while ($result_nid = db_fetch_object($results_nids)) {
                    if ($result_nid) {
                        //drupal_set_message("resu: $result_nid->nid");
                        $nids[] = $result_nid->nid;
                    }
                }
            }
        }
        if (empty($nids)) {
            $nids_aux = 0;
        } else {
            $nids_aux = implode(",", $nids);
        }
        $this->query->add_where($this->options['group'], " node.nid in ($nids_aux)");
    }
}
