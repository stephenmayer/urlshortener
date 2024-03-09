<?php

namespace App\Controller;

use App\Service\CSVUploadService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ImportCsvController extends AbstractController
{
    public function __construct(private CSVUploadService $csvUploadService)
    {
    }

    #[Route('/', name: 'app_import_form')]
    public function index(): Response
    {
        return $this->render('import_csv/index.html.twig', [
            'controller_name' => 'ImportCsvController',
        ]);
    }

    #[Route('/import', name: 'app_import_csv')]
    public function import(Request $request): Response
    {
        $links = $this->csvUploadService->getLinksFromUploadFile($request->files->get('csv'));
        $response = $this->csvUploadService->processLinks($links);

        return $this->render('import_csv/result.html.twig', [
            'links' => $response,
        ]);
    }
}
