<?php

namespace App\Twig;

use Symfony\Bridge\Twig\AppVariable as SymfonyAppVariable;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class AppVariable
{
    private $kernel;
    private $cache;
    private $inner;
    private $config;

    public function __construct(KernelInterface $kernel, CacheInterface $cache, SymfonyAppVariable $inner, $config)
    {
        $this->kernel = $kernel;
        $this->cache = $cache;
        $this->inner = $inner;
        $this->config = $config;
    }

    public function getToken()
    {
        return $this->inner->getToken();
    }

    public function getUser()
    {
        return $this->inner->getUser();
    }

    public function getRequest()
    {
        return $this->inner->getRequest();
    }

    public function getSession()
    {
        return $this->inner->getSession();
    }

    public function getEnvironment()
    {
        return $this->inner->getEnvironment();
    }

    public function getDebug()
    {
        return $this->inner->getDebug();
    }

    public function getFlashes($types = null)
    {
        return $this->inner->getFlashes($types);
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getVersion()
    {
        return $this->cache->get('git_version', function (ItemInterface $item) {
            $item->expiresAfter(60);

            $process = (new Process(['/usr/bin/git', 'describe', '--abbrev=12', '--always', '--dirty=+']))
                ->setWorkingDirectory($this->kernel->getProjectDir())
                ->setTimeout(1);

            $ret = $process->run();

            if ($ret !== 0) {
                return null;
            }

            return $process->getOutput();
        });
    }
}