<?php

namespace DigitalisStudios\SlickForms\Http\Controllers;

use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Services\FormTemplateService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TemplateController extends Controller
{
    protected FormTemplateService $templateService;

    public function __construct(FormTemplateService $templateService)
    {
        $this->templateService = $templateService;
    }

    /**
     * Create a new form from a template
     */
    public function use(Request $request, CustomForm $template)
    {
        $validated = $request->validate([
            'form_name' => 'required|string|max:255',
        ]);

        try {
            $form = $this->templateService->createFromTemplate($template, $validated['form_name']);

            return redirect()->route('slick-forms.builder.show', $form)
                ->with('success', "Form '{$form->name}' created from template successfully!");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create form from template: '.$e->getMessage());
        }
    }

    /**
     * Save an existing form as a template
     */
    public function saveAsTemplate(Request $request, CustomForm $form)
    {
        $validated = $request->validate([
            'template_name' => 'required|string|max:255',
            'template_category' => 'required|string|in:contact,survey,registration,order,application,quiz,lead,other',
            'template_description' => 'nullable|string',
        ]);

        try {
            $template = $this->templateService->saveAsTemplate(
                $form,
                $validated['template_name'],
                $validated['template_category'],
                $validated['template_description']
            );

            return redirect()->back()
                ->with('success', "Template '{$template->name}' created successfully!");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to save as template: '.$e->getMessage());
        }
    }
}
