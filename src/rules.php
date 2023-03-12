<?php

if (!function_exists('ip_range')) {
    function ip_range(string $ipAddress, string $cidr): bool
    {
        list ($ip, $mask) = explode('/', $cidr);

        $ip = ip2long($ip);
        $mask = ~((1 << (32 - $mask)) - 1);

        return (ip2long($ipAddress) & $mask) == ($ip & $mask);
    }
}

if (!function_exists('in')) {
    function in(string $userRole, ...$roles): bool
    {
        return in_array($userRole, $roles);
    }
}
