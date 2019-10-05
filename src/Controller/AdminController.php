<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Kampen;
use DateTime;
use DateTimeInterface;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(Request $request, PaginatorInterface $paginator) {
        // Retrieve the entity manager of Doctrine
        $em = $this->getDoctrine()->getManager();

        // Get some repository of data, in our case we have an Kampen entity
        $kampenRepository = $em->getRepository(Kampen::class);

        // Find all the data on the Kampen table, filter your query as you need
        $allKampenQuery = $kampenRepository->createQueryBuilder('kamp')
            ->select('kamp')
            ->orderBy('kamp.created_at', 'DESC')
            ->getQuery();
        // Paginate the results of the query
        $records = $paginator->paginate(
        // Doctrine Query, not results
            $allKampenQuery,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            5
        );
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController@index',
            'title' => 'Dashboard - Alle Kampen',
            'records' => $records,
        ]);
    }

    /**
     * @Route("/admin/create", name="admin.create")
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function create(Request $request)
    {
        // set default timezone
        date_default_timezone_set('Europe/Brussels');
        $task = new Kampen();

        // set the created and updated fields of the new camp
        $task
            ->setLikes(0)
            ->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime());

        // generate a form with all the values of the db table Kampen
        $form = $this->createFormBuilder($task)
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'html5' => false
            ])
            ->add('quote', TextType::class,[
                'required' => false
            ])
            ->add('image', TextType::class,[
                'required' => false
            ])
            ->add('promotion', CheckboxType::class,[
                'required' => false
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Maak kamp!'
            ])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $task = $form->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('admin');
        }
        return $this->render('admin/create.html.twig', [
            'form' => $form->createView(),
            'title' => 'Maak nieuw kamp'
        ]);
    }

    /**
     * @Route("/admin/create/{id}")
     * @param $id
     * @param $request
     * @return Response
     * @throws Exception
     */
    public function update(string $id, Request $request) {
        $record = $this
            ->getDoctrine()
            ->getRepository('App:Kampen')
            ->findOneBy(
                array('id' => $id)
            );
        date_default_timezone_set('Europe/Brussels');
        if($record) {
            $record->setUpdatedAt(new DateTime());
            $form = $this->createFormBuilder($record)
                ->add('title', TextType::class)
                ->add('description', TextareaType::class)
                ->add('date', DateType::class, [
                    'widget' => 'single_text',
                    'html5' => false
                ])
                ->add('quote', TextType::class,[
                    'required' => false
                ])
                ->add('image', TextType::class,[
                    'required' => false
                ])
                ->add('promotion', CheckboxType::class,[
                    'required' => false
                ])
                ->add('save', SubmitType::class, [
                    'label' => 'Maak kamp!'
                ])
                ->getForm();
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                // $form->getData() holds the submitted values
                // but, the original `$task` variable has also been updated
                $record = $form->getData();

                // ... perform some action, such as saving the task to the database
                // for example, if Task is a Doctrine entity, save it!
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($record);
                $entityManager->flush();

                return $this->redirectToRoute('admin');
            }
            return $this->render('admin/create.html.twig', [
                'controller_name' => 'AdminController@update',
                'title' => 'Update Kamp: '. $record->getTitle(),
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('admin/notFound.html.twig', [
                'controller_name' => 'AdminController@update',
                'action' => 'update'
            ]);
        }
    }

    /**
     * calculates difference of 2 dates and gives the slug for the created date and time difference
     * @param DateTimeInterface $date1
     * @param DateTime $date2
     * @return array
     */
    public function dateDifference(DateTimeInterface $date1, DateTime $date2){
        $result = [
            'timeAgo' => 0,
            'created_at_slug' => '',
        ];
        $differenceDays = $date1->diff($date2);
        if($differenceDays->format('%y') > 0){
            $result['timeAgo'] = $differenceDays->format('%y');
            if($differenceDays->format('%y') == 1){
                $result['created_at_slug'] = 'year ago';
            } else {
                $result['created_at_slug'] = 'years ago';
            }
        } else if($differenceDays->format('%m') > 0){
            $result['timeAgo'] = $differenceDays->format('%m');
            if($differenceDays->format('%m') == 1){
                $result['created_at_slug'] = 'month ago';
            } else {
                $result['created_at_slug'] = 'months ago';
            }
        } else if($differenceDays->format('%d') > 0) {
            $result['timeAgo'] = $differenceDays->format('%d');
            if ($differenceDays->format('%d') == 1) {
                $result['created_at_slug'] = 'day ago';
            } else {
                $result['created_at_slug'] = 'days ago';
            }
        } else if($differenceDays->format('%h') > 0) {
            $result['timeAgo'] = $differenceDays->format('%h');
            if ($differenceDays->format('%h') == 1) {
                $result['created_at_slug'] = 'hour ago';
            } else {
                $result['created_at_slug'] = 'hours ago';
            }
        } else if($differenceDays->format('%i') > 0) {
            $result['timeAgo'] = $differenceDays->format('%i');
            if ($differenceDays->format('%i') == 1) {
                $result['created_at_slug'] = 'minute ago';
            } else {
                $result['created_at_slug'] = 'minutes ago';
            }
        } else {
            $result['timeAgo'] = $differenceDays->format('%s');
            if ($differenceDays->format('%s') == 1) {
                $result['created_at_slug'] = 'second ago';
            } else {
                $result['created_at_slug'] = 'seconds ago';
            }
        }
        return $result;
    }

    /**
     * @Route("/admin/kamp/{id}")
     * @param $id
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     * @throws Exception
     */
    public function detail($id, Request $request, PaginatorInterface $paginator) {
        date_default_timezone_set('Europe/Brussels');
        $record = $this
            ->getDoctrine()
            ->getRepository('App:Kampen')
            ->findOneBy(
                array('id' => $id)
            );
        // Retrieve the entity manager of Doctrine
        $em = $this->getDoctrine()->getManager();

        // Get some repository of data, in our case we have an Kampen entity
        $commentsRepository = $em->getRepository(Comments::class);

        // Find all the data on the Kampen table, filter your query as you need
        $allCommentsQuery = $commentsRepository->createQueryBuilder('comment')
            ->select('comment')
            ->where('comment.kamp_id = ?0')
            ->setParameter(0, $id)
            ->orderBy('comment.created_at', 'DESC')
            ->getQuery();
        // Paginate the results of the query
        $comments = $paginator->paginate(
        // Doctrine Query, not results
            $allCommentsQuery,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            5
        );
        $error = '';
        if ($comments->count() === 0) {
            $error = 'No comments yet for this Camp!';
        } else {
            foreach ($comments as $comment) {
                $created_date = $comment->getCreatedAt();
                $now = new DateTime();
                $created_info = $this->dateDifference($created_date, $now);
                $comment->{'timeAgo'} = $created_info['timeAgo'];
                $comment->{'created_at_slug'} = $created_info['created_at_slug'];
            }
        }
        if(!$record){
            return $this->render('admin/notFound.html.twig', [
                'controller_name' => 'AdminController@detail',
                'action' => 'see'
            ]);
        }
        return $this->render('admin/detail.html.twig', [
            'controller_name' => 'AdminController@detail',
            'title' => 'Kamp: '. $record->getTitle(),
            'record' => $record,
            'comments' => $comments,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/admin/delete/{id}")
     * @param $id
     * @return RedirectResponse|Response
     */
    public function delete($id) {
        $record = $this
            ->getDoctrine()
            ->getRepository('App:Kampen')
            ->findOneBy(
                array('id' => $id)
            );
        $comments = $this
            ->getDoctrine()
            ->getRepository('App:Comments')
            ->findBy(
                array('kamp_id' => $id)
            );
        if($record){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($record);
            if($comments){
                foreach ($comments as $comment){
                    $entityManager->remove($comment);
                }
            }
            $entityManager->flush();
            return $this->redirectToRoute('admin');
        }
        return $this->render('admin/notFound.html.twig', [
            'controller_name' => 'AdminController@delete',
            'action' => 'delete',
        ]);
    }
}
