<?php

namespace DigitalisStudios\SlickForms\Livewire;

use DigitalisStudios\SlickForms\Services\FormTemplateService;
use Illuminate\Support\Collection;
use Livewire\Component;

class FormTemplates extends Component
{
    public bool $collapsed = true;

    public function toggleCollapse(): void
    {
        $this->collapsed = ! $this->collapsed;
    }

    public function getTemplatesProperty(): Collection
    {
        $templateService = app(FormTemplateService::class);

        return $templateService->getTemplates();
    }

    public function render()
    {
        return view('slick-forms::livewire.form-templates', [
            'templates' => $this->templates,
        ]);
    }
}
