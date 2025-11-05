<?php

namespace DigitalisStudios\SlickForms\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class SubmissionsExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected Collection $submissions;

    protected array $fields;

    protected string $formName;

    public function __construct(Collection $submissions, array $fields, string $formName)
    {
        $this->submissions = $submissions;
        $this->fields = $fields;
        $this->formName = $formName;
    }

    public function collection()
    {
        return $this->submissions;
    }

    public function headings(): array
    {
        $headings = ['ID', 'Submitted At', 'IP Address'];

        foreach ($this->fields as $field) {
            $headings[] = $field['label'];
        }

        return $headings;
    }

    public function map($submission): array
    {
        $row = [
            $submission->id,
            $submission->created_at->format('Y-m-d H:i:s'),
            $submission->ip_address ?? 'N/A',
        ];

        // Get all field values as associative array
        $fieldValues = $submission->fieldValues->keyBy('slick_form_field_id');

        foreach ($this->fields as $field) {
            $fieldValue = $fieldValues->get($field['id']);

            if ($fieldValue) {
                $value = $fieldValue->value;

                // Handle array values (multi-select, checkboxes, tags)
                if (is_array($value)) {
                    $row[] = implode(', ', $value);
                } else {
                    $row[] = $value;
                }
            } else {
                $row[] = '';
            }
        }

        return $row;
    }

    public function title(): string
    {
        return substr($this->formName, 0, 31); // Excel sheet name limit
    }
}
