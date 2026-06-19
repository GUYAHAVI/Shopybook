<?php

/**
 * Forward the request to Laravel's front controller when the web root
 * is the project directory (not public/). Remove any cPanel "coming soon"
 * index.php and use this file instead.
 */
require __DIR__.'/public/index.php';
