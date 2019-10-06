<?php
namespace App\Controller;
use App\Entity\Kampen;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/vote", name="vote", methods={"POST"})
     *
     * @param $request
     *
     * @return Response
     */
    public function vote(Request $request) {
        $data = json_decode($request->getContent(), true);
        $id  = $data['id'];
        $vote = $data['vote'];
        $entityManager = $this->getDoctrine()->getManager();
        $camp = $entityManager->getRepository(Kampen::class)->find($id);
        if (!$camp) {
            throw $this->createNotFoundException(
                'No product found for id '.$camp
            );
        }
        if($vote == "up") {
            $camp->setLikes($camp->getLikes() + 1);
            $entityManager->flush();
            return new Response(
                Response::HTTP_OK
            );
        }elseif ($vote == "down") {
            $camp->setLikes($camp->getLikes() - 1);
            $entityManager->flush();
            return new Response(
                Response::HTTP_OK
            );
        }
        else {
            return new Response(
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}