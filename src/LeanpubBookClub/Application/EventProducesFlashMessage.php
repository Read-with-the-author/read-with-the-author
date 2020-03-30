<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application;

interface EventProducesFlashMessage
{
    /**
     * @return string Should be one of the FlashType interface constants to make it work with Bootstrap
     */
    public function flashType(): string;

    public function flashTranslatableMessage(): string;

    /**
     * @return array<string,mixed>
     */
    public function flashTranslationVariables(): array;
}
