<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\ThemeSettings;

class CustomSiteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->has('domainName')) {
            $this->merge([
                'domain_name' => $this->domainName,
            ]);
        }
    }

    public function rules()
    {
        $rules = [
            'selectedTheme' => 'required|in:lendingnext,lendingsource,loan5000,loan10000',
            'themeSettings' => 'required|json',
            'fileIdMapping' => 'required|json',
            'title' => 'required|string|max:255',
        ];

        // Rules for create site
        if (!$this->has('siteId')) {
            $rules['domain_name'] = ['required', 'unique:sites', 'min:1', 'max:255', 'regex:/^([a-zA-Z0-9][a-zA-Z0-9-_]*\.)*[a-zA-Z0-9]*[a-zA-Z0-9-_]*[a-zA-Z0-9]+$/'];
            $rules['selectedForm'] = 'required|integer';
            $rules['selectedServer'] = 'required|integer';
        }

        // Rules for site update
        if ($this->has('siteId')) {
            $rules['siteId'] = 'required|integer|exists:sites,id';
        }

        // Rules for files
        $fileIdMapping = json_decode($this->input('fileIdMapping'), true);
        if (is_array($fileIdMapping)) {
            foreach ($fileIdMapping as $elementId => $properties) {
                if (is_array($properties)) {
                    foreach ($properties as $property => $value) {
                        $this->addFileValidationRules($value, $rules);
                    }
                } else {
                    $this->addFileValidationRules($properties, $rules);
                }
            }
        }

        return $rules;
    }

    private function addFileValidationRules($value, &$rules)
    {
        if (is_array($value)) {
            foreach ($value as $key => $fileName) {
                if (in_array($key, ['src', 'background-image']) && is_string($fileName)) {
                    $fileKey = $this->formatFileKey($fileName);
                    $rules[$fileKey] = 'file|mimes:svg,jpeg,jpg,png,ico';
                }
            }
        } elseif (is_string($value)) {
            $fileKey = $this->formatFileKey($value);
            $rules[$fileKey] = 'file|mimes:svg,jpeg,jpg,png,ico';
        }
    }

    private function formatFileKey($fileName)
    {
        return str_replace(['.', '-', ' '], '_', $fileName);
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->isValidThemeSettings()) {
                $validator->errors()->add('themeSettings', 'Invalid theme settings.');
            }

            if (!$this->isValidFileIdMapping()) {
                $validator->errors()->add('fileIdMapping', 'Invalid file ID mapping.');
            }

            $this->validateUploadedFiles($validator);
        });
    }

    protected function isValidThemeSettings()
    {
        $themeName = $this->input('selectedTheme');
        $themeSettings = json_decode($this->input('themeSettings'), true);
        $fileIdMapping = json_decode($this->input('fileIdMapping'), true);

        $themeService = new ThemeSettings();
        $defaultSettings = $themeService->getSettings($themeName);

        $errors = array_merge(
            $this->validateThemeSettingsKeys($defaultSettings, $themeSettings),
            $this->validateColorFormats($themeSettings),
            $this->validateFileIdMappingKeys($defaultSettings, $fileIdMapping)
        );

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->validator->errors()->add('themeSettings->' . $error['key'], $error['message']);
            }
            return false;
        }

        return true;
    }

    protected function validateThemeSettingsKeys($defaultSettings, $themeSettings)
    {
        $errors = [];

        foreach (['general', 'elements'] as $section) {
            if (!isset($themeSettings[$section])) {
                $errors[] = ['key' => $section, 'message' => "$section section is missing in themeSettings"];
                continue;
            }

            foreach ($defaultSettings[$section] as $key => $value) {
                if (!isset($themeSettings[$section][$key])) {
                    $errors[] = ['key' => "$section->$key", 'message' => "Key $key is missing in $section section of themeSettings"];
                }
            }
        }

        return $errors;
    }

    protected function validateColorFormats($themeSettings)
    {
        $errors = [];
        foreach ($themeSettings as $section => $settings) {
            $errors = array_merge($errors, $this->checkColorFormatsRecursive($settings, $section));
        }
        return $errors;
    }

    protected function checkColorFormatsRecursive($settings, $path)
    {
        $errors = [];
        foreach ($settings as $key => $value) {
            if (is_array($value)) {
                $errors = array_merge($errors, $this->checkColorFormatsRecursive($value, "$path->$key"));
            } else {
                if (in_array($key, ['color', 'background-color', 'value']) && !$this->isValidColorFormat($value)) {
                    $errors[] = ['key' => "$path->$key", 'message' => 'Invalid color format'];
                }
            }
        }
        return $errors;
    }

    protected function validateFileIdMappingKeys($defaultSettings, $fileIdMapping)
    {
        $errors = [];

        foreach ($fileIdMapping as $elementId => $properties) {
            $section = ($elementId === 'favicon') ? 'general' : 'elements';

            if (!isset($defaultSettings[$section][$elementId])) {
                $errors[] = ['key' => "$section->$elementId", 'message' => "Key $elementId is missing in $section section of defaultSettings"];
            }
        }

        return $errors;
    }

    protected function isValidColorFormat($color)
    {
        if (!is_string($color)) {
            return false;
        }
        // Allow  #rgb, #rgba, #rrggbb, #rrggbbaa, 'transparent', empty string
        return preg_match('/^#(?:[0-9a-fA-F]{3}){1,2}(?:[0-9a-fA-F]{2})?$|^transparent$|^$/', $color);
    }

    protected function isValidFileIdMapping()
    {
        return true;
    }

    protected function validateUploadedFiles($validator)
    {
        $fileIdMapping = json_decode($this->input('fileIdMapping'), true);
        $this->checkFilesRecursive($fileIdMapping, $validator, 'fileIdMapping');
    }
    
    protected function checkFilesRecursive($mapping, $validator, $path)
    {
        foreach ($mapping as $key => $value) {
            $newPath = $path . '->' . $key;
    
            if (is_array($value)) {
                $this->checkFilesRecursive($value, $validator, $newPath);
            } else {
                $fileKey = str_replace([' ', '.'], '_', $value);
                if (!$this->isValidFileName($fileKey)) {
                    $validator->errors()->add($newPath, 'Invalid file name ' . $fileKey . '. Only letters, numbers, spaces, underscores, and parentheses are allowed.');
                } else if (!$this->hasFile($fileKey)) {
                    $validator->errors()->add($newPath, 'The file ' . $fileKey . ' is required.');
                } else {
                    $this->validateFileType($fileKey, $validator, $newPath);
                }
            }
        }
    }
    
    protected function isValidFileName($fileName)
    {
        return preg_match('/^[A-Za-z0-9- _()]+$/', $fileName);
    }
    

    protected function validateFileType($fileKey, $validator, $jsonPath)
    {
        if (!$this->file($fileKey)->isValid() || !in_array($this->file($fileKey)->getClientOriginalExtension(), ['svg', 'ico', 'jpeg', 'jpg', 'png'])) {
            $validator->errors()->add($jsonPath, 'Invalid file type for ' . $fileKey . '. Only svg, jpeg, jpg, and png are allowed.');
        }
    }

}
