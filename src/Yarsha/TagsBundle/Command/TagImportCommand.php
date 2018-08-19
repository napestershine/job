<?php

namespace Yarsha\TagsBundle\Command;


use Yarsha\ArticleBundle\Entity\Article;
use Symfony\Component\Console\Input\InputInterface;
//use Yarsha\ContentBundle\Entity\Post;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class TagImportCommand extends ContainerAwareCommand
{

    public function configure()
    {
        $this->setName('educationsansar:tags:update')
            ->setDescription('Import Tags From previous posts');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $posts = $em->getRepository(Post::class)->findAll();
        $events = $em->getRepository(Event::class)->findAll();

        $postCount = count($posts);
        $eventCount = count($events);

        $output->writeln('------------------- UPDATING TAGS ---------------- ');
        $output->writeln("Total Posts : ".$postCount);
        $output->writeln("Total Events : ".$eventCount);

        $output->writeln('-------------------- IMPORTING POSTS TAGS ---------------- ');

        $failedProcess['posts'] = [];
        $failedProcess['events'] = [];

        foreach($posts as $p)
        {
            try{
                $this->process($output, $p);
            }catch(\Exception $e)
            {
                $failedProcess['posts'][] = $p->getId();
            }
        }

        $output->writeln('-------------------- IMPORTING EVENTS TAGS ---------------- ');

        foreach($events as $e)
        {
            try{
                $this->process($output, $e);
            }catch(\Exception $e)
            {
                $failedProcess['events'][] = $e->getId();
            }
        }

        $output->writeln("Process Finished");
        $postMessage = count($failedProcess['posts']) > 0 ? "Ids (".implode(',',$failedProcess['posts']).") failed." : "";
        $output->writeln("Post Success. ".$postMessage);

        $eventMessage = count($failedProcess['events']) > 0 ? "Ids (".implode(',',$failedProcess['events']).") failed." : "";
        $output->writeln("Event Success. ".$eventMessage);

    }

    public function process(OutputInterface $output, $entity)
    {
        try{
            $tags = $entity->getTags();

            if( $tags != "" )
            {
                $output->writeln($tags);
                $this->getContainer()->get('education_sansar.service.tags')->updateTags($entity, $tags);
                $output->writeln(":::: Updated");
            }
        }catch(\Exception $e)
        {
            throw $e;
        }

    }

}
