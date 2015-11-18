<?php

namespace Dontdrinkandroot\UtilsBundle\Controller;

use Dontdrinkandroot\Entity\EntityInterface;
use Dontdrinkandroot\Repository\OrmEntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class AbstractEntityController extends Controller
{

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request)
    {
        return $this->createListResponse($request);
    }

    /**
     * @param Request $request
     * @param int     $id
     *
     * @return Response
     */
    public function detailAction(Request $request, $id)
    {
        return $this->createDetailResponse($request, $id);
    }

    /**
     * @param Request         $request
     * @param int|null|string $id
     *
     * @return Response
     */
    public function editAction(Request $request, $id = null)
    {
        return $this->createdEditResponse($request, $id);
    }

    /**
     * @param Request $request
     * @param int     $id
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        return $this->createDeleteResponse($request, $id);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    protected function createListResponse(Request $request)
    {
        $view = $this->getListView();
        $model = $this->getListModel($request);

        return $this->render($view, $model);
    }

    /**
     * @return string
     */
    protected function getListView()
    {
        return $this->getViewPrefix() . ':list.html.twig';
    }

    /**
     * @param Request $request
     *
     * @return array
     * @throws \Exception
     */
    protected function getListModel(Request $request)
    {
        $page = $request->query->get('page', 1);
        $perPage = $request->query->get('perpage', 10);
        $paginatedEntities = $this->getRepository()->findPaginatedBy($page, $perPage);

        return [
            'pagination' => $paginatedEntities->getPagination(),
            'entities'   => $paginatedEntities->getResults(),
            'fields'     => $this->getListFields(),
            'routes'     => $this->getRoutes()
        ];
    }

    /**
     * @param Request $request
     * @param int     $id
     *
     * @return Response
     */
    protected function createDetailResponse(Request $request, $id)
    {
        $view = $this->getDetailView();
        $model = $this->getDetailModel($request, $id);

        return $this->render($view, $model);
    }

    /**
     * @return string
     */
    protected function getDetailView()
    {
        return $this->getViewPrefix() . ':detail.html.twig';
    }

    /**
     * @param Request $request
     * @param int     $id
     *
     * @return array
     */
    protected function getDetailModel(Request $request, $id)
    {
        $entity = $this->fetchEntity($id);

        return [
            'entity' => $entity,
            'routes' => $this->getRoutes(),
            'fields' => $this->getDetailFields(),
        ];
    }

    /**
     * @param Request  $request
     * @param int|null $id
     *
     * @return Response
     */
    protected function createdEditResponse(Request $request, $id = null)
    {
        $view = $this->getEditView();

        $entity = null;
        if (null !== $id && $id !== 'new') {
            $entity = $this->fetchEntity($id);
        }
        $form = $this->createForm($this->getFormType(), $entity);

        $form->handleRequest($request);
        if ($form->isValid()) {
            /** @var EntityInterface $entity */
            $entity = $form->getData();
            $entity = $this->getRepository()->save($entity);

            return $this->redirectToRoute($this->getDetailRoute(), ['id' => $entity->getId()]);
        }

        return $this->render($view, ['entity' => $entity, 'form' => $form->createView()]);
    }

    /**
     * @return string
     */
    protected function getEditView()
    {
        return $this->getViewPrefix() . ':edit.html.twig';
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return Response
     */
    protected function createDeleteResponse(Request $request, $id)
    {
        $entity = $this->fetchEntity($id);
        $this->getRepository()->remove($entity);

        return $this->redirectToRoute($this->getListRoute());
    }

    /**
     * @param $id
     *
     * @return EntityInterface
     */
    protected function fetchEntity($id)
    {
        $entity = $this->getRepository()->find($id);
        if (null === $entity) {
            throw new NotFoundHttpException();
        }

        return $entity;
    }

    /**
     * @return array
     */
    protected function getRoutes()
    {
        return [
            'list'   => $this->getListRoute(),
            'detail' => $this->getDetailRoute(),
            'edit'   => $this->getEditRoute(),
            'delete' => $this->getDeleteRoute()
        ];
    }

    /**
     * @return string
     */
    protected function getListRoute()
    {
        return $this->getRoutePrefix() . ".list";
    }

    /**
     * @return string
     */
    protected function getDetailRoute()
    {
        return $this->getRoutePrefix() . ".detail";
    }

    /**
     * @return string
     */
    protected function getEditRoute()
    {
        return $this->getRoutePrefix() . ".edit";
    }

    /**
     * @return string
     */
    protected function getDeleteRoute()
    {
        return $this->getRoutePrefix() . ".delete";
    }

    /**
     * @string
     */
    protected function getViewPrefix()
    {
        return 'DdrUtilsBundle:Entity';
    }

    /**
     * @return OrmEntityRepository
     */
    protected function getRepository()
    {
        return $this->getDoctrine()->getRepository($this->getEntityClass());
    }

    /**
     * @return array
     */
    protected function getListFields()
    {
        return [
            'id' => 'Id'
        ];
    }

    /**
     * @return array
     */
    protected function getDetailFields()
    {
        return [
            'id' => 'Id'
        ];
    }

    /**
     * @return FormTypeInterface
     */
    protected abstract function getFormType();

    /**
     * @return string
     */
    protected abstract function getRoutePrefix();

    /**
     * @return string
     */
    protected abstract function getEntityClass();
}
