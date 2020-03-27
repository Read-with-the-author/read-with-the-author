<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class RequestAccessForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'emailAddress',
                EmailType::class,
                [
                    'label' => 'request_access_form.email_address.label',
                    'help' => 'request_access_form.email_address.help'
                ]
            )
            ->add(
                'leanpubInvoiceId',
                TextType::class,
                [
                    'label' => 'request_access_form.leanpub_invoice_id.label',
                    'help' => 'request_access_form.leanpub_invoice_id.help'
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
