<?php

declare(strict_types=1);

namespace App\Exception;

/**
 * Exceptions metier previsibles (affichables en flash), pas des 500.
 */
class DomainException extends \RuntimeException
{
}
