<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function getCacheDir(): string
    {
        return '/tmp/symfony/cache'; // Use a temporary directory
    }

    public function getLogDir(): string
    {
        return '/tmp/symfony/logs'; // Optional: Redirect logs
    }
}
