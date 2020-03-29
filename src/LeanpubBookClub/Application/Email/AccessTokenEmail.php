<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\Email;

use LeanpubBookClub\Domain\Model\Member\AccessToken;
use LeanpubBookClub\Domain\Model\Member\EmailAddress;

final class AccessTokenEmail implements Email
{
    private EmailAddress $recipient;

    private AccessToken $accessToken;

    public function __construct(EmailAddress $recipient, AccessToken $accessToken)
    {
        $this->recipient = $recipient;
        $this->accessToken = $accessToken;
    }

    public function recipient(): EmailAddress
    {
        return $this->recipient;
    }

    public function subject(): string
    {
        return 'access_token_email.subject';
    }

    public function template(): string
    {
        return 'email/access_token.html.twig';
    }

    public function templateVariables(): array
    {
        return [
            'accessToken' => $this->accessToken->asString()
        ];
    }
}
