<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Form;

use LeanpubBookClub\Infrastructure\Symfony\Validation\LeanpubInvoiceIdConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

final class LeanpubInvoiceIdField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'label' => 'form.leanpub_invoice_id.label',
                'help' => 'form.leanpub_invoice_id.help',
                'constraints' => [
                    new NotBlank(),
                    new LeanpubInvoiceIdConstraint()
                ]
            ]
        );
    }

    public function getParent()
    {
        return TextType::class;
    }
}
