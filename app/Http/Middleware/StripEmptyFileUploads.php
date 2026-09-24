<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use ReflectionClass;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;
use Symfony\Component\HttpFoundation\Response;

class StripEmptyFileUploads
{
    /**
     * Handle an incoming request.
     *
     * In HTML forms, empty <input type="file"> elements are submitted with UPLOAD_ERR_NO_FILE (4).
     * Laravel wraps these in UploadedFile objects with isValid() === false.
     * When validation rules like 'nullable|file' or 'required|file' evaluate these,
     * Laravel treats them as failed uploads and returns "The :attribute failed to upload."
     *
     * This middleware strips those empty placeholders so:
     * 1. 'nullable|file' passes cleanly when no file was chosen.
     * 2. 'required|file' triggers the proper custom 'required' error message.
     * 3. Real file uploads are preserved untouched.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('PATCH')) {
            $this->stripEmptyFiles($request);
        }

        return $next($request);
    }

    protected function stripEmptyFiles(Request $request): void
    {
        $files = $request->files->all();
        $modified = false;

        foreach ($files as $key => $file) {
            if (is_array($file)) {
                $cleaned = $this->filterEmptyFilesArray($file);
                if (empty($cleaned)) {
                    $request->files->remove($key);
                    $modified = true;
                } elseif (count($cleaned) !== count($file)) {
                    $request->files->set($key, array_values($cleaned));
                    $modified = true;
                }
            } elseif ($file instanceof SymfonyUploadedFile) {
                if ($file->getError() === UPLOAD_ERR_NO_FILE) {
                    $request->files->remove($key);
                    $modified = true;
                }
            }
        }

        if ($modified) {
            // Clear Laravel's convertedFiles cache so calls to $request->file() reflect the cleaned files
            $ref = new ReflectionClass($request);
            while ($ref) {
                if ($ref->hasProperty('convertedFiles')) {
                    $prop = $ref->getProperty('convertedFiles');
                    $prop->setAccessible(true);
                    $prop->setValue($request, null);
                    break;
                }
                $ref = $ref->getParentClass();
            }
        }
    }

    /**
     * Recursively filter empty file uploads from nested arrays.
     */
    protected function filterEmptyFilesArray(array $files): array
    {
        $result = [];
        foreach ($files as $k => $f) {
            if (is_array($f)) {
                $sub = $this->filterEmptyFilesArray($f);
                if (! empty($sub)) {
                    $result[$k] = $sub;
                }
            } elseif ($f instanceof SymfonyUploadedFile) {
                if ($f->getError() !== UPLOAD_ERR_NO_FILE) {
                    $result[$k] = $f;
                }
            }
        }

        return $result;
    }
}

