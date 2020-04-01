<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class UpdateTimeZoneForm extends AbstractType
{
    private UrlGeneratorInterface $generator;

    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('timeZone', TimeZoneField::class)
            ->add('update', SubmitType::class, ['label' => 'update_time_zone_button.label']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('action', $this->generator->generate('update_time_zone'));
    }
}
