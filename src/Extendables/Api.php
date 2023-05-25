<?php

namespace Blax\Wordpress\Extendables;

abstract class Api
{
    public const NAMESPACE = 'blax/v1';
    public const ROUTE = '/example';

    abstract public static function handle(\WP_REST_Request $request);

    public function __construct()
    {
        add_action('rest_api_init', function () {
            register_rest_route(
                static::getRestNamespace(),
                static::getRestRoute(),
                [
                    'methods' => 'POST',
                    'callback' => function (\WP_REST_Request $request) {
                        return static::handle($request);
                    }
                ]
            );
        });
    }

    /*
     * |--------------------------------------------------------------------------
     * | Get rest namespace
     * |--------------------------------------------------------------------------
     * |
     * | Gets the defined namespace form the extending class or use plugins default
     * |
     */
    private static function getRestNamespace()
    {
        return static::NAMESPACE ?? Plugin::getVar('rest_namespace');
    }

    /*
     * |--------------------------------------------------------------------------
     * | Get rest route
     * |--------------------------------------------------------------------------
     * |
     * | Gets the defined route form the extending class or throw error
     * |
     */
    private static function getRestRoute()
    {
        if (static::ROUTE) {
            return static::ROUTE;
        } else {
            throw new \Exception('No route defined for ' . get_called_class());
        }
    }
}
