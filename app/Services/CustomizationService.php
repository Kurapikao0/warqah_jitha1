<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use App\Models\ProductCustomizationRequest;
use App\Repositories\Contracts\CustomizationRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class CustomizationService
{
    public function __construct(
        protected CustomizationRepositoryInterface $repository
    ) {}

    public function all()
    {
        return $this->repository->getAll();
    }

    public function find(int $id): ProductCustomizationRequest
    {
        return $this->repository->findById($id);
    }


    public function create(array $data): ProductCustomizationRequest
    {
        return DB::transaction(function () use ($data): ProductCustomizationRequest {
            $attributeValues = collect(
                $data['attribute_values'] ?? []
            );

            unset($data['attribute_values']);

            $product = Product::query()
                ->with('attributes')
                ->findOrFail((int) $data['base_product_id']);

            $validatedAttributeValues = $this->validateAttributeValues(
                $product,
                $attributeValues
            );

            $data['request_code'] = $data['request_code']
                ?? $this->generateRequestCode();

            $data['status'] = $data['status']
                ?? 'pending_approval';

            $customization = $this->repository->create($data);

            if ($validatedAttributeValues->isNotEmpty()) {
                $customization->attributeValues()->createMany(
                    $validatedAttributeValues
                        ->map(fn (array $item): array => [
                            'attribute_id' => $item['attribute_id'],
                            'value' => $item['value'],
                        ])
                        ->all()
                );
            }

            return $customization->load([
                'customer',
                'baseProduct',
                'color',
                'designPattern',
                'attributeValues.attribute',
            ]);
        });
    }



    public function updateStatus(
        ProductCustomizationRequest $request,
        array $data
    ): bool {
        return DB::transaction(
            fn (): bool => $this->repository->update(
                $request,
                $data
            )
        );
    }

    public function delete(
        ProductCustomizationRequest $request
    ): bool {
        return DB::transaction(
            fn (): bool => $this->repository->delete($request)
        );
    }

    private function validateAttributeValues(
        Product $product,
        Collection $attributeValues
    ): Collection {
        $productAttributes = $product->attributes->keyBy('id');

        $submittedAttributeIds = $attributeValues
            ->pluck('attribute_id')
            ->map(static fn ($id): int => (int) $id);

        $unknownAttributeId = $submittedAttributeIds->first(
            static fn (int $id): bool => ! $productAttributes->has($id)
        );

        if ($unknownAttributeId !== null) {
            throw ValidationException::withMessages([
                'attribute_values' => [
                    "Attribute {$unknownAttributeId} is not assigned to the selected product.",
                ],
            ]);
        }

        $requiredMissing = $product->attributes
            ->filter(fn ($attribute): bool => (bool) $attribute->is_required)
            ->filter(
                fn ($attribute): bool => ! $submittedAttributeIds->contains(
                    $attribute->id
                )
            );

        if ($requiredMissing->isNotEmpty()) {
            throw ValidationException::withMessages([
                'attribute_values' => [
                    'All required product attributes must be provided.',
                ],
            ]);
        }

        foreach ($attributeValues as $item) {
            $attributeId = (int) $item['attribute_id'];
            $value = trim((string) $item['value']);

            if ($value === '') {
                throw ValidationException::withMessages([
                    "attribute_values.{$attributeId}.value" => [
                        "The value for attribute {$attributeId} is required.",
                    ],
                ]);
            }

            $attribute = $productAttributes->get($attributeId);

            if (
                $attribute->input_type->value === 'select'
                && is_array($attribute->options)
                && ! in_array($value, $attribute->options, true)
            ) {
                throw ValidationException::withMessages([
                    "attribute_values.{$attributeId}.value" => [
                        "The selected value is not valid for attribute {$attribute->display_name}.",
                    ],
                ]);
            }
        }

        return $attributeValues->map(
            static fn (array $item): array => [
                'attribute_id' => (int) $item['attribute_id'],
                'value' => trim((string) $item['value']),
            ]
        );
    }


    private function generateRequestCode(): string
    {
        do {
            $code = 'REQ-'.strtoupper(Str::random(5));
        } while (
            ProductCustomizationRequest::query()
            ->where('request_code', $code)
            ->exists()
        );

    return $code;
    }

}
