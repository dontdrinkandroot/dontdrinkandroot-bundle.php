<?php
namespace Dontdrinkandroot\UtilsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface EntityControllerInterface
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request);

    /**
     * @param Request $request
     * @param int     $id
     *
     * @return Response
     */
    public function detailAction(Request $request, $id);

    /**
     * @param Request         $request
     * @param int|null|string $id
     *
     * @return Response
     */
    public function editAction(Request $request, $id = null);

    /**
     * @param Request $request
     * @param int     $id
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id);

    /**
     * @return string
     */
    public function getRoutePrefix();

    /**
     * @return string
     */
    public function getPathPrefix();
}