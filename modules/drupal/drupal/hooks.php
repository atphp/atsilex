<?php

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use atphp\module\drupal\drupal\Drupal;

define('DRILEX_HOOK_IMPLEMENTATION', 'drilex_drupal,drilex_event,drilex_dispatcher,node_init,system_exit,system_boot,system_batch_alter,system_drupal_goto_alter,system_css_alter,system_ajax_render_alter,node_custom_theme,system_contextual_links_view_alter,user_cron,node_user_load,node_user_login,node_user_logout,system_watchdog');

/**
 * @param Drupal $drupal
 * @return Drupal
 */
function drilex_drupal(Drupal $drupal = null)
{
    static $_drupal;

    if (null !== $drupal) {
        $_drupal = $drupal;
    }

    return $_drupal;
}

/**
 * @param array $params
 * @return GenericEvent
 */
function drilex_event(array &$params = [])
{
    return new GenericEvent(drilex_drupal(), $params);
}

/**
 * @param EventDispatcherInterface $dispatcher
 * @return EventDispatcherInterface
 */
function drilex_dispatcher(EventDispatcherInterface $dispatcher = null)
{
    static $_dispatcher = null;

    if (null !== $dispatcher) {
        $_dispatcher = $dispatcher;
    }

    return $_dispatcher;
}

/**
 * Implements hook_init()
 */
function node_init()
{
    return drilex_dispatcher()->dispatch('drupal.init');
}

/**
 * Implements hook_exit().
 *
 * @param string $destination
 */
function system_exit($destination)
{
    $params = ['destination' => $destination];
    drilex_dispatcher()->dispatch('drupal.exit', drilex_event($params));
}

/**
 * Implements hook_boot().
 */
function system_boot()
{
    drilex_dispatcher()->dispatch('drupal.boot', drilex_event());
}

/**
 * Implements hook_batch_alter().
 *
 * @param $batch
 */
function system_batch_alter(&$batch)
{
    $params = ['batch' => $batch];
    drilex_dispatcher()->dispatch('drupal.batch_alter', drilex_event($params));
}

/**
 * Implements hook_drupal_goto_alter().
 */
function system_drupal_goto_alter(&$path, &$options, &$http_response_code)
{
    $params = [
        'path'               => $path,
        'options'            => $options,
        'http_response_code' => $http_response_code,
    ];
    drilex_dispatcher()->dispatch('drupal.goto_alter', drilex_event($params));
}

/**
 * Implements hook_css_alter().
 *
 * @param $css
 */
function system_css_alter(&$css)
{
    $params = ['css' => $css];
    drilex_dispatcher()->dispatch('drupal.css_alter', drilex_event($params));
}

/**
 * Implements hook_ajax_render_alter().
 *
 * @param $commands
 */
function system_ajax_render_alter(&$commands)
{
    $params = ['commands' => $commands];
    drilex_dispatcher()->dispatch('drupal.ajax_render_alter', drilex_event($params));
}

/**
 * Implements hook_custom_theme()
 */
function node_custom_theme()
{
    drilex_dispatcher()->dispatch('drupal.custom_theme', drilex_event());
}

/**
 * Implements hook_contextual_links_view_alter()
 */
function system_contextual_links_view_alter(&$element, $items)
{
    $params = ['element' => $element, 'items' => $items];
    drilex_dispatcher()->dispatch('drupal.contextual_links_view_alter', drilex_event($params));
}

/**
 * Implements hook_cron().
 */
function user_cron()
{
    drilex_dispatcher()->dispatch('drupal.cron', drilex_event());
}

/**
 * Implements hook_user_load().
 *
 */
function node_user_load($users)
{
    $params = ['users' => $users];
    drilex_dispatcher()->dispatch('drupal.user_load', drilex_event($params));
}

/**
 * Implements hook_user_login().
 */
function node_user_login(&$edit, $account)
{
    $params = ['edit' => $edit, 'account' => $account];
    drilex_dispatcher()->dispatch('drupal.user_login', drilex_event($params));
}

/**
 * Implements hook_user_logout().
 */
function node_user_logout($account)
{
    $params = ['account' => $account];
    drilex_dispatcher()->dispatch('drupal.user_logout', drilex_event($params));
}

/**
 * Implements hook_watchdog().
 *
 * @param array $log_entry
 */
function system_watchdog(array $log_entry)
{
    $params = ['log_entry' => $log_entry];
    drilex_dispatcher()->dispatch('drupal.watchdog', drilex_event($params));
}
