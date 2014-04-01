<?php

/**
 * Provides information about the Request.
 *
 * @return \Symfony\Component\HttpFoundation\Request
 */
function _request()
{
    return \Symfony\Component\HttpFoundation\Request::createFromGlobals();
}

/**
 * Returns a logging instance for use by the Application.
 *
 * @return \Psr\Log\LoggerInterface
 */
function _log()
{
    return new \Psr\Log\NullLogger();
}

/**
 * Returns a Razor HTTP instance for working with requests.
 *
 * @return \Http
 */
function _http(\Psr\Log\LoggerInterface $_log)
{
    return new Http($_log);
}
