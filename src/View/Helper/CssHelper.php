<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\View;

class CssHelper extends Helper
{
    /**
     * Default storage.
     *
     * @var array
     */
    protected $storage = [];

    /**
     * @param \Cake\View\View $View The View this helper is being attached to.
     * @param array $config Configuration settings for the helper.
     */
    public function __construct(View $View, array $config = [])
    {
        parent::__construct($View, $config);
        if (array_key_exists('storage', $config)) {
            $this->storage += $config['storage'];
        }
    }

    /**
     * Adds a class to the specified container
     *
     * @param string $container The destination container
     * @param string $class The class to add
     * @param string|null $overwrite Replace the class with $class param if exists
     * @return bool True on success, false on failure
     */
    public function add(string $container, string $class, ?string $overwrite = null): bool
    {
        if (!array_key_exists($container, $this->storage)) {
            $this->storage[$container] = [];
        }

        if (is_string($overwrite)) {
            if (in_array($overwrite, $this->storage[$container])) {
                $this->storage[$container][array_search($overwrite, $this->storage[$container])] = $class;

                return true;
            }

            return false;
        }

        if (!in_array($class, $this->storage[$container])) {
            array_push($this->storage[$container], $class);

            return true;
        }

        return false;
    }

    /**
     * Removes a class from a specified container
     *
     * @param string $container The destination container
     * @param string $class The class to remove
     * @return bool True on success, false on failure
     */
    public function remove(string $container, string $class): bool
    {
        if (!array_key_exists($container, $this->storage)) {

            return false;
        }

        if (in_array($class, $this->storage[$container])) {
            unset($this->storage[$container][array_search($class, $this->storage[$container])]);

            return true;
        }

        return false;
    }


    /**
     * Checks if a class exists in a container or not
     *
     * @param string $container
     * @param string $class
     * @return bool True if exists, false otherwise
     */
    public function has(string $container, string $class): bool
    {
        if (!array_key_exists($container, $this->storage)) {

            return false;
        }

        return in_array($class, $this->storage[$container]);
    }

    /**
     * Returns the formatted container as a HTML Tag attribute
     *
     * @param string $container
     * @return string
     */
    public function get(string $container): string
    {
        if (!array_key_exists($container, $this->storage)) {

            return '';
        }

        return sprintf('class="%s"', h(implode(' ', $this->storage[$container])));
    }
}
