<?php

namespace DigitalisStudios\SlickForms\Http\Controllers;

use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Services\SubmissionExportService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class SubmissionExportController extends Controller
{
    public function __construct(
        protected SubmissionExportService $exportService
    ) {}

    public function csv(Request $request, CustomForm $form): BinaryFileResponse
    {
        $search = $request->input('search');
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        return $this->exportService->exportToCsv($form, $search, $startDate, $endDate);
    }

    public function excel(Request $request, CustomForm $form): BinaryFileResponse
    {
        $search = $request->input('search');
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        return $this->exportService->exportToExcel($form, $search, $startDate, $endDate);
    }

    public function pdf(Request $request, CustomForm $form): Response
    {
        $search = $request->input('search');
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        return $this->exportService->exportToPdf($form, $search, $startDate, $endDate);
    }
}
