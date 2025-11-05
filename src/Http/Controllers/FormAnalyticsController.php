<?php

namespace DigitalisStudios\SlickForms\Http\Controllers;

use DigitalisStudios\SlickForms\Models\CustomForm;
use Illuminate\Routing\Controller;

class FormAnalyticsController extends Controller
{
    public function show(CustomForm $form)
    {
        return view('slick-forms::analytics.show', compact('form'));
    }
}
