<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ServersRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\VoteType;
use App\Form\CommentType;
use App\Entity\Vote;
use App\Entity\Comments;
use App\Repository\VoteRepository;
use App\Repository\ServerStatisticsRepository;


#[Route('/{_locale}')]
class ServerController extends AbstractController
{
    protected $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;   
    }
    
    #[Route('/server/{sid}', name: 'server')]
    public function index(int $sid, ServersRepository $server, AuthenticationUtils $authenticationUtils, Request $request, VoteRepository $voteRepository, ServerStatisticsRepository $ssr): Response
    {
        $server = $server->find($sid);
        if (!$server) {
            throw new NotFoundHttpException('Server not found');
        }
        $uid =  $this->getUser() ? $this->getUser()->getId() : null;
        $vote = new Vote();
        $comment = new Comments();
        $yestarday = time() - (60*60*24); // current time - 24h in seconds
        
        $voteForm = $this->createForm(VoteType::class, $vote);
        $voteForm->handleRequest($request);
        if ($voteForm->isSubmitted() && $voteForm->isValid()) {
            $date = new \DateTime();
            $date->setTimestamp($yestarday);
            $checker = $voteRepository->checkVote($yestarday, $uid, $request->getClientIp(), $server->getId());
            if ($checker === null) {
                $vote->setVoteIp($request->getClientIp());
                $vote->setSid($server);
                $vote->setUser($this->getUser());
                $vote->setCreatedAt(new \DateTimeImmutable());
                
                $this->entityManager->persist($vote);
                $this->entityManager->flush();
                
                $this->addFlash(
                    'success',
                    'Congratulations you vote on this server!'
                );
            } else {
                 $this->addFlash(
                    'danger',
                    'You vote already in last 24h!'
                );
            }
        }     

        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setAccept(false);
            $comment->setSid($server);
            $comment->setUid($this->getUser());
            $comment->setCreatedAt(new \DateTimeImmutable());
            
            $this->entityManager->persist($comment);
            $this->entityManager->flush();
            
             $this->addFlash(
                'info',
                'The comment will be visible after approval by the administrator.'
            );
       
        }     
        
        $response = ( $this->render('server/index.html.twig', [
            'server' => $server,
            'voteForm' => $voteForm->createView(),
            'commentForm' => $commentForm->createView(),
            'last_username' => $authenticationUtils->getLastUsername(),
            'stats' => ($ssr->stats($sid))
        ]));
       // $response->setSharedMaxAge(300); // 5min
        return $response;
    }
}
