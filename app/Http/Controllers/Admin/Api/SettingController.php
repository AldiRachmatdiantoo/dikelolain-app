<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingRequest;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    public function __construct(private SettingService $settings)
    {
    }

    public function show(string $key): JsonResponse
    {
        abort_unless($this->settings->exists($key), 404);

        return response()->json(['data' => $this->settings->fields([$key])[0]]);
    }

    public function update(UpdateSettingRequest $request, string $key): JsonResponse
    {
        $this->settings->set($key, $request->file('file') ?? $request->input('value'));

        return response()->json(['data' => $this->settings->fields([$key])[0]]);
    }
}
