<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Form;

use LeanpubBookClub\Domain\Model\Common\TimeZone;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use LeanpubBookClub\Infrastructure\Symfony\Validation\LeanpubInvoiceIdConstraint;
use LeanpubBookClub\Infrastructure\Symfony\Validation\UniqueLeanpubInvoiceIdConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

final class RequestAccessForm extends AbstractType
{
    private TimeZone $authorTimeZone;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(TimeZone $authorTimeZone, UrlGeneratorInterface $urlGenerator)
    {
        $this->authorTimeZone = $authorTimeZone;
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
                    ]
                ]
            )
            ->add(
                'emailAddress',
                EmailType::class,
                [
                    'label' => 'request_access_form.email_address.label',
                    'help' => 'request_access_form.email_address.help',
                    'constraints' => [
                        new NotBlank(),
                        new Email()
                    ]
                ]
            )
            ->add(
                'timeZone',
                TimeZoneField::class,
                [
                    'data' => $this->authorTimeZone->asString(),
                    'help' => 'request_access_form.time_zone.help'
                ]
            )
            ->add(
                'request_access',
                SubmitType::class,
                [
                    'label' => 'request_access_form.request_access.label'
                ]
            )
            ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('action', $this->urlGenerator->generate('request_access'));
    }
}
