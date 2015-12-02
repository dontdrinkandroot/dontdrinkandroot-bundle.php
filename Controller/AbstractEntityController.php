<?php

namespace Dontdrinkandroot\UtilsBundle\Controller;

use Dontdrinkandroot\Entity\EntityInterface;
use Dontdrinkandroot\Entity\UpdatedEntityInterface;
use Dontdrinkandroot\Repository\OrmEntityRepository;
use Dontdrinkandroot\Utils\StringUtils;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class AbstractEntityController extends Controller implements EntityControllerInterface
{

    protected $routePrefix = null;

    protected $viewPrefix = null;

    protected $pathPrefix = null;

    /**
     * {@inheritdoc}
     */
    public function listAction(Request $request)
    {
        $user = $this->getUser();
        $this->checkListActionAuthorization($user);

        $view = $this->getListView();
        $model = $this->getListModel($request);

        return $this->render($view, $model);
    }

    /**
     * {@inheritdoc}
     */
    public function detailAction(Request $request, $id)
    {
        $user = $this->getUser();
        $this->checkDetailActionAuthorization($user);

        $entity = $this->fetchEntity($id);

        $response = new Response();
        $lastModified = $this->getLastModified($entity);
        if (null !== $lastModified) {

            $response->setLastModified($lastModified);
            $response->setPublic();

            if ($response->isNotModified($request)) {
                return $response;
            }
        }

        $model = $this->getDetailModel($request, $entity);
        $view = $this->getDetailView();

        return $this->render($view, $model, $response);
    }

    /**
     * {@inheritdoc}
     */
    public function editAction(Request $request, $id = null)
    {
        $user = $this->getUser();
        $this->checkEditActionAuthorization($user);

        $new = true;
        $entity = null;
        if (null !== $id && $id !== 'new') {
            $new = false;
            $entity = $this->fetchEntity($id);
        }
        $form = $this->createForm($this->getFormType(), $entity);

        $form->handleRequest($request);
        if ($form->isValid()) {
            /** @var EntityInterface $entity */
            $entity = $form->getData();
            if ($new) {
                $entity = $this->getRepository()->persist($entity);
            } else {
                $this->getRepository()->flush($entity);
            }

            return $this->createPostEditResponse($request, $entity);
        }

        $view = $this->getEditView();

        return $this->render($view, ['entity' => $entity, 'form' => $form->createView()]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAction(Request $request, $id)
    {
        $user = $this->getUser();
        $this->checkDeleteActionAuthorization($user);

        $entity = $this->fetchEntity($id);
        $this->getRepository()->remove($entity);

        return $this->createPostDeleteResponse($request, $entity);
    }

    /**
     * {@inheritdoc}
     */
    public function getRoutePrefix()
    {
        if (null !== $this->routePrefix) {
            return $this->routePrefix;
        }

        list($bundle, $entityName) = $this->extractBundleAndEntityName();

        $prefix = str_replace('Bundle', '', $bundle);
        $prefix = $prefix . '.' . $entityName;
        $prefix = str_replace('\\', '.', $prefix);
        $prefix = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $prefix));

        return $prefix;
    }

    /**
     * {@inheritdoc}
     */
    public function getPathPrefix()
    {
        if (null !== $this->pathPrefix) {
            return $this->pathPrefix;
        }

        list($bundle, $entityName) = $this->extractBundleAndEntityName();

        return '/' . strtolower($entityName) . '/';
    }

    /**
     * @param string|null $routePrefix
     */
    public function setRoutePrefix($routePrefix)
    {
        $this->routePrefix = $routePrefix;
    }

    /**
     * @param string|null $viewPrefix
     */
    public function setViewPrefix($viewPrefix)
    {
        $this->viewPrefix = $viewPrefix;
    }

    /**
     * @param string|null $pathPrefix
     */
    public function setPathPrefix($pathPrefix)
    {
        $this->pathPrefix = $pathPrefix;
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
     * @return string
     */
    protected function getDetailView()
    {
        return $this->getViewPrefix() . ':detail.html.twig';
    }

    /**
     * @param Request $request
     * @param EntityInterface     $entity
     *
     * @return array
     */
    protected function getDetailModel(Request $request, EntityInterface $entity)
    {
        return [
            'entity' => $entity,
            'routes' => $this->getRoutes(),
            'fields' => $this->getDetailFields(),
        ];
    }

    /**
     * @return string
     */
    protected function getEditView()
    {
        return $this->getViewPrefix() . ':edit.html.twig';
    }

    /**
     * @param mixed $id
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
     * @return string
     */
    protected function getViewPrefix()
    {
        if (null !== $this->viewPrefix) {
            return $this->viewPrefix;
        }

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

    protected function extractBundleAndEntityName()
    {
        $shortName = $this->getEntityShortName();
        $parts = explode(':', $shortName);
        if (2 !== count($parts)) {
            throw new \Exception(sprintf('Expecting entity class to be "Bundle:Entity", %s given', $shortName));
        }

        return $parts;
    }

    /**
     * @param Request         $request
     * @param EntityInterface $entity
     *
     * @return Response
     */
    protected function createPostEditResponse(Request $request, EntityInterface $entity)
    {
        return $this->redirectToRoute($this->getDetailRoute(), ['id' => $entity->getId()]);
    }

    /**
     * @param Request         $request
     * @param EntityInterface $entity
     *
     * @return Response
     */
    protected function createPostDeleteResponse(Request $request, EntityInterface $entity)
    {
        return $this->redirectToRoute($this->getListRoute());
    }

    /**
     * @param EntityInterface $entity
     *
     * @return \DateTime|null
     */
    protected function getLastModified(EntityInterface $entity)
    {
        if (is_a($entity, UpdatedEntityInterface::class)) {
            /** @var UpdatedEntityInterface $updatedEntity */
            $updatedEntity = $entity;
            return $updatedEntity->getUpdated();
        }

        return null;
    }

    protected function checkListActionAuthorization($user)
    {
    }

    protected function checkDetailActionAuthorization($user)
    {
    }

    protected function checkEditActionAuthorization($user)
    {
    }

    protected function checkDeleteActionAuthorization($user)
    {
    }

    /**
     * @return FormTypeInterface
     */
    protected abstract function getFormType();

    /**
     * @return string
     */
    protected function getEntityShortName()
    {
        $entityClass = $this->getEntityClass();
        $entityClassParts = explode('\\', $entityClass);

        $bundle = $this->findBundle($entityClassParts);
        $className = $entityClassParts[count($entityClassParts) - 1];

        $shortName = $bundle . ':' . $className;

        return $shortName;
    }

    private function findBundle(array $entityClassParts)
    {
        foreach ($entityClassParts as $part) {
            if (StringUtils::endsWith($part, 'Bundle')) {
                return $part;
            }
        }

        throw new \RuntimeException('No Bundle found in namespace');
    }

    /**
     * @return string
     */
    protected abstract function getEntityClass();


}
