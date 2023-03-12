<?php

function ip_range(string $ipAddress, string $range): bool
{
    list ($net, $mask) = explode('/', $range);

    $ip_net = ip2long($net);
    $ip_mask = ~((1 << (32 - $mask)) - 1);

    $ip_ip = ip2long($ipAddress);

    return ($ip_ip & $ip_mask) == ($ip_net & $ip_mask);
}

function in(string $userRole, ...$roles): bool
{
    return in_array($userRole, $roles);
}
