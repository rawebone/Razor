# Razor HTTP

This part of the library provides a PHP5.3 and PSR-7 compatible API based off
of [phly/http](https://github.com/phly/http). PSR-7 is the base for handling
of HTTP messages in the core of the framework and it's use will allow for
mix and match of components if you need a different HTTP library which
supports the standard.

This layer exists only to support backwards compatibility with PHP5.3 as there
are currently no alternatives targeting this version; once the framework changes
PHP version support to PHP5.4 or greater this layer will be removed in favour
of alternatives (planned for Razor 3).

As such, and as should already be a best practice, do not target the concrete
implementations in this package but the PSR-7 interfaces. 
