<?php

namespace Yarsha\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;

class TestingCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('yarsha:command:test')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $request = new Request();
        $em = $this->getContainer()->get('doctrine')->getManager();

        $emailService = $this->getContainer()->get('yarsha.service.mailer');

        $emailService->sendEmail('test', 'this is test email', 'danepliz@gmail.com', 'info@kantipurjob.com', 'Kantipur Job' );


    }



}
