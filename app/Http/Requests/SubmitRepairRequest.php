<?php

namespace App\Http\Requests;

use App\Models\RepairRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SubmitRepairRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', RepairRequest::class);
    }

    protected function prepareForValidation(): void
    {
        if ($this->missing('priority')) {
            $this->merge(['priority' => 'normal']);
        }
    }

    public function rules(): array
    {
        return [
            'submission_key' => ['required', 'uuid'],
            'property_name' => ['nullable', 'string', 'max:150'],
            'property_type' => ['nullable', 'string', 'max:100'],
            'street_address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:150'],
            'state' => ['required', 'regex:/^[A-Z]{2}$/D'],
            'zip' => ['required', 'regex:/^\d{5}(?:-\d{4})?$/D'],
            'home_sqft' => ['required', 'regex:/^\d{1,10}(?:\.\d{1,2})?$/D', 'numeric', 'gt:0'],
            'stories' => ['required', 'integer', 'min:1', 'max:255'],
            'nte' => ['nullable', 'regex:/^\d{1,12}(?:\.\d{1,2})?$/D'],
            'priority' => ['required', Rule::in(RepairRequest::PRIORITIES)],
            'descriptions' => ['required', 'list', 'array', 'min:1', 'max:9'],
            'descriptions.*' => ['required', 'array:trade,description'],
            'descriptions.*.trade' => ['required', 'distinct', Rule::in(RepairRequest::TRADES)],
            'descriptions.*.description' => ['required', 'string', 'max:2000'],
            'dimensions' => ['nullable', 'list', 'array', 'max:20'],
            'dimensions.*' => ['array:label,length,width,height,area'],
            'dimensions.*.label' => ['required', 'string', 'max:100'],
            'dimensions.*.length' => ['nullable', 'numeric', 'gt:0', 'max:1000000'],
            'dimensions.*.width' => ['nullable', 'numeric', 'gt:0', 'max:1000000'],
            'dimensions.*.height' => ['nullable', 'numeric', 'gt:0', 'max:1000000'],
            'dimensions.*.area' => ['nullable', 'numeric', 'gt:0', 'max:100000000'],
            'photos' => ['required', 'list', 'array', 'min:2', 'max:14'],
            'photos.*' => ['required', 'file', 'image', 'mimetypes:'.implode(',', RepairRequest::PHOTO_MIMES), 'max:5120'],
            'photo_trades' => ['nullable', 'list', 'array', 'max:14'],
            'photo_trades.*' => ['nullable', Rule::in(RepairRequest::TRADES)],
            'documents' => ['nullable', 'list', 'array', 'max:6'],
            'documents.*' => ['required', 'file', 'mimetypes:'.implode(',', RepairRequest::DOCUMENT_MIMES), 'max:10240'],
            'consent' => ['accepted'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator) {
            $bytes = 0;
            foreach (['photos', 'documents'] as $key) {
                $files = $this->file($key, []);
                foreach (is_array($files) ? $files : [] as $file) {
                    if ($file instanceof \Illuminate\Http\UploadedFile && $file->isValid()) {
                        $bytes += $file->getSize();
                    }
                }
            }
            if ($bytes > 12 * 1024 * 1024) {
                $validator->errors()->add('photos', 'Photos and supporting files can total up to 12 MB.');
            }

            $dimensions = $this->input('dimensions', []);
            foreach (is_array($dimensions) ? $dimensions : [] as $i => $area) {
                if (is_array($area) && blank($area['area'] ?? null) && (blank($area['length'] ?? null) || blank($area['width'] ?? null))) {
                    $validator->errors()->add("dimensions.{$i}.area", 'Enter an area or both length and width.');
                }
            }
        }];
    }

    public function messages(): array
    {
        return [
            'photos.required' => 'Add at least two photos.',
            'photos.min' => 'Add at least two photos.',
            'photos.*.mimetypes' => 'Photos must be JPEG, PNG or WebP.',
            'home_sqft.*' => 'Enter the total home square footage.',
            'zip.regex' => 'Enter a five-digit ZIP or ZIP+4.',
        ];
    }
}
