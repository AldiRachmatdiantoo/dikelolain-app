<?php

return [

    /*
     * Proxies in front of the site: comma-separated IPs, or * for whichever one is calling.
     * Behind a proxy every visitor shares its IP, and with it the limits on login and the contact form.
     * Leave empty on a server that faces the internet directly, or visitors can fake their IP.
     */
    'proxies' => env('TRUSTED_PROXIES'),

];
