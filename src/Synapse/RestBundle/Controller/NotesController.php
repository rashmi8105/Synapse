<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Service\Impl\NotesService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\Response;
use Synapse\RestBundle\Entity\Error;
use Synapse\RestBundle\Entity\NotesDto;

/**
 * NotesController
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("/notes")
 */
class NotesController extends AbstractAuthController
{

    /**
     *
     * @var NotesService
     *     
     *      @DI\Inject(NotesService::SERVICE_KEY)
     */
    private $notesService;

    /**
     * Creates a new note.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Note",
     * input = "Synapse\RestBundle\Entity\NotesDto",
     * output = "Synapse\RestBundle\Entity\NotesDto",
     * section = "Notes",
     * statusCodes = {
     *                  201 = "Note was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param NotesDto $notesDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     */
    public function createNoteAction(NotesDto $notesDto, ConstraintViolationListInterface $validationErrors)
    {
        $this->ensureAccess([self::PERM_NOTES_PUBLIC_CREATE,self::PERM_NOTES_PRIVATE_CREATE, self::PERM_NOTES_TEAMS_CREATE]);
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($notesDto, $errors), 400);
        } else {
            $note = $this->notesService->createNote($notesDto);
            return new Response($note);
        }
    }

    /**
     * Edits an existing note.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Edit Note",
     * input = "Synapse\RestBundle\Entity\NotesDto",
     * output = "Synapse\RestBundle\Entity\NotesDto",
     * section = "Notes",
     * statusCodes = {
     *                  201 = "Note was edited. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Put("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param NotesDto $notesDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     */
    public function editNotesAction(NotesDto $notesDto, ConstraintViolationListInterface $validationErrors)
    {
        $this->ensureAccess([self::PERM_NOTES_PUBLIC_CREATE,self::PERM_NOTES_PRIVATE_CREATE, self::PERM_NOTES_TEAMS_CREATE]);
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($notesDto, $errors), 400);
        } else {
            $result = $this->notesService->editNote($notesDto);
            return new Response($result);
        }
    }

    /**
     * Deletes an existing note.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete Note",
     * section = "Notes",
     * statusCodes = {
     *                  204 = "Note was deleted. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Delete("/{noteId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param int $noteId
     * @return Response
     */
    public function deleteNoteAction($noteId)
    {
        $this->ensureAccess([self::PERM_NOTES_PUBLIC_CREATE,self::PERM_NOTES_PRIVATE_CREATE, self::PERM_NOTES_TEAMS_CREATE]);
        $note = $this->notesService->deleteNote($noteId);
        return new Response($note);
    }

    /**
     * Gets a note and its contents.
     *
     * @ApiDoc(
     * resource = true,
     * description = "View Note",
     * section = "Notes",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/{noteId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $noteId
     * @return Response
     */
    public function viewNoteAction($noteId)
    {   
        $this->ensureAccess([self::PERM_NOTES_PUBLIC_VIEW,self::PERM_NOTES_PRIVATE_VIEW, self::PERM_NOTES_TEAMS_VIEW]);
        $result = $this->notesService->getNotes($noteId);
        return new Response($result);
    }
}