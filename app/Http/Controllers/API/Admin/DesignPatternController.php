<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\DesignPattern;
use App\Services\DesignPatternService;
use App\Http\Controllers\Controller;
use App\Http\Resources\DesignPatternResource;
use App\Http\Requests\DesignPattern\StoreDesignPatternRequest;
use App\Http\Requests\DesignPattern\UpdateDesignPatternRequest;

class DesignPatternController extends Controller
{
    public function __construct(
        protected DesignPatternService $service
    ) {
    }

    protected function normalizePayload(array $data, $request): array
    {
        if ($request->hasFile('image')) {
            $data['preview_image_url'] = $request->file('image')->store('design-patterns', 'public');
        }

        if (isset($data['image_url']) && ! isset($data['preview_image_url'])) {
            $data['preview_image_url'] = $data['image_url'];
        }

        if (isset($data['preview_image_url']) && ! isset($data['image_url'])) {
            $data['image_url'] = $data['preview_image_url'];
        }

        unset($data['image']);

        return $data;
    }

    public function index()
    {
        $this->authorize('viewAny', DesignPattern::class);

        return DesignPatternResource::collection(
            $this->service->all()
        );
    }

    public function store(
        StoreDesignPatternRequest $request
    ) {
        $this->authorize('create', DesignPattern::class);

        $designPattern = $this->service->create(
            $this->normalizePayload($request->validated(), $request)
        );

        return response()->json([
            'message' => 'Design pattern created successfully.',
            'data' => new DesignPatternResource($designPattern),
        ], 201);
    }

    public function show(
        DesignPattern $designPattern
    ) {
        $this->authorize('view', $designPattern);

        return new DesignPatternResource($designPattern);
    }

    public function update(
        UpdateDesignPatternRequest $request,
        DesignPattern $designPattern
    ) {
        $this->authorize('update', $designPattern);

        $this->service->update(
            $designPattern,
            $this->normalizePayload($request->validated(), $request)
        );

        return response()->json([
            'message' => 'Design pattern updated successfully.',
            'data' => new DesignPatternResource(
                $designPattern->fresh()
            ),
        ]);
    }

    public function destroy(
        DesignPattern $designPattern
    ) {
        $this->authorize('delete', $designPattern);

        $this->service->delete($designPattern);

        return response()->json([
            'message' => 'Design pattern deleted successfully.',
        ]);
    }
}
