<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Form;

use DateTimeZone;
use LeanpubBookClub\Domain\Model\Common\TimeZone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

final class RequestAccessForm extends AbstractType
{
    private TimeZone $authorTimeZone;

    public function __construct(TimeZone $authorTimeZone)
    {
        $this->authorTimeZone = $authorTimeZone;
    }

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
                'timeZone',
                ChoiceType::class,
                [
                    'label' => 'request_access_form.time_zone.label',
                    'help' => 'request_access_form.time_zone.help',
                    'constraints' => [
                        new NotBlank(),
                        new Choice(array_values($this->availableTimeZones()))
                    ],
                    'choices' => $this->availableTimeZones(),
                    'data' => $this->authorTimeZone->asString(),
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

    /**
     * @return array<string,string>
     */
    private function availableTimeZones(): array
    {
        $timeZones = [];

        foreach (\DateTimeZone::listIdentifiers(\DateTimeZone::ALL) as $timeZone) {
            $timeZones[$timeZone] = $timeZone;
        }

        return $timeZones;
    }
}
