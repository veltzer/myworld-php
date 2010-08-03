<?php

/* This php script shows all information regarding php support on your
 * server. This is usefull in debugging or configuring php support on
 * your server.
 */

// Show all information, defaults to INFO_ALL
phpinfo();

// Show just the module information.
// phpinfo(8) yields identical results.
phpinfo(INFO_MODULES);

?>
