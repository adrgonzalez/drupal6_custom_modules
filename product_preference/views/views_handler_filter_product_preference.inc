<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

module_load_include('inc', 'views', 'handlers/views_handler_filter');

class views_handler_filter_product_preference extends views_handler_filter {
        
    function query() {
        global $user;
        $uids[] = array();
        $results = db_query("SELECT uid FROM {user_preference} where nid=" . 535);
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
        $nids_aux = implode(",", $nids);
        
        global $user;
        //drupal_set_message('arg: '.arg(1));
        $query_tids = "SELECT tid FROM {term_node} WHERE nid=" . arg(1);
        //$in = ' (' . implode(", ", $replace) . ')';
        $this->query->add_where($this->options['group'], " node.nid in ($nids_aux)");
    }

}
