<?php

namespace RuBAC;

interface RequestInterface
{
    public function getIpAddress(): string;

    public function getPath(): string;
}
