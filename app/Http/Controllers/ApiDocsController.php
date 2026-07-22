<?php

namespace App\Http\Controllers;

use Symfony\Component\Yaml\Yaml;

class ApiDocsController extends Controller
{
    /**
     * Render the browsable API reference (password-gated, see
     * ApiDocsPasswordGate) generated live from openapi.yaml, so it never
     * drifts out of sync with the spec file the mobile team is given.
     */
    public function show()
    {
        $spec = Yaml::parseFile(base_path('openapi.yaml'));

        return view('api-docs.show', [
            'specJson' => json_encode($spec, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ]);
    }
}
