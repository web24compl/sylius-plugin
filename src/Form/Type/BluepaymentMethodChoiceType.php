<?php
declare(strict_types=1);

namespace Bluemedia\SyliusBluepaymentPlugin\Form\Type;

use Bluemedia\SyliusBluepaymentPlugin\Filter\PaymentListFilterInterface;
use Sylius\Bundle\PaymentBundle\Form\Type\PaymentMethodChoiceType;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class BluepaymentMethodChoiceType extends AbstractTypeExtension
{
    /** @var PaymentMethodsResolverInterface */
    private $paymentMethodsResolver;

    /** @var RepositoryInterface */
    private $paymentMethodRepository;

    /** @var PaymentListFilterInterface */
    private $paymentListFilter;

    private const SUBJECT = 'subject';

    public function __construct(
        PaymentMethodsResolverInterface $paymentMethodsResolver,
        RepositoryInterface $paymentMethodRepository,
        PaymentListFilterInterface $paymentListFilter
    ) {
        $this->paymentMethodsResolver = $paymentMethodsResolver;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->paymentListFilter = $paymentListFilter;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choices' => function (Options $options) {
                    if (isset($options[self::SUBJECT])) {
                        return $this->paymentListFilter->filter(
                            $this->paymentMethodsResolver->getSupportedMethods($options[self::SUBJECT])
                        );
                    }

                    return $this->paymentListFilter->filter(
                        $this->paymentMethodRepository->findAll()
                    );
                },
                'choice_value' => 'code',
                'choice_label' => 'name',
                'choice_translation_domain' => false,
            ])
            ->setDefined([
                self::SUBJECT,
            ])
            ->setAllowedTypes(self::SUBJECT, PaymentInterface::class)
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        return [PaymentMethodChoiceType::class];
    }
}
