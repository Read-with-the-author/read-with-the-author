<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Form;

use LeanpubBookClub\Infrastructure\Symfony\Validation\ExistingLeanpubInvoiceIdConstraint;
use LeanpubBookClub\Infrastructure\Symfony\Validation\LeanpubInvoiceIdConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

final class RequestAccessTokenForm extends AbstractType
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'leanpubInvoiceId',
                LeanpubInvoiceIdField::class,
                [
                    'constraints' => [
                        new NotBlank(),
                        new LeanpubInvoiceIdConstraint(),
                        new ExistingLeanpubInvoiceIdConstraint()
                    ]
                ]
            )
            ->add(
                'request_access_token',
                SubmitType::class,
                [
                    'label' => 'request_access_token_form.request_access_token.label'
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('action', $this->urlGenerator->generate('request_access_token'));
    }
}
