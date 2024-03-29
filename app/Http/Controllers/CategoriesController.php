<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Field;
use App\Models\FieldCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class CategoriesController extends Controller
{
    public function getCategories(Request $request): Collection
    {
        return Category::orderBy('order', 'desc')->get();
    }

    /**
     * @param int $id
     * @return Category[]|array|Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getCategoriesByDepartment(int $id): \Illuminate\Database\Eloquent\Collection|array
    {
        return $this->getCategoriesTree(0, $id);
    }

    /**
     * @param int $parent
     * @param null $departmentID
     * @return \Illuminate\Database\Eloquent\Collection|array
     */
    public function getCategoriesTree(int $parent = 0, $departmentID = null): \Illuminate\Database\Eloquent\Collection|array
    {
        $categories = Category::where('parent', $parent);
        if (!is_null($departmentID)) {
            $categories->where(function (Builder $builder) use ($departmentID) {
                $builder->orWhere('department_id', $departmentID)
                    ->orWhereNull('department_id');
            });
        }
        $categories = $categories->get();
        $i = 0;
        foreach ($categories as $category) {
            $countChildren = Category::where('parent', $category->id)->count();
            if ($countChildren > 0) {
                $categories[$i]->children = $this->getCategoriesTree($category->id, $departmentID);
            }
            $i++;
        }


        return $categories;
    }

    /**
     * @param int $id
     * @return Category|Category[]|\Illuminate\Database\Eloquent\Collection|Model|null
     */
    public function getCategoryAndCreateFields(int $id): Model|\Illuminate\Database\Eloquent\Collection|array|Category|null
    {
        $category = Category::find($id);
        $this->fillWithDefaultFields($category);
        $category->refresh()->load('fields');
        return $category;
    }

    /**
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\HigherOrderCollectionProxy|mixed
     */
    public function getCategoryFields(int $id): mixed
    {
        return Category::findOrFail($id)->fields;
    }

    public function fillWithDefaultFields(Category $category)
    {
        $defaultFields = Field::selectRaw('GROUP_CONCAT(id) as ids')
            ->where('is_default', true)
            ->first()->ids;
        if (!is_null($defaultFields)) {
            foreach (explode(',', $defaultFields) as $fieldId) {
                FieldCategory::insertOrIgnore([
                    'category_id' => $category->id,
                    'field_id' => $fieldId
                ]);
            }
        }
        return $defaultFields;
    }

    /**
     * @param Request $request
     * @return Model|Category
     */
    public function createCategory(Request $request): Model|Category
    {
        return Category::create($request->all());
    }

    /**
     * @param $id
     * @param Request $request
     * @return Model|\Illuminate\Database\Eloquent\Collection|array|Category|null
     * @throws \Exception
     */
    public function saveCategory($id, Request $request): Model|\Illuminate\Database\Eloquent\Collection|array|Category|null
    {
        $category = Category::find($id);
        if ($request->get('parent') === $category->id) {
            throw new \Exception('Category parent can not be the same as category id');
        }
        if ($request->get('parent') > 0 && $category->parent === 0) {
            $makeChildParent = Category::find($request->get('parent'));
            $makeChildParent->parent = 0;
            $makeChildParent->save();
        }
        $category->update($request->all());
        return $category;
    }

    protected function hasChildren(Category $category)
    {
        return Category::where('parent', $category->id)->count('id') > 0;
    }

    /**
     * TODO проверять заявки и поля,принадлежащие к категории
     * @param int $id
     * @return bool|null
     * @throws \Exception
     */
    public function deleteCategory(int $id): ?bool
    {
        $category = Category::find($id);
        if ($this->hasChildren($category)) {
            throw new \Exception('Can not delete category if it contains children');
        }
        return $category->delete();
    }
}
