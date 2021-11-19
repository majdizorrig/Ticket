<?php

namespace App\Controller;

use App\Entity\Ticket;

use App\Repository\TicketRepository;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

class TicketController extends AbstractController
{
    #[Route('/ticket', name: 'ticket')]

    public function index(): Response
    {
        return $this->render('ticket/index.html.twig', [
            'controller_name' => 'TicketController',
        ]);
    }


    #[Route('add/{titre}/{nompersonne}/{description}', name: 'ticket.add')]
    public function addTicket($titre,$nompersonne, $description) {
        $manager = $this->getDoctrine()->getManager();
        $ticket = new Ticket();
        $ticket->setTitre($titre);
        $ticket->setNomPersonne($nompersonne);
        $ticket->setDescription($description);
        $ticket->setStatut("EN ATTENTE");

//        $datenow = new \DateTime('now');
//        echo $datenow;

                $today = getdate();
        $ticket->setDate($today['mday'].'/'.$today['mon'].'/'.$today['year']);
//
        //Persister ajouter dans la transaction
        $manager->persist($ticket);
//
        //executer la transaction
        $manager->flush();
        $this->addFlash('success', "Ticket ADDED");
        return $this->render('ticket/index.html.twig', [
            'controller_name' => 'TicketController',
            'ticket'=>$ticket,
            //'sysdate'=>$datenow
        ]);
    }

    #[Route('/modif/{id}/{nouvStatut}', name: 'ticket.modif')]
    public function modifById($nouvStatut,Ticket $ticket = null): Response
    {
        if (!$ticket)  {
            $this->addFlash('error', "ID NOT FOUND");
        }
        else {
            $manager = $this->getDoctrine()->getManager();

            $ticket->setStatut($nouvStatut);
            $manager->persist($ticket);
            $manager->flush();
        }
        return $this->render('ticket/index.html.twig', [
            'ticket' => $ticket
        ]);
    }

    #[Route('/delete/{id}', name: 'ticket.delete')]
    public function deleteTicket(Ticket $ticket = null) {
        if($ticket) {
            // recupérer manager
            $manager = $this->getDoctrine()->getManager();
            // supprime le user avec le id
            $manager->remove($ticket);
            $manager->flush();
            $this->addFlash('success', "Ticket DELETED");
        } else {
            $this->addFlash('error', "ID NOT FOUND");
        }
        return $this->forward('App\Controller\TicketController::listAllTickets');

    }


    #[Route('/ticketById/{id}', name: 'ticket.ticketById')]
    public function ticketById(Ticket $ticket = null)
    {
        if (!$ticket)  {
            $this->addFlash('error', "ID NOT FOUND");
        }
        else {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($ticket);
            $manager->flush();
        }
        return $this->render('ticket/index.html.twig', [
            'ticket' => $ticket
        ]);
    }


    #[Route('/all/{nbPage?1}', name: 'ticket.list')]
    public function listAllTickets($nbPage) {
        $limit = 3;
        $offset = ($nbPage-1)*$limit;

        // Récupérer la liste des tickets
        $repository = $this->getDoctrine()->getRepository(ticket::class);

        $tickets = $repository->findBy([],[],$limit,$offset);
        // l'envoyer à twig

        return $this->render('ticket/list.html.twig', [
            'tickets' => $tickets,
            'nbpage'  =>$nbPage
        ]);
    }


    #[Route('/interval/date/{min}/{max}', name: 'ticket.list.date.interval')]
    public function listAllTicketsByIntervalDate(TicketRepository $ticketRepository, $min,$max) {
//        $limit = 3;
//        $offset = ($nbPage-1)*$limit;
        //$tickets = new Ticket();
       // $ticketParDate = [];
        $tickets=$ticketRepository->TicketByDate($min,$max);

//         Récupérer la liste des tickets
//        $repository = $this->getDoctrine()->getRepository(ticket::class);
//        $datemin = new DateTime($min);
//        $datemax = new DateTime($max);
//        $tickets = $repository->
//
////        for ($i=0;$i<=$tickets<>Null;$i++) {
////
////
////            $datecreation = new \DateTime($tickets->date);
////            if ($datecreation<=$datemax and $datecreation>=$datemin)
////            {
////                $ticketParDate[]=$tickets;
////            }
////        }
//        // l'envoyer à twig

        return $this->render('ticket/list-par-interv-date.html.twig', [
            'tickets' => $tickets
        ]);
    }




}
