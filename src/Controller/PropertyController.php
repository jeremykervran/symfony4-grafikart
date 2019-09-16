<?php

namespace App\Controller;

use App\Entity\Property;
use App\Repository\PropertyRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PropertyController extends AbstractController
{
    /**
     * @var PropertyRepository
     */
    private $repository;

    /**
     * @var ObjectManager
     */
    private $em;

    public function __construct(PropertyRepository $repository, ObjectManager $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/biens", name="property.index")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {

        $properties = $paginator->paginate($this->repository->findAllVisibleQuery(), $request->query->getInt('page', 1), 12);
        return $this->render(
            'property/index.html.twig',
            [
                'current_menu' => 'properties',
                'properties' => $properties,
            ]
        );
    }

    /**
     * @Route("/biens/{slug}-{id}", name="property.show", requirements={"slug": "[a-z0-9\-]*"})
     * @param Property $property
     * @param string $slug
     * @return Response
     */
    public function show(Property $property, string $slug): Response
    {
        $getSlug = $property->getSlug();
        if ($getSlug !== $slug) {
            return $this->redirectToRoute(
                'property.show',
                [
                    'id' => $property->getId(),
                    'slug' => $getSlug,
                ],
                301
            );
        }

        return $this->render(
            'property/show.html.twig',
            [
                'property' => $property,
                'current_menu' => 'properties',
            ]
        );
    }

}