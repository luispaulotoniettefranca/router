<?php

namespace Toniette\Router;


class Router extends Dispatch
{
    /**
     * Router constructor.
     *
     * @param string $projectUrl
     * @param null|string $separator
     */
    public function __construct(string $projectUrl, ?string $separator = ":")
    {
        parent::__construct($projectUrl, $separator);
    }

    /**
     * @param string $route
     * @param $handler
     * @param string|null $name
     */
    public function post(string $route, $handler, string $name = null): void
    {
        $this->addRoute("POST", $route, $handler, $name);
    }

    /**
     * @param string $route
     * @param $handler
     * @param string|null $name
     */
    public function get(string $route, $handler, string $name = null): void
    {
        $this->addRoute("GET", $route, $handler, $name);
    }

    /**
     * @param string $route
     * @param $handler
     * @param string|null $name
     */
    public function put(string $route, $handler, string $name = null): void
    {
        $this->addRoute("PUT", $route, $handler, $name);
    }

    /**
     * @param string $route
     * @param $handler
     * @param string|null $name
     */
    public function patch(string $route, $handler, string $name = null): void
    {
        $this->addRoute("PATCH", $route, $handler, $name);
    }

    /**
     * @param string $route
     * @param $handler
     * @param string|null $name
     */
    public function delete(string $route, $handler, string $name = null): void
    {
        $this->addRoute("DELETE", $route, $handler, $name);
    }

    /**
     * @param string $controller
     * @param array|string[] $protected
     * @param bool $api
     */
    public function resource(string $controller, bool $api = false): void
    {
        $routes = [
            "rootG" => ["method" => "get", "route" => ""],
            "index" => ["method" => "get", "route" => "/"],
            "show" => ["method" => "get", "route" => "/{key}"],
            "create" => ["method" => "get", "route" => "/create"],
            "rootP" => ["method" => "post", "route" => ""],
            "store" => ["method" => "post", "route" => "/"],
            "edit" => ["method" => "get", "route" => "/{key}/edit"],
            "update" => ["method" => "patch", "route" => "/{key}/update"],
            "destroy" => ["method" => "delete", "route" => "/{key}/destroy"]
        ];
        if ($api) {
            unset($routes["create"]);
            unset($routes["edit"]);
        } else {
            $routes["destroy"]["method"] = "get";
            $routes["update"]["method"] = "post";
        }
        $group = $this->group;
        $this->group(null);
        foreach ($routes as $key => $value) {
            $key = $key == "rootG" ? "index" : $key;
            $key = $key == "rootP" ? "store" : $key;
            $this->{$value["method"]}("/" . $group . "/" . strtolower($controller) . $value["route"], $controller . ":{$key}", $controller . ".{$key}");
        }
        $this->group($group);
    }

    /**
     * @param  array $methods
     * @param string $route
     * @param $handler
     * @param string|null $name
     */
    public function match(array $methods, string $route, $handler, string $name = null): void
    {
        foreach($methods as $method) {
            $this->addRoute(strtoupper($method), $route, $handler, $name);
        }
    }
}