<?php

namespace DigitalisStudios\SlickForms\Http\Controllers;

use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Services\FormTemplateService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FormBuilderController extends Controller
{
    public function index()
    {
        return view('slick-forms::manage.index');
    }

    public function create()
    {
        return view('slick-forms::manage.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
            'expires_at' => 'nullable|date|after:now',
            'time_limited' => 'nullable|integer|min:0',
        ]);

        // Convert time_limited from minutes to seconds
        if (isset($validated['time_limited'])) {
            $validated['time_limited'] = $validated['time_limited'] * 60;
        }

        // Ensure boolean fields are properly set (checkboxes don't send value if unchecked)
        $validated['is_active'] = $request->has('is_active');
        $validated['is_public'] = $request->has('is_public');

        $form = CustomForm::create($validated);

        return redirect()->route('slick-forms.builder.show', $form)
            ->with('success', 'Form created successfully!');
    }

    public function show(CustomForm $form)
    {
        return view('slick-forms::builder.show', compact('form'));
    }

    public function edit(CustomForm $form)
    {
        return view('slick-forms::manage.edit', compact('form'));
    }

    public function update(Request $request, CustomForm $form)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
            'expires_at' => 'nullable|date',
            'time_limited' => 'nullable|integer|min:0',
        ]);

        // Convert time_limited from minutes to seconds
        if (isset($validated['time_limited'])) {
            $validated['time_limited'] = $validated['time_limited'] * 60;
        }

        // Ensure boolean fields are properly set (checkboxes don't send value if unchecked)
        $validated['is_active'] = $request->has('is_active');
        $validated['is_public'] = $request->has('is_public');

        $form->update($validated);

        return redirect()->route('slick-forms.builder.show', $form)
            ->with('success', 'Form updated successfully!');
    }

    public function destroy(CustomForm $form)
    {
        $form->delete();

        return redirect()->route('slick-forms.manage.index')
            ->with('success', 'Form deleted successfully!');
    }

    public function duplicate(CustomForm $form)
    {
        $templateService = app(FormTemplateService::class);

        try {
            $newForm = $templateService->createFromTemplate($form, $form->name.' (Copy)');

            return redirect()->route('slick-forms.builder.show', $newForm)
                ->with('success', "Form '{$newForm->name}' duplicated successfully!");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to duplicate form: '.$e->getMessage());
        }
    }

    public function toggleActive(CustomForm $form)
    {
        $form->update(['is_active' => ! $form->is_active]);

        $status = $form->is_active ? 'enabled' : 'disabled';

        return redirect()->back()
            ->with('success', "Form '{$form->name}' {$status} successfully!");
    }
}
