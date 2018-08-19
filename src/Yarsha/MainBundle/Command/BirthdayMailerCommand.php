<?php

namespace Yarsha\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Yarsha\JobSeekerBundle\Entity\User;

class BirthdayMailerCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('yarsha:users:birthdaymailer')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $request = new Request();
        $em = $this->getContainer()->get('doctrine')->getManager();

//        $userService = $this->getContainer()->get('yarsha.service.job_seeker');
//        $users = $userService->getBirthdaySeekers();

        $users = $em->getRepository(User::class)->findBy([
            'enabled' => 1,
            'deleted' => 0
        ]);

        foreach ($users as $user) {

            if ($user->getDob() && $user->getContactEmail()) {

                $date_of_birth = $user->getDob()->format('Y-m-d');
                $dob = date('m-d', strtotime($date_of_birth));
                $today = date('m-d');

                try {
                    if ($dob == $today) {

                        $this->sendBirthdayEmail($user, $request);
                        $output->writeln("Birthday message sent to " . $user->getEmail());
                    } else {
                        $output->writeln("Birthday message not sent to " . $user->getContactEmail());
                    }
                } catch (\Exception $e) {
                    $output->writeln($e->getMessage());
                    continue;
                }
            }

        }


    }


    public function sendBirthdayEmail(User $seeker, Request $request)
    {
        $to = $seeker->getContactEmail();;
        $fullname = $seeker->getfirstName();
        $data['message'] = "We have updated our site. Please reset your password to continue. Sorry for our inconvenience.";
        $subject = 'Birthday Message';
        $body = $this->getContainer()->get('templating')->render(
            '@YarshaMain/EmailTemplates/birthday_wish.html.twig',
            [
                'seeker' => $seeker,

            ]
        );
        $from = 'kantipurjob@gmail.com';
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom('info@yarshastudio.com')
            ->setTo($to)
            ->setBody(
                $body,
                'text/html'
            );

        $this->getContainer()->get('mailer')->send($message);


    }
}
