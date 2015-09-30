<?php

namespace atphp\module\drupal\drupal\api;

class ThemeAPI
{

    public function drupalPreRenderConditionalComments($elements)
    {
        return drupal_pre_render_conditional_comments($elements);
    }

    public function drupalPreRenderLink($element)
    {
        return drupal_pre_render_link($element);
    }

    public function drupalPreRenderLinks($element)
    {
        return drupal_pre_render_links($element);
    }

    public function drupalPreRenderMarkup($elements)
    {
        return drupal_pre_render_markup($elements);
    }

    public function drupalPreRenderStyles($elements)
    {
        return drupal_pre_render_styles($elements);
    }

    public function drupalRender(&$elements)
    {
        return drupal_render($elements);
    }

    public function drupalRenderCacheByQuery($query, $function, $expire = CACHE_TEMPORARY, $granularity = null)
    {
        return drupal_render_cache_by_query($query, $function, $expire, $granularity);
    }

    public function drupalRenderCacheGet($elements)
    {
        return drupal_render_cache_get($elements);
    }

    public function drupalRenderCacheSet(&$markup, $elements)
    {
        return drupal_render_cache_set($markup, $elements);
    }

    public function drupalRenderChildren(&$element, $childrenKeys = null)
    {
        return drupal_render_children($element, $childrenKeys);
    }

    public function drupalRenderCidCreate($elements)
    {
        return drupal_render_cid_create($elements);
    }

    public function drupalRenderCidParts($granularity = null)
    {
        return drupal_render_cid_parts($granularity);
    }

    public function drupalRenderCollectAttached($elements, $return = false)
    {
        return drupal_render_collect_attached($elements, $return);
    }

    public function drupalRenderPage($page)
    {
        return drupal_render_page($page);
    }

    public function elementChild($key)
    {
        return element_child($key);
    }

    public function elementChildren(&$elements, $sort = false)
    {
        return element_children($elements, $sort);
    }

    public function elementGetVisibleChildren(array $elements)
    {
        return element_get_visible_children($elements);
    }

    public function elementInfo($type)
    {
        return element_info($type);
    }

    public function elementInfoProperty($type, $propertyName, $default = null)
    {
        return element_info_property($type, $propertyName, $default);
    }

    public function elementProperties($element)
    {
        return element_properties($element);
    }

    public function elementProperty($key)
    {
        return element_property($key);
    }

    public function elementSetAttributes(array &$element, array $map)
    {
        return element_set_attributes($element, $map);
    }

    public function elementSort($a, $b)
    {
        return element_sort($a, $b);
    }

    public function elementSortByTitle($a, $b)
    {
        return element_sort_by_title($a, $b);
    }

    public function filterXss($string, $allowedTags = ['a', 'em', 'strong', 'cite', 'blockquote', 'code', 'ul', 'ol', 'li', 'dl', 'dt', 'dd'])
    {
        return filter_xss($string, $allowedTags);
    }

    public function filterXssAdmin($string)
    {
        return filter_xss_admin($string);
    }

    public function filterXssBadProtocol($string, $decode = true)
    {
        return filter_xss_bad_protocol($string, $decode);
    }

    public function formatDate($timestamp, $type = 'medium', $format = '', $timezone = null, $languageCode = null)
    {
        return format_date($timestamp, $type, $format, $timezone, $languageCode);
    }

    public function formatInterval($interval, $granularity = 2, $languageCode = null)
    {
        return format_interval($interval, $granularity, $languageCode);
    }

    public function formatPlural($count, $singular, $plural, array $args = [], array $options = [])
    {
        return format_plural($count, $singular, $plural, $args, $options);
    }

    public function formatRssChannel($title, $link, $description, $items, $languageCode = null, $args = [])
    {
        return format_rss_channel($title, $link, $description, $items, $languageCode, $args);
    }

    public function formatRssItem($title, $link, $description, $args = [])
    {
        return format_rss_item($title, $link, $description, $args);
    }

