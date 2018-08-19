<?php

namespace Yarsha\MainBundle\Command;

use Yarsha\JobsBundle\Entity\Job;
use Yarsha\MainBundle\Entity\Emails;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Yarsha\JobSeekerBundle\Entity\User as JobSeeker;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class JobAlertMailCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('yarsha:mail:job-alert')
            ->setDescription('Send recent related jobs to those seekers who choose to receive job alert email.')
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

        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        $jobSeekers = $em->getRepository(JobSeeker::class)->getJobSeekersWithJobAlertEmailActivated();

        $count = count($jobSeekers);
        if ($count) {
            $output->writeln("<info> $count are accepting job alert emails.</info>");

            foreach ($jobSeekers as $jobSeeker) {
                $jobs = $em->getRepository(Job::class)->getRelatedJobsByJobSeeker($jobSeeker['seekerId']);
            }

        } else {
            $output->writeln('<info>None of the job seeker has activated job alert email receiving.</info>');
        }

//        $jobService = $this->getContainer()->get('yarsha.service.job');
//        $jobs = $jobService->getRecentJob();
//
//        if ($jobs) {
//            if ($users) {
//                foreach ($users as $user) {
//
//                    try {
//                        if ($user->getEmail()) {
//                            $this->sendJobAlertEmail($user, $request, $jobs);
//                            $output->writeln("Job alert message sent to " . $user->getEmail());
//                        } else {
//                            $output->writeln("Email does not exits.");
//                        }
//
//                    } catch
//                    (\Exception $e) {
//                        $output->writeln($e->getMessage());
//                        continue;
//                    }
//
//                }
//            } else {
//                $output->writeln("No Users found.");
//            }
//        } else {
//
//            $output->writeln("No jobs found.");
//        }
    }

    public function sendJobAlertEmail(
        Emails $user,
        Request $request,
        $jobs
    ) {

        $to = $user->getEmail();
        $subject = 'Job Alert Message';
        $body = $this->getContainer()->get('templating')->render(
            '@YarshaMain/Emails/job_alert.html.twig',
            [
                'jobs' => $jobs,
                'name' => $user->getName() ? $user->getName() : $to,
                'email' => $to
            ]
        );
        $from = 'noreply@kantipurjob.com';
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody(
                $body,
                'text/html'
            );

        $this->getContainer()->get('mailer')->send($message);


    }
}
