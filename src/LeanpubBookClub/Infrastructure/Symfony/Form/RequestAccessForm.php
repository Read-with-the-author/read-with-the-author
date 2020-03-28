<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

final class RequestAccessForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'leanpubInvoiceId',
                LeanpubInvoiceIdField::class
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
                'request_access',
                SubmitType::class,
                [
                    'label' => 'request_access_form.request_access.label'
                ]
            )
            ->getForm();
    }
}
