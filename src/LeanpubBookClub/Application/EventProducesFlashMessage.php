<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application;

interface EventProducesFlashMessage
{
    public const PRIMARY = 'primary';

    public const SECONDARY = 'secondary';

    public const SUCCESS = 'success';

    public const DANGER = 'danger';

    public const WARNING = 'warning';

    public const INFO = 'info';

    public const LIGHT = 'light';

    public const DARK = 'dark';

    /**
     * @return string Should be one of the interface constants to make it work with Bootstrap
     */
    public function flashType(): string;

    public function flashTranslatableMessage(): string;

    public function flashTranslationVariables(): array;
}
