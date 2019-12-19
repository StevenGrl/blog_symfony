<?php

namespace App\Services;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class AntiSpam
{
    private $forbiddenStrings;
    private $logger;
    private $requestStack;

    public function __construct(array $forbiddenStrings, LoggerInterface $logger, RequestStack $requestStack)
    {
        $this->forbiddenStrings = $forbiddenStrings;
        $this->logger = $logger;
        $this->requestStack = $requestStack;
    }

    public function isSpam(string $stringToTest): bool
    {
        $isSpam = false;
        foreach ($this->forbiddenStrings as $string) {
            if (strstr($stringToTest, $string)) {
                $isSpam = true;
                $this->logger->info("La chaîne de caractère $string a été trouvée, c'est donc un spam ! 
                IP de l'utilisateur : " . $this->requestStack->getCurrentRequest()->getClientIp());
            }
        }

        return $isSpam;
    }
}