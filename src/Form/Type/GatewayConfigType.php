<?php
declare(strict_types=1);

namespace Bluemedia\SyliusBluepaymentPlugin\Form\Type;

use Bluemedia\SyliusBluepaymentPlugin\Validator\Constraints\FieldRequiredWith;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Range;

final class GatewayConfigType extends AbstractType
{
    private const LABEL = 'label';
    private const GROUPS = 'groups';
    private const SYLIUS = 'sylius';

    /** @var RepositoryInterface */
    private $currencyRepository;

    public function __construct(RepositoryInterface $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currencies = $this->currencyRepository->findAll();

        $builder->add(
            'test_mode',
            ChoiceType::class,
            [
                'choices' => [
                    'sylius.ui.yes_label' => true,
                    'sylius.ui.no_label' => false,
                ],
                self::LABEL => 'bluemedia_sylius_bluepayment_plugin.form.gateway_configuration.test_mode',
            ]
        );

        $i = 0;
        foreach ($currencies as $currency) {
            $code = $currency->getCode();

            $builder
                ->add(
                    "currency_$i",
                    HiddenType::class,
                    [
                        'data' => $code,
                    ]
                )
                ->add(
                    "service_id_$code",
                    NumberType::class,
                    [
                        self::LABEL => 'bluemedia_sylius_bluepayment_plugin.form.gateway_configuration.service_id',
                        'constraints' => [
                            new Range([
                                'min' => 1,
                                'max' => 999999,
                                self::GROUPS => [self::SYLIUS],
                            ]),
                            new FieldRequiredWith([
                                'type' => 'shared_key',
                                self::GROUPS => [self::SYLIUS],
                            ])
                        ],
                    ]
                )
                ->add(
                    "shared_key_$code",
                    TextType::class,
                    [
                        self::LABEL => 'bluemedia_sylius_bluepayment_plugin.form.gateway_configuration.shared_key',
                        'constraints' => [
                            new Length([
                                'min' => 30,
                                'max' => 50,
                                self::GROUPS => [self::SYLIUS],
                            ]),
                            new FieldRequiredWith([
                                'type' => 'service_id',
                                self::GROUPS => [self::SYLIUS],
                            ])
                        ],
                    ]
                );
            $i++;
        }
    }
}
