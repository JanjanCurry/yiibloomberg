<?php

class UrlRuleDash extends CBaseUrlRule {
    /**
     * Creates a URL based on this rule.
     * @param CUrlManager $manager the manager
     * @param string      $route the route
     * @param array       $params list of parameters
     * @param string      $ampersand the token separating name-value pairs in the URL.
     * @return mixed the constructed URL or false on error
     */
    public function createUrl ($manager, $route, $params, $ampersand) {
        $url = false;

        switch ($route) {
            case 'equity/index':
            case 'commodity/index':
            case 'currency/index':
            case 'trade/country':
            case 'trade/index':
                if (!empty($params['id'])) {
                    $route .= '/' . $params['id'];
                    unset($params['id']);

                    $url = $route;
                    if (!empty($params)) {
                        $url = $route . '/' . $manager->createPathInfo($params, '-', '/');
                    }
                }
                break;

            case 'macro/index':
                if (!empty($params)) {
                    $url = 'economics/index/' . $manager->createPathInfo($params, '-', '/');
                }
                break;
        }

        return $url;
    }

    /**
     * Parses a URL based on this rule.
     * @param CUrlManager  $manager the URL manager
     * @param CHttpRequest $request the request object
     * @param string       $pathInfo path info part of the URL
     * @param string       $rawPathInfo path info that contains the potential URL suffix
     * @return mixed the route that consists of the controller ID and action ID or false on error
     */
    public function parseUrl ($manager, $request, $pathInfo, $rawPathInfo) {
        $page = '';


        if (preg_match('/^trade\/country\/[a-zA-Z]{3}\/.+/i', $rawPathInfo) == 1) {
            $page = 'trade/country';
            $rawPathInfo = 'id-' . str_replace($page . '/', '', $rawPathInfo);

        } else if (preg_match('/^(macro|economics)\/index\/.+/i', $rawPathInfo) == 1) {
            $page = 'macro/index';
            $rawPathInfo = 'id-' . str_replace($page . '/', '', $rawPathInfo);

        } else if (preg_match('/^commodity\/index\/.+\/.+/i', $rawPathInfo) == 1) {
            $page = 'commodity/index';
            $rawPathInfo = 'id-' . str_replace($page . '/', '', $rawPathInfo);

        } else if (preg_match('/^currency\/index\/.+\/.+/i', $rawPathInfo) == 1) {
            $page = 'currency/index';
            $rawPathInfo = 'id-' . str_replace($page . '/', '', $rawPathInfo);

        } else if (preg_match('/^equity\/index\/.+\/.+/i', $rawPathInfo) == 1) {
            $page = 'equity/index';
            $rawPathInfo = 'id-' . str_replace($page . '/', '', $rawPathInfo);
        }

        if (!empty($page)) {
            $pairs = explode('/', $rawPathInfo);

            foreach ($pairs as $pair) {
                if ($pair != '') {
                    $exploded = explode('-', $pair);
                    if (sizeof($exploded) == 2) {
                        $_REQUEST[$exploded[0]] = $_GET[$exploded[0]] = $exploded[1];
                    } elseif (sizeof($exploded) == 3) {
                        $_REQUEST[$exploded[0] . '-' . $exploded[1]] = $_GET[$exploded[0] . '_' . $exploded[1]] = $exploded[2];
                    }
                }
            }

            return $page;
        }

        return false;
    }
}