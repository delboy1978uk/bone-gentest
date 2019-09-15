<?php declare(strict_types=1);

namespace Random\Developer\Jedi\Controller;

use Bone\Mvc\View\ViewEngine;
use Bone\View\Helper\AlertBox;
use Bone\View\Helper\Paginator;
use Del\Form\Field\Submit;
use Del\Form\Form;
use Del\Icon;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Random\Developer\Jedi\Collection\JediCollection;
use Random\Developer\Jedi\Entity\Jedi;
use Random\Developer\Jedi\Form\JediForm;
use Random\Developer\Jedi\Service\JediService;
use Zend\Diactoros\Response\HtmlResponse;

class JediController
{
    /** @var int $numPerPage */
    private $numPerPage = 10;

    /** @var Paginator $paginator */
    private $paginator;

    /** @var JediService $service */
    private $service;

    /** @var ViewEngine $view */
    private $view;

    /**
     * @param JediService $service
     */
    public function __construct(ViewEngine $view, JediService $service)
    {
        $this->paginator = new Paginator();
        $this->service = $service;
        $this->view = $view;
    }

    /**
     * @param ServerRequestInterface $request
     * @param array $args
     * @return ResponseInterface $response
     * @throws \Exception
     */
    public function indexAction(ServerRequestInterface $request, array $args): ResponseInterface
    {
        $db = $this->service->getRepository();
        $total = $db->getTotalJediCount();

        $this->paginator->setUrl('jedi?page=:page');
        $page = (int) $request->getQueryParams()['page'] ?: 1;
        $this->paginator->setCurrentPage($page);
        $this->paginator->setPageCountByTotalRecords($total, $this->numPerPage);

        $jedis = new JediCollection($db->findBy([], null, $this->numPerPage, ($page *  $this->numPerPage) - $this->numPerPage));

        $body = $this->view->render('jedi::index', [
            'jedis' => $jedis,
            'paginator' => $this->paginator->render(),
        ]);

        return new HtmlResponse($body);
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface $response
     * @throws \Exception
     */
    public function viewAction(ServerRequestInterface $request, array $args): ResponseInterface
    {
        $db = $this->service->getRepository();
        $id = $args['id'];
        $jedi = $db->find($id);
        $body = $this->view->render('jedi::view', [
            'jedi' => $jedi,
        ]);

        return new HtmlResponse($body);
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface $response
     * @throws \Exception
     */
    public function createAction(ServerRequestInterface $request, array $args): ResponseInterface
    {
        $msg = '';
        $form = new JediForm('createJedi');
        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();
            $form->populate($post);
            if ($form->isValid()) {
                $data = $form->getValues();
                $jedi = $this->service->createFromArray($data);
                $this->service->saveJedi($jedi);
                $msg = $this->alertBox(Icon::CHECK_CIRCLE . ' New jedi added to database.', 'success');
                $form = new JediForm('createJedi');
            } else {
                $msg = $this->alertBox(Icon::REMOVE . ' There was a problem with the form.', 'danger');
            }
        }

        $form = $form->render();
        $body = $this->view->render('jedi::create', [
            'form' => $form,
            'msg' => $msg,
        ]);

        return new HtmlResponse($body);
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface $response
     * @throws \Exception
     */
    public function editAction(ServerRequestInterface $request, array $args): ResponseInterface
    {
        $msg = '';
        $form = new JediForm('editJedi');
        $id = $args['id'];
        $db = $this->service->getRepository();
        /** @var Jedi $jedi */
        $jedi = $db->find($id);
        $form->populate($jedi->toArray());

        if ($request->getMethod() === 'POST') {
            $post = $request->getParsedBody();
            $form->populate($post);
            if ($form->isValid()) {
                $data = $form->getValues();
                $jedi = $this->service->updateFromArray($jedi, $data);
                $this->service->saveJedi($jedi);
                $msg = $this->alertBox(Icon::CHECK_CIRCLE . ' Jedi details updated.', 'success');
            } else {
                $msg = $this->alertBox(Icon::REMOVE . ' There was a problem with the form.', 'danger');
            }
        }

        $form = $form->render();
        $body = $this->view->render('jedi::edit', [
            'form' => $form,
            'msg' => $msg,
        ]);

        return new HtmlResponse($body);
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface $response
     * @throws \Exception
     */
    public function deleteAction(ServerRequestInterface $request, array $args): ResponseInterface
    {
        $id = $args['id'];
        $db = $this->service->getRepository();
        $form = new Form('deleteJedi');
        $submit = new Submit('submit');
        $submit->setValue('Delete');
        $submit->setClass('btn btn-danger');
        $form->addField($submit);
        /** @var Jedi $jedi */
        $jedi = $db->find($id);

        if ($request->getMethod() === 'POST') {
            $this->service->deleteJedi($jedi);
            $msg = $this->alertBox(Icon::CHECK_CIRCLE . ' Jedi deleted.', 'warning');
            $form = '<a href="/jedi" class="btn btn-default">Back</a>';
        } else {
            $form = $form->render();
            $msg = $this->alertBox(Icon::WARNING . ' Warning, please confirm your intention to delete.', 'danger');
            $msg .= '<p class="lead">Are you sure you want to delete ' . $jedi->getName() . '?</p>';
        }

        $body = $this->view->render('jedi::delete', [
            'jedi' => $jedi,
            'form' => $form,
            'msg' => $msg,
        ]);

        return new HtmlResponse($body);
    }

    /**
     * @param string $message
     * @param string $class
     * @return string
     */
    private function alertBox(string $message, string $class): string
    {
        return AlertBox::alertBox([
            'message' => $message,
            'class' => $class,
        ]);
    }
}
