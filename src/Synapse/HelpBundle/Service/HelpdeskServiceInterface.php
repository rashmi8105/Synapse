<?php
namespace Synapse\HelpBundle\Service;

use Synapse\HelpBundle\EntityDto\TicketCategoryDto;
use Synapse\HelpBundle\EntityDto\TicketDto;
use Synapse\HelpBundle\EntityDto\TicketRequesterDto;

interface HelpdeskServiceInterface
{

    /**
     * Get applicable support categories for support tickets.
     * @return TicketCategoryDto returns a collection of categories via a DTO
     */
    public function getCategories();

    /**
     * Create a new support ticket from provided DTO's
     * @param  TicketDto          $ticketDto          DTO containing information about the ticket
     * @param  TicketRequesterDto $ticketRequesterDto DTO containing information about the ticket requester
     * @return TicketDto                              DTO containing information about the ticket
     */
    public function createTicket(TicketDto $ticketDto);
    
    public function getSubDomain();
}