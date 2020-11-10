<?php
declare(strict_types=1);

namespace Bluemedia\SyliusBluepaymentPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class FieldRequiredWithValidator extends ConstraintValidator
{
    public $message = 'bluemedia_sylius_bluepayment_plugin.form.gateway_configuration.required_with';

    public function validate($value, Constraint $constraint): void
    {
        $fields = $this->context->getRoot()->getData()->getGatewayConfig()->getConfig();

        $parts = explode('_', $this->context->getObject()->getName());
        $currency = array_pop($parts);
        $service_id = $fields[sprintf('%s_%s', $constraint->type, $currency)];

        if ($this->context->getValue() === null && $service_id !== null) {
            $this->context
                ->buildViolation($this->message)
                ->addViolation();
        }
    }
}
