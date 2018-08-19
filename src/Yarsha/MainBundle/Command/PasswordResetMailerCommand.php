<?php

namespace Yarsha\MainBundle\Command;

use FOS\UserBundle\Event\GetResponseNullableUserEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Yarsha\JobSeekerBundle\Controller\UserController;
use Yarsha\JobSeekerBundle\Entity\User;

class PasswordResetMailerCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('yarsha:users:passwordresetmailer')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $request = new Request();
        $em = $this->getContainer()->get('doctrine')->getManager();

        $users = $em->getRepository(User::class)->findBy([
            'enabled' => 1,
            'deleted' => 0
        ]);

        foreach ($users as $user) {
            $request->request->set('username', $user->getEmail());
            try {
                $this->sendSeekerEmail($user, $request);
            } catch (\Exception $e) {
                $output->writeln($e->getMessage());
                continue;
            }
            $output->writeln("Password reset sent to " . $user->getEmail());
        }
    }

    public function sendSeekerEmail(User $seeker, Request $request)
    {
        $username = $seeker->getContactEmail();

        /** @var $user UserInterface */
        $user = $this->getContainer()->get('yarsha_job_seeker.user_manager')->findUserByUsernameOrEmail($username);

        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->getContainer()->get('event_dispatcher');

        /* Dispatch init event */
        $event = new GetResponseNullableUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }


        if ($user != null) {
            $event = new GetResponseUserEvent($user, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_REQUEST, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }

            if (null === $user->getConfirmationToken() or $user->getConfirmationToken() == '') {
                /** @var $tokenGenerator TokenGeneratorInterface */
                $tokenGenerator = $this->getContainer()->get('fos_user.util.token_generator');
                $token = $tokenGenerator->generateToken();
                $user->setConfirmationToken($token);
            } else {
                $token = $user->getConfirmationToken();
            }

            /* Dispatch confirm event */
            $event = new GetResponseUserEvent($user, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_CONFIRM, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }

            $to = $username;
            $fullname = $user->getfirstName();

            $url = $this->getContainer()
                ->get('router')
                ->generate('yarsha_job_seeker_password-resetting',
                    ['token' => $token],
                    UrlGeneratorInterface::ABSOLUTE_URL);
            $data['message'] = "We have updated our site. Please reset your password to continue. Sorry for our inconvenience.";
            $subject = 'Password Resetting';
            $body = $this->getContainer()->get('templating')->render(
                'Emails/password_resetting.html.twig',
                [
                    'name' => $fullname,
                    'token' => $token,
                    'url' => $url
                ]
            );
            $from = 'noreply@kantipurjob.com';
            $message = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom('info@yarshastudio.com')
                ->setTo($to)
                ->setBody(
                    $body,
                    'text/html'
                );
//            $this->getContainer()->get('yarsha.service.mailer')->sendEmail($subject, $body, $to);
            $this->getContainer()->get('mailer')->send($message);

            $user->setPasswordRequestedAt(new \DateTime());
            $this->getContainer()->get('yarsha_job_seeker.user_manager')->updateUser($user);

            $event = new GetResponseUserEvent($user, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_COMPLETED, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }
        }
    }

}
