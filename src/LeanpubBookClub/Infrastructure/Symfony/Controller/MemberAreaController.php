<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Controller;

use Assert\Assert;
use LeanpubBookClub\Application\ApplicationInterface;
use LeanpubBookClub\Application\AttendSession;
use LeanpubBookClub\Application\Members\Member;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/member-area")
 */
final class MemberAreaController extends AbstractController
{
    private ApplicationInterface $application;

    public function __construct(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * @Route("/login", name="login", methods={"GET"})
     */
    public function loginAction(): Response
    {
        return new RedirectResponse($this->generateUrl('index'));
    }

    /**
     * @Route("/", name="member_area_index", methods={"GET"})
     */
    public function indexAction(UserInterface $member): Response
    {
        Assert::that($member)->isInstanceOf(Member::class);
        /** @var Member $member */

        return $this->render(
            'member_area/index.html.twig',
            [
                'upcomingSessions' => $this->application->listUpcomingSessions($member->memberId()),
                'memberTimeZone' => $member->timeZone()
            ]
        );
    }

    /**
     * @Route("/attend-session", name="attend_session", methods={"POST"})
     */
    public function attendSession(Request $request, UserInterface $user): Response
    {
        $this->application->attendSession(
            new AttendSession($request->request->get('sessionId'), $user->getUsername())
        );

        return $this->redirectToRoute('member_area_index');
    }
}
