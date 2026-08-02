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
            $request->validated()
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
            $request->validated()
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