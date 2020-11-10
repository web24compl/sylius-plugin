<?php
declare(strict_types=1);

namespace Bluemedia\SyliusBluepaymentPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use InvalidArgumentException;

final class FieldRequiredWith extends Constraint
{
    private const ALLOWED_TYPES = ['shared_key', 'service_id'];

    public $type;

    public function __construct($options = null)
    {
        if (is_array($options) && isset($options['type']) && !in_array($options['type'], self::ALLOWED_TYPES)) {
            throw new InvalidArgumentException(sprintf('Unsupported type provided: "%s".', $options['type']));
        }

        parent::__construct($options);
    }

    public function validatedBy(): string
    {
        return FieldRequiredWithValidator::class;
    }

    public function getTargets(): array
    {
        return [self::CLASS_CONSTRAINT];
    }
}
