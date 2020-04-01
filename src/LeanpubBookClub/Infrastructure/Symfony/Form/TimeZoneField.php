<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;

final class TimeZoneField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'label' => 'time_zone_field.label',
                'constraints' => [
                    new NotBlank(),
                    new Choice(array_values($this->availableTimeZones()))
                ],
                'choices' => $this->availableTimeZones()
            ]
        );
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

    public function getParent()
    {
        return ChoiceType::class;
    }
}
