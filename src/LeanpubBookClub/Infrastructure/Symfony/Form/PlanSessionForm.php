<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Form;

use DateTimeImmutable;
use LeanpubBookClub\Domain\Model\Common\TimeZone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

final class PlanSessionForm extends AbstractType
{
    private TimeZone $authorTimeZone;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(TimeZone $authorTimeZone, UrlGeneratorInterface $urlGenerator)
    {
        $this->authorTimeZone = $authorTimeZone;
        $this->urlGenerator = $urlGenerator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'date',
                DateTimeType::class,
                [
                    'constraints' => new NotBlank(),
                    'data' => new DateTimeImmutable('now', $this->authorTimeZone->asPhpDateTimeZone()),
                    'model_timezone' => $this->authorTimeZone->asString(),
                    'view_timezone' => $this->authorTimeZone->asString()
                ]
            )
            ->add(
                'description',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank()
                    ]
                ]
            )
            ->add(
                'maximumNumberOfParticipants',
                IntegerType::class,
                [
                    'constraints' => [
                        new Range(['min' => 1]),
                        new NotBlank()
                    ]
                ]
            )
            ->add('planSession', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('action', $this->urlGenerator->generate('plan_session'));
    }
}
