<?php

function &drupal_array_get_nested_value(array &$array, array $parents, &$key_exists = NULL) {}
function archiver_get_archiver($file) {}
function archiver_get_extensions() {}
function archiver_get_info() {}
function date_iso8601($date) {}
function debug($data, $label = NULL, $print_r = FALSE) {}
# function drupal_access_denied() {}
function drupal_array_diff_assoc_recursive($array1, $array2) {}
function drupal_array_nested_key_exists(array $array, array $parents) {}
function drupal_array_set_nested_value(array &$array, array $parents, $value, $force = FALSE) {}
function drupal_build_css_cache($css) {}
function drupal_build_js_cache($files) {}
function drupal_check_incompatibility($v, $current_version) {}
function drupal_cron_cleanup() {}
function drupal_cron_run() {}
function drupal_delete_file_if_stale($uri) {}
# function drupal_deliver_html_page($page_callback_result) {}
function drupal_deliver_page($page_callback_result, $default_delivery_callback = NULL) {}
# function drupal_exit($destination = NULL) {}
function drupal_explode_tags($tags) {}
function drupal_get_destination() {}
function drupal_get_filetransfer_info() {}
function drupal_get_private_key() {}
function drupal_get_profile() {}
function drupal_get_query_array($query) {}
function drupal_get_query_parameters(array $query = NULL, array $exclude = array('q'), $parent = '') {}
function drupal_get_rdf_namespaces() {}
function drupal_get_region_content($region = NULL, $delimiter = ' ') {}
function drupal_get_schema_unprocessed($module, $table = NULL) {}
function drupal_get_token($value = '') {}
function drupal_get_updaters() {}
# function drupal_goto($path = '', array $options = array(), $http_response_code = 302) {}
function drupal_http_build_query(array $query, $parent = '') {}
# function drupal_http_header_attributes(array $attributes = array()) {}
function drupal_http_request($url, array $options = array()) {}
function drupal_map_assoc($array, $function = NULL) {}
function drupal_not_found() {}
function drupal_page_set_cache() {}
function drupal_parse_dependency($dependency) {}
function drupal_parse_info_file($filename) {}
function drupal_parse_info_format($data) {}
function drupal_parse_url($url) {}
function drupal_process_attached($elements, $group = JS_DEFAULT, $dependency_check = FALSE, $every_page = NULL) {}
function drupal_process_states(&$elements) {}
function drupal_region_class($region) {}
function drupal_set_time_limit($time_limit) {}
function drupal_site_offline() {}
function drupal_sort_title($a, $b) {}
function drupal_sort_weight($a, $b) {}
function drupal_strip_dangerous_protocols($uri) {}
function drupal_system_listing($mask, $directory, $key = 'name', $min_depth = 1) {}
function drupal_uninstall_schema($module) {}
function drupal_valid_token($token, $value = '', $skip_anonymous = FALSE) {}
function drupal_write_record($table, &$record, $primary_keys = array()) {}
function fix_gpc_magic() {}
function flood_clear_event($name, $identifier = NULL) {}
function flood_is_allowed($name, $threshold, $window = 3600, $identifier = NULL) {}
function flood_register_event($name, $window = 3600, $identifier = NULL) {}
function parse_size($size) {}
function watchdog_severity_levels() {}
function xmlrpc($url, $args, $options = array()) {}
