<?php


namespace Toniette\Router;


use Toniette\Router\Router;


class Request
{
    /**
     * @var array|string|bool
     */
    public array|string|bool $headers;
    /**
     * @var array|object
     */
    public array|object $body;
    /**
     * @var null|Router
     */
    public null|Router $router;

    /**
     * Request constructor.
     * @param array $body
     * @param null|Router $router
     */
    public function __construct(array $body = [], null|Router $router = null)
    {
        if (!function_exists('getallheaders')) {
            function getallheaders() {
                $headers = [];
                foreach ($_SERVER as $name => $value) {
                    if (substr($name, 0, 5) == 'HTTP_') {
                        $headers[str_replace(' ', '-', 
                        ucwords(strtolower(str_replace('_', ' ',
                        substr($name, 5)))))] = $value;
                    }
                }
                return $headers;
                }
        }
        $this->headers = getallheaders();
        $this->body = $body;
        $this->router = $router;
    }

    /**
     * @param $index
     * @return mixed
     */
    public function __get($index): mixed
    {
        return $this->body[$index];
    }

    public function __set(string $name, mixed $value): void
    {
        $this->body[$name] = $value;
    }

    public function __invoke(): array
    {
        return [
                "REMOTE_ADDR" => $_SERVER["REMOTE_ADDR"],
                "SERVER_PROTOCOL" => $_SERVER["SERVER_PROTOCOL"],
                "REQUEST_METHOD" => $_SERVER["REQUEST_METHOD"],
                "REQUEST_URI" => $_SERVER["REQUEST_URI"],
                "HTTP_USER_AGENT" => $_SERVER["HTTP_USER_AGENT"],
                "HEADERS"  => getallheaders(),
                "BODY" => $_REQUEST
        ];
    }

    public function header($index = false): array|bool|string|null
    {
        if ($index) {
            return (isset($this->headers[$index]) ? $this->headers[$index] : null);
        } else {
            return $this->headers;
        }
    }

    public function validate(array $options): Request
    {
        // verify |&&| sanitize variables
        foreach ($options as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $filter) {
                    $this->body[$key] = filter_var($this->body[$key], $filter);
                }
            }
            $this->body[$key] = filter_var($this->body[$key], $value);
        }

        //unset unverified variables
        foreach ($this->body as $key => $value) {
            if (!in_array($key, array_keys($options))) {
                unset($this->body[$key]);
            }
        }
        return $this;
    }
}
