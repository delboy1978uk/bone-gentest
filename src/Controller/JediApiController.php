<?php

declare(strict_types=1);

namespace Random\Developer\Jedi\Controller;

use League\Route\Http\Exception\NotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Random\Developer\Jedi\Collection\JediCollection;
use Random\Developer\Jedi\Form\JediForm;
use Random\Developer\Jedi\Service\JediService;
use Zend\Diactoros\Response\JsonResponse;

class JediApiController
{
    /** @param JediService $service */
    private $service;

    /**
     * @param JediService $service
     */
    public function __construct(JediService $service)
    {
        $this->service = $service;
    }

    /**
     * @param ServerRequestInterface $request
     * @param array $args
     * @return ResponseInterface
     * @throws NotFoundException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function indexAction(ServerRequestInterface $request, array $args): ResponseInterface
    {
        $params = $request->getQueryParams();
        $limit = $params['limit'];
        $offset = $params['offset'];
        $db = $this->service->getRepository();
        $jedis = new JediCollection($db->findBy([], null, $limit, $offset));
        $total = $db->getTotalJediCount();
        $count = count($jedis);
        if ($count < 1) {
            throw new NotFoundException();
        }

        $payload['_embedded'] = $jedis->toArray();
        $payload['count'] = $count;
        $payload['total'] = $total;

        return new JsonResponse($payload);
    }

    /**
     * @param ServerRequestInterface $request
     * @param array $args
     * @return ResponseInterface
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createAction(ServerRequestInterface $request, array $args): ResponseInterface
    {
        $post = json_decode($request->getBody()->getContents(), true) ?: $request->getParsedBody();
        $form = new JediForm('create');
        $form->populate($post);

        if ($form->isValid()) {
            $data = $form->getValues();
            $jedi = $this->service->createFromArray($data);
            $this->service->saveJedi($jedi);

            return new JsonResponse($jedi->toArray());
        }

        return new JsonResponse([
            'error' => $form->getErrorMessages(),
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     * @param array $args
     * @return ResponseInterface
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function viewAction(ServerRequestInterface $request, array $args): ResponseInterface
    {
        $jedi = $this->service->getRepository()->find($args['id']);

        return new JsonResponse($jedi->toArray());
    }

    /**
     * @param ServerRequestInterface $request
     * @param array $args
     * @return ResponseInterface
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateAction(ServerRequestInterface $request, array $args): ResponseInterface
    {
        $db = $this->service->getRepository();
        $jedi = $db->find($args['id']);

        $post = json_decode($request->getBody()->getContents(), true) ?: $request->getParsedBody();
        $form = new JediForm('update');
        $form->populate($post);

        if ($form->isValid()) {
            $data = $form->getValues();
            $jedi = $this->service->updateFromArray($jedi, $data);
            $this->service->saveJedi($jedi);

            return new JsonResponse($jedi->toArray());
        }

        return new JsonResponse([
            'error' => $form->getErrorMessages(),
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     * @param array $args
     * @return ResponseInterface
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteAction(ServerRequestInterface $request, array $args): ResponseInterface
    {
        $db = $this->service->getRepository();
        $jedi = $db->find($args['id']);
        $this->service->deleteJedi($jedi);

        return new JsonResponse(['deleted' => true]);
    }
}
