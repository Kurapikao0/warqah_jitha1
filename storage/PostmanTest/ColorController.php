<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\Color;
use App\Services\ColorService;
use App\Http\Controllers\Controller;
use App\Http\Resources\ColorResource;
use App\Http\Requests\Color\StoreColorRequest;
use App\Http\Requests\Color\UpdateColorRequest;

class ColorController extends Controller
{
    public function __construct(
        protected ColorService $service
    ) {
    }

    public function index()
    {
        $this->authorize('viewAny', Color::class);

        return ColorResource::collection(
            $this->service->all()
        );
    }

    public function store(StoreColorRequest $request)
    {
        $this->authorize('create', Color::class);

        $color = $this->service->create(
            $request->validated()
        );

        return response()->json([
            'message' => 'Color created successfully.',
            'data' => new ColorResource($color),
        ], 201);
    }

    public function show(Color $color)
    {
        $this->authorize('view', $color);

        return new ColorResource($color);
    }

    public function update(
        UpdateColorRequest $request,
        Color $color
    ) {
        $this->authorize('update', $color);

        $this->service->update(
            $color,
            $request->validated()
        );

        return response()->json([
            'message' => 'Color updated successfully.',
            'data' => new ColorResource(
                $color->fresh()
            ),
        ]);
    }

    public function destroy(Color $color)
    {
        $this->authorize('delete', $color);

        $this->service->delete($color);

        return response()->json([
            'message' => 'Color deleted successfully.',
        ]);
    }
}