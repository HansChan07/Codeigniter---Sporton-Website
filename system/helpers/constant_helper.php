<?php
/* define configuration variables */
$ci = &get_instance();
$ci->load->database();
$query = $ci->db->get('tbl_configurations');
foreach($query->result() as $row) {
	define(strtoupper($row->conf_name),$row->conf_value);
}

date_default_timezone_set(CONF_TIMEZONE);
/* end configuration variables */

