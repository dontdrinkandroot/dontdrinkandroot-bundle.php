<?php


namespace Dontdrinkandroot\UtilsBundle\Controller;

use Dontdrinkandroot\Entity\UuidEntityInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class AbstractUuidEntityController extends AbstractEntityController
{

    /**
     * {@inheritdoc}
     */
    protected function getViewPrefix()
    {
        if (null !== $this->viewPrefix) {
            return $this->viewPrefix;
        }

        return 'DdrUtilsBundle:UuidEntity';
    }

    /**
     * {@inheritdoc}
     */
    protected function fetchEntity($id)
    {
        $entity = $this->getRepository()->findOneBy(['uuid' => $id]);
        if (null === $entity) {
            throw new NotFoundHttpException();
        }

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    protected function createPostEditResponse(Request $request, UuidEntityInterface $entity)
    {
        return $this->redirectToRoute($this->getDetailRoute(), ['id' => $entity->getUuid()]);
    }
}
