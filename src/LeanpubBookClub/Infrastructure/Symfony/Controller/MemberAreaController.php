<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Controller;

use Assert\Assert;
use LeanpubBookClub\Application\ApplicationInterface;
use LeanpubBookClub\Application\AttendSession;
use LeanpubBookClub\Application\CancelAttendance;
use LeanpubBookClub\Application\FlashType;
use LeanpubBookClub\Application\Members\Member;
use LeanpubBookClub\Application\SessionCall\CouldNotGetCallUrl;
use LeanpubBookClub\Application\UpdateTimeZone;
use LeanpubBookClub\Infrastructure\Symfony\Form\UpdateTimeZoneForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/member-area")
 */
final class MemberAreaController extends AbstractController
{
    private ApplicationInterface $application;

    private TranslatorInterface $translator;

    public function __construct(ApplicationInterface $application, TranslatorInterface $translator)
    {
        $this->application = $application;
        $this->translator = $translator;
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
                'upcomingSessions' => $this->application->listUpcomingSessions($member->memberId()->asString()),
                'memberTimeZone' => $member->timeZone(),
                'updateTimeZoneForm' => $this->createUpdateTimeZoneForm($member)->createView()
            ]
        );
    }

    /**
     * @Route("/update-time-zone", name="update_time_zone", methods={"POST"})
     */
    public function updateTimeZoneAction(UserInterface $member, Request $request): Response
    {
        Assert::that($member)->isInstanceOf(Member::class);
        /** @var Member $member */

        $form = $this->createUpdateTimeZoneForm($member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $this->application->updateTimeZone(
                new UpdateTimeZone($member->getUsername(), $formData['timeZone'])
            );

            return $this->redirectToRoute('member_area_index');
        }

        // The user shouldn't need any assistance, so we don't render the form again to show form errors

        return $this->redirectToRoute('member_area_index');
    }

    /**
     * @Route("/attend-session", name="attend_session", methods={"POST"})
     */
    public function attendSessionAction(Request $request, UserInterface $user): Response
    {
        $this->application->attendSession(
            new AttendSession($request->request->get('sessionId'), $user->getUsername())
        );

        return $this->redirectToRoute('member_area_index');
    }

    /**
     * @Route("/cancel-attendance", name="cancel_attendance", methods={"POST"})
     */
    public function cancelAttendanceAction(Request $request, UserInterface $user): Response
    {
        $this->application->cancelAttendance(
            new CancelAttendance($request->request->get('sessionId'), $user->getUsername())
        );

        return $this->redirectToRoute('member_area_index');
    }

    /**
     * @Route("/redirect-to-video-call/{sessionId}", name="redirect_to_video_call", methods={"GET"})
     */
    public function redirectToVideoCall(string $sessionId): Response
    {
        try {
            $videoCallUrl = $this->application->getCallUrlForSession($sessionId);

            return $this->redirect($videoCallUrl);
        } catch (CouldNotGetCallUrl $exception) {
            $this->addFlash(FlashType::WARNING, $this->translator->trans('session_video_call_url_not_available.flash_message'));

            return $this->redirectToRoute('member_area_index');
        }
    }

    private function createUpdateTimeZoneForm(Member $member): FormInterface
    {
        return $this->createForm(
            UpdateTimeZoneForm::class,
            [
                'timeZone' => $member->timeZone()
            ]
        );
    }
}
