<?php

namespace Yarsha\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Yarsha\ArticleBundle\Entity\Article;
use Yarsha\JobsBundle\Entity\Job;
Use Yarsha\AdminBundle\Entity\Banner;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Yarsha\JobSeekerBundle\Entity\User;
use Yarsha\MainBundle\Entity\Emails;

/**
 * Class HomeController
 * @package Yarsha\FrontendBundle\Controller
 *
 * @Route("/")
 */
class HomeController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/", name="yarsha_frontend_homepage")
     */
    public function indexAction(Request $request)
    {
        $data = [];
        $em = $this->get('doctrine.orm.entity_manager');

        $currentDate = new \DateTime();

        $jobs = $this->getDoctrine()->getRepository(Job::class)->getJobs($currentDate->format('Y-m-d'));
        $data['jobs'] = $jobs;

        $banner = $em->getRepository(Banner::class)->findOneBy(
            [
                'isFeatured' => true,
                'deleted' => false
            ]
        );
        $data['banner'] = $banner;

        return $this->render('YarshaFrontendBundle:Home:index.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/view/{id}/employer", name="yarsha_frontend_employer_detail")
     */
    public function viewEmployerDetailAction(Request $request)
    {
        $id = $request->get('id');
        $employerService = $this->get('yarsha.service.employer');
        $employer = $employerService->getEmployerById($id);
        $organization = $employer->getOrganization();
        if (!$employer) {
            throw new NotFoundHttpException();
        }

        $organization->increaseVisitCount();
        $em = $this->getDoctrine()->getManager();
        $em->persist($organization);
        try {
            $em->flush();
        } catch (\Exception $e) {
            $data['errorMessage'] = $e->getMessage();
        }

        $banners = $employerService->getActiveBanner($employer->getId());
        $total_follower = $employerService->getTotalFollower($employer->getId());
        $follower = $employerService->getEmployerById($employer->getId());
        $totalfollower = $follower->getFollowers();
        $data['employer'] = $employer;
        $data['banners'] = $banners;
        $data['follower'] = count($total_follower);
        $data['followers'] = $totalfollower;


        return $this->render('YarshaFrontendBundle:employer:employerdetail.html.twig', $data);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/organization/{slug}", name="yarsha_frontend_employer_detail_by_slug")
     */
    public function viewEmployerDetailBySlugAction(Request $request)
    {
        $slug = $request->get('slug');
        $employerService = $this->get('yarsha.service.employer');
        $organization = $employerService->getOrganizationBySlug($slug);

        if (!$organization) {
            throw new NotFoundHttpException;
        }

        try {
            $organization->increaseVisitCount();
            $em = $this->getDoctrine()->getManager();
            $em->persist($organization);
            $em->flush();
        } catch (\Exception $e) {
        }


        $data['organization'] = $organization;

        return $this->render('YarshaFrontendBundle:employer:employerdetail.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/article/{category}/{slug}", name="yarsha_frontend_article_detail")
     */
    public function viewArticleDetailAction(Request $request)
    {

        $category = $request->get('category');
        $slug = $request->get('slug');

        $categoryLinksDesc = array_flip(Article::$articlesCategoryDescForUrl);

        if (!$slug or !array_key_exists($category, $categoryLinksDesc)) {
            throw new NotFoundHttpException();
        }

        $articleService = $this->get('yarsha.service.article');
        $article = $articleService->getArticleBySlug($slug);

        if (!$article) {
            throw new NotFoundHttpException();
        }

        try {
            $article->increaseHits();
            $articleService->getEntityManager()->persist($article);
            $articleService->getEntityManager()->flush();
        } catch (\Exception $e) {
        }

        $data['contentTitle'] = Article::$articleCategories[$categoryLinksDesc[$category]];
        $data['article'] = $article;
        $data['category'] = $category;

        return $this->render('@YarshaFrontend/article_detail.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/article/{category}", name="yarsha_frontend_article_list")
     */
    public function listArticleAction(Request $request)
    {
        $category = $request->get('category');
        $categoryLinksDesc = array_flip(Article::$articlesCategoryDescForUrl);

        if (!array_key_exists($category, $categoryLinksDesc)) {
            throw new NotFoundHttpException();
        }

        $filters = ['category' => $categoryLinksDesc[$category]];
        $data['category'] = $category;
        $data['title'] = Article::$articleCategories[$filters['category']];

        $data['articles'] = $this->get('yarsha.service.article')->getPaginatedArticleList($filters);

        return $this->render('@YarshaFrontend/News/news_list.html.twig', $data);

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/employers", name="yarsha_frontend_employers_list")
     */
    public function listEmployersAction(Request $request)
    {

        return $this->render('@YarshaFrontend/employer/employers_lists.html.twig');

    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/unsubscribe/job-alert/{email}", name="yarsha_frontend_unsubscribe_from_mass")
     */
    public function unSubscribeFromMassEmailAction(Request $request)
    {
        $email = $request->get('email');

        $em = $this->get('doctrine.orm.default_entity_manager');

        try{
            $emailFromMass = $em->getRepository(Emails::class)->findOneBy([
                'email' => $email
            ]);

            if($emailFromMass)
            {
                $em->remove($emailFromMass);

                $em->flush();
            }
        }catch(\Exception $e){ }

        $data['email'] = $email;

        return $this->render('@YarshaFrontend/Subscription/unsubscribe_from_mass.html.twig', $data);

    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/cv/{slug}", name="yarsha_frontend_seeker_cv_search")
     */
    public function viewSeekerCVByUsernameAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $slug = $request->get('slug');
        $seekerService = $this->get('yarsha.service.job_seeker');
        $employerService = $this->get('yarsha.service.organization');
        $seeker = $seekerService->getSeekerByUsername($slug);
        $jobSeekers = $seekerService->getOtherJobseekers($slug);

        $profileVisit = $seeker->getProfileVisits();
            $pV = $profileVisit + 1;
            $seeker->setProfileVisits($pV);
            $em->persist($seeker);
            $em->flush();

        $superEmployers = $employerService->getSuperEmployers(5);

        if (!$seeker) {
            throw new NotFoundHttpException;
        }
        $data['slug'] = $slug;
        $data['seeker'] = $seeker;
        $data['jobSeekers']= $jobSeekers;
        $data['superEmployers'] = $superEmployers;
        return $this->render('YarshaFrontendBundle:seeker:cv_details.html.twig', $data);
    }

}
