<?php

namespace Directus\Session\Storage;

interface SessionStorageInterface
{
    /**
     * Start session storage.
     *
     * @throws \RuntimeException if session fails to start
     *
     * @return bool
     */
    public function start();

    /**
     * Stop session storage.
     */
    public function stop();

    /**
     * Check if session was started.
     *
     * @return bool
     */
    public function isStarted();

    /**
     * Get the session name.
     *
     * @return string
     */
    public function getName();

    /**
     * Get a session attribute.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * Get all session attribute.
     *
     * @return array
     */
    public function getItems();

    /**
     * Set a session attribute.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value);

    /**
     * Remove a session attribute.
     *
     * @param $key
     */
    public function remove($key);

    /**
     * Clear all session keys.
     */
    public function clear();
}
