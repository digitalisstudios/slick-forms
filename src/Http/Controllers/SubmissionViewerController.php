<?php

namespace DigitalisStudios\SlickForms\Http\Controllers;

use DigitalisStudios\SlickForms\Models\CustomForm;
use Illuminate\Routing\Controller;

class SubmissionViewerController extends Controller
{
    public function show(CustomForm $form)
    {
        return view('slick-forms::submissions.show', compact('form'));
    }
}
