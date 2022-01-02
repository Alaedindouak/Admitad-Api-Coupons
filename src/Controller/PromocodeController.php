<?php

namespace App\Controller;

use App\Entity\AdmitadCoupon;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PromocodeController extends AbstractController
{

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="admitad_promocodes")
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $data = $this->entityManager
            ->getRepository(AdmitadCoupon::class)
            ->findAll();

        $promocodes = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            9
        );

        return $this->render('promocode/index.html.twig', [
            'promocodes' => $promocodes
        ]);
    }

    /**
     * @Route("/promocode/{id}", name="admitad_show_promocode")
     */
    public function show(AdmitadCoupon $promocode): Response
    {
        return $this->render('promocode/show.html.twig', [
            'promocode' => $promocode
        ]);
    }

//    /**
//     * @Route('/promocode/{slug}', methods={'GET'}, name='admitad_show_promocode')
//     */
//    public function showPromocode(AdmitadCoupon $promocode): Response
//    {
//        $name = 'douak';
//
//        return $this->render('promocode/show.html.twig', [
//            'name' => $name
//        ]);
//    }
}
