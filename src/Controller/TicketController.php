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



    #[Route('add/{titre}/{nompersonne}/{description}', name: 'ticket.add')]
    public function addTicket($titre,$nompersonne, $description) {
        $manager = $this->getDoctrine()->getManager();
        $ticket = new Ticket();
        $ticket->setTitre($titre);
        $ticket->setNomPersonne($nompersonne);
        $ticket->setDescription($description);
        $ticket->setStatut("EN ATTENTE");
        $today = getdate();
        $ticket->setDate($today['mday'].'/'.$today['mon'].'/'.$today['year']);

        $manager->persist($ticket);
        $manager->flush();
        $this->addFlash('success', "Ticket ADDED");
        return $this->render('ticket/index.html.twig', [
            'controller_name' => 'TicketController',
            'ticket'=>$ticket,

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

            $manager = $this->getDoctrine()->getManager();

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


        $repository = $this->getDoctrine()->getRepository(ticket::class);

        $tickets = $repository->findBy([],[],$limit,$offset);


        return $this->render('ticket/list.html.twig', [
            'tickets' => $tickets,
            'nbpage'  =>$nbPage
        ]);
    }


    #[Route('/interval/date/{min}/{max}', name: 'ticket.list.date.interval')]
    public function listAllTicketsByIntervalDate(TicketRepository $ticketRepository, $min,$max) {
//
        $tickets=$ticketRepository->TicketByDate($min,$max);

//
        return $this->render('ticket/list-par-interv-date.html.twig', [
            'tickets' => $tickets
        ]);
    }


    #[Route('/byStatut/{statut}', name: 'ticket.listByStatut')]
    public function TicketsByStatut($statut) {
//


        $repository = $this->getDoctrine()->getRepository(ticket::class);

        $tickets = $repository->findBy(['statut'=>$statut]);


        return $this->render('ticket/list.html.twig', [
            'tickets' => $tickets,

        ]);
    }




}