    public function formatSize($size, $languageCode = null)
    {
        return format_size($size, $languageCode);
    }

    public function formatUsername($account)
    {
        return format_username($account);
    }

    public function formatXml_elements($array)
    {
        return format_xml_elements($array);
    }

    public function hide(&$element)
    {
        return hide($element);
    }

    public function render(&$element)
    {
        return render($element);
    }

    public function show(&$element)
    {
        return show($element);
    }

    public function drupal_add_css($data = null, $options = null)
    {
        return drupal_add_css($data, $options);
    }

    public function drupal_add_feed($url = null, $title = '')
    {
        return drupal_add_feed($url, $title);
    }

    public function drupal_add_html_head($data = null, $key = null)
    {
        return drupal_add_html_head($data, $key);
    }

    public function drupal_add_html_head_link($attributes, $header = false)
    {
        return drupal_add_html_head_link($attributes, $header);
    }

    public function drupal_add_js($data = null, $options = null)
    {
        return drupal_add_js($data, $options);
    }

    public function drupal_add_library($module, $name, $everyPage = null)
    {
        return drupal_add_library($module, $name, $everyPage);
    }

    public function drupal_add_region_content($region = null, $data = null)
    {
        return drupal_add_region_content($region, $data);
    }

    public function drupal_add_tabledrag($tableId, $action, $relationship, $group, $subgroup = null, $source = null, $hidden = true, $limit = 0)
    {
        return drupal_add_tabledrag($tableId, $action, $relationship, $group, $subgroup, $source, $hidden, $limit);
    }

    public function drupal_aggregate_css(&$cssGroups)
    {
        return drupal_aggregate_css(&$cssGroups);
    }

    public function drupal_attributes(array $attributes = [])
    {
        return drupal_attributes($attributes);
    }

    public function drupal_clean_css_identifier($identifier, $filter = [' ' => '-', '_' => '-', '/' => '-', '[' => '-', ']' => ''])
    {
        return drupal_clean_css_identifier($identifier, $filter);
    }

    public function drupal_clear_css_cache()
    {
        return drupal_clear_css_cache();
    }

    public function drupal_clear_js_cache()
    {
        return drupal_clear_js_cache();
    }

    public function drupal_common_theme()
    {
        return drupal_common_theme();
    }

    public function drupal_get_css($css = null, $skipAlter = false)
    {
        return drupal_get_css($css, $skipAlter);
    }

    public function drupal_get_breadcrumb()
    {
        return drupal_get_breadcrumb();
    }

    public function drupal_get_feeds($delimiter = "\n")
    {
        return drupal_get_feeds($delimiter);
    }

    public function drupal_get_html_head()
    {
        return drupal_get_html_head();
    }

    public function drupal_get_js($scope = 'header', $javascript = null, $skipAlter = false)
    {
        return drupal_get_js($scope, $javascript, $skipAlter);
    }

    public function drupal_get_library($module, $name = null)
    {
        return drupal_get_library($module, $name);
    }

    public function drupal_group_css($css)
    {
        return drupal_group_css($css);
    }

    public function drupal_html_class($class)
    {
        return drupal_html_class($class);
    }

    public function drupal_html_id($id)
    {
        return drupal_html_id($id);
    }

    public function drupal_js_defaults($data = null)
    {
        return drupal_js_defaults($data);
    }

    public function drupal_page_footer()
    {
        return drupal_page_footer();
    }

    public function drupal_sort_css_js($a, $b)
    {
        return drupal_sort_css_js($a, $b);
    }

    function drupal_load_stylesheet($file, $optimize = null, $reset_basepath = true)
    {
    }

    function drupal_load_stylesheet_content($contents, $optimize = false)
    {
    }

    function drupal_set_breadcrumb($breadcrumb = null)
    {
    }

    function drupal_set_page_content($content = null)
    {
    }

}
