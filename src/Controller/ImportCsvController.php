<?php

namespace App\Controller;

use App\Repository\LinkRepository;
use App\Service\CSVUploadService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ImportCsvController extends AbstractController
{
    public function __construct(
        private readonly CSVUploadService $csvUploadService,
        private readonly LinkRepository $shortLinksRepository
    )
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
        if ($request->files->get('csv')) {
            // @todo we should probably make sure that this is a CSV file
            if ($links = $this->csvUploadService->getLinksFromUploadFile($request->files->get('csv'))) {
                $response = $this->csvUploadService->processLinks($links);
            }

            return $this->render('import_csv/result.html.twig', [
                'links' => $response,
            ]);
        }

        return new Response('No file uploaded', 400);
    }

    #[Route('/analytics/{shortUrl}', name: 'app_analytics')]
    public function analytics($shortUrl): Response
    {
        $shortLink = $this->shortLinksRepository->findByShortUrl($shortUrl);

        return $this->render('import_csv/analytics.html.twig', [
            'links' => [$shortLink],
        ]);
    }
}
