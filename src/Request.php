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
