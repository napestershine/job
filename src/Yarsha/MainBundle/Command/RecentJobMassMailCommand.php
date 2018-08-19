<?php

namespace Yarsha\MainBundle\Command;

use Faker\Provider\ka_GE\DateTime;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Yarsha\JobsBundle\Entity\Job;
use Yarsha\MainBundle\Entity\Emails;
use Yarsha\JobSeekerBundle\Entity\User;

class RecentJobMassMailCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('yarsha:mail-job:mass-email')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $host = $this->getContainer()->getParameter('site.host');
        $scheme = $this->getContainer()->getParameter('site.protocol');

        $request = new Request();

        $context = $this->getContainer()->get('router')->getContext();
        $context->setHost($host);
        $context->setScheme($scheme);


        $em = $this->getContainer()->get('doctrine')->getManager();

        $jobSeekers = $em->getRepository(User::class)->getAllActiveUsersForMass();
        $emails = $em->getRepository(Emails::class)->getAllActiveEmails();

        $users = array_merge($jobSeekers, $emails);

        $jobService = $this->getContainer()->get('yarsha.service.job');
        $jobs = $jobService->getRecentJob();

        if ($jobs) {
            if ($users) {
                foreach ($users as $user) {

                    try {
                        if (array_key_exists('enabled', $user)) {
                            $massUser = [
                                'email' => $user['contactEmail'],
                                'name' => $user['firstName']
                            ];

                        } else {
                            $massUser = [
                                'email' => $user['email'],
                                'name' => $user['name']
                            ];

                        }
                        $this->sendJobAlertEmail($massUser, $request, $jobs);
                        $output->writeln("Job alert message sent to " . $massUser['email']);

                    } catch
                    (\Exception $e) {
                        $output->writeln($e->getMessage());
                        continue;
                    }

                }
            } else {
                $output->writeln("No Users found.");
            }
        } else {

            $output->writeln("No jobs found.");
        }
    }

    public function sendJobAlertEmail(
        $massUser,
        Request $request,
        $jobs
    ) {

        $to = $massUser['email'];
        $name = $massUser['name'];
        $subject = 'Job Alert Message';
        $body = $this->getContainer()->get('templating')->render(
            '@YarshaMain/Emails/job_alert.html.twig',
            [
                'jobs' => $jobs,
                'name' => $name ? $name : $to,
                'email' => $to
            ]
        );
        $from = 'noreply@kantipurjob.com';
        $fromName = 'Kantipur Job';
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from, $fromName)
            ->setTo($to)
            ->setBody(
                $body,
                'text/html'
            );

        $this->getContainer()->get('mailer')->send($message);


    }
}
