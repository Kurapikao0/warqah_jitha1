<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryRepository implements CategoryRepositoryInterface
{


    public function getAll(
        array $filters = []
    ): LengthAwarePaginator
    {

        return Category::query()

            ->with([
                'products'
            ])


            ->when(
                isset($filters['search']),
                function($query) use($filters){

                    $search = $filters['search'];

                    $query->where(function($q) use($search){

                        $q->where(
                            'name',
                            'LIKE',
                            "%{$search}%"
                        );


                    });

                }
            )


            ->when(
                isset($filters['status']),
                function($query) use($filters){

                    $query->where(
                        'status',
                        $filters['status']
                    );

                }
            )


            ->orderBy(
                $filters['sort_by'] ?? 'created_at',
                $filters['sort_direction'] ?? 'desc'
            )


            ->paginate(
                $filters['per_page'] ?? 15
            );

    }





    public function findById(
        int $id
    ): ?Category
    {

        return Category::with([
            'products'
        ])
        ->find($id);

    }





    public function create(
        array $data
    ): Category
    {

        return Category::create($data);

    }





    public function update(
        Category $category,
        array $data
    ): Category
    {

        $category->update($data);


        return $category->fresh();

    }





    public function delete(
        Category $category
    ): bool
    {

        return $category->delete();

    }





    public function restore(
        Category $category
    ): bool
    {

        return $category->restore();

    }





    public function changeStatus(
        Category $category,
        string $status
    ): Category
    {

        $category->update([

            'status'=>$status

        ]);


        return $category->fresh();

    }





    public function loadRelations(
        Category $category
    ): Category
    {

        return $category->load([

            'products'

        ]);

    }


}