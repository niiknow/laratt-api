<?php

namespace Api\Controllers;

use League\Csv\Reader;
use App\Exceptions\GeneralException;
use Api\Controllers\Controller;
use Api\Models\Recipe;
use Api\Extra\RequestSearchQuery;
use Illuminate\Http\Request;
use Api\Controllers\Traits\SpaceResourceTrait;

use App\Jobs\ProcessRecipeImageUrl;
use Validator;
use Api\Extra\FileManager;

class RecipeController extends Controller
{
    public function destroy(Recipe $model)
    {
        if (!$model->delete()) {
            throw new GeneralException(__('exceptions.recipe.delete'));
        }
    }

    public function index(Request $request)
    {
        $requestSearchQuery = new RequestSearchQuery($request, Recipe::where('space_id', $this->spaceId()), [
            'id',
            'name',
            'description',
            'recipe_ingredient',
            'recipe_instructions',
            'keywords',
            'recipe_cuisine',
            'recipe_category',
            'recipe_yield',
            'author_name',
            'recipe_tip',
            'recipe_note',
            'cook_method',
            'legacy_id',
            'export_id',
            'export_to',
            'export_category1',
            'export_category2',
            'export_category3',
            'export_category4',
            'src_recipe_id',
            'src_image_url',
            'src_url'
        ]);

        if ($request->get('exportData')) {
            $currentDate = date('dmY-His');

            return \Maatwebsite\Excel\Facades\Excel::download(
                new \Api\Extra\RecipeTableExport(
                    $requestSearchQuery->query
                ),
                "recipes-export-$currentDate.csv"
            );
        }

        return $requestSearchQuery->result();
    }

    public function restore($id)
    {
        // Recipe::withTrashed()->findOrFail($id)->restore();
        // do nothing, recipe is permentally delete
    }

    public function store(Request $request)
    {
        $data = $this->validate($request, $this->createValidation());

        $item           = new Recipe($data);
        $item->space_id = $this->spaceId();

        // must save before sync image can be called
        if (!$item->save()) {
            throw new GeneralException(__('exceptions.recipe.create'));
        }

        ProcessRecipeImageUrl::dispatch($item);

        return $item;
    }

    public function show(Recipe $model)
    {
        return $model;
    }

    public function update(Request $request, Recipe $model)
    {
        // $this->authorize('update', $model);
        $data = $this->validate($request, $this->createValidation());
        $model->fill($data);

        // model space id should match with query string
        if ($model->space_id !== $this->spaceId()) {
            throw new GeneralException(__('exceptions.recipe.update'));
        }

        if (!$model->save()) {
            throw new GeneralException(__('exceptions.recipe.update'));
        }

        ProcessRecipeImageUrl::dispatch($model);

        return $model;
    }

    public function bulk(Request $request)
    {
        $this->validate($request, [
            'action' => 'required',
            'selected' => 'required'
        ]);

        // do nothing for now
        $ids = $request->input('selected');

        // use switch statement to call different method
        $action = $request->input('action');
    }

    public function image(Request $request)
    {
        $space_id = $this->spaceId();
        $uploader = new FileManager(
            's3-legacy',
            config('admin.upload.path.recipe', 'recipe') . '/' . $space_id
        );
        return $uploader->uploadFromRequest($request, 'file');
    }

    public function validateCsv($csv)
    {
        // pre-validate
        foreach ($csv as $row) {
            $array = array();

            // undot the csv array
            foreach ($row as $key => $value) {
                array_set($array, $key, $value);
            }

            // validate data
            Validator::make($array, $this->createValidation(true))
                ->validate();
        }
    }

    public function importRow($row)
    {
        $space_id = $this->spaceId();
        $array    = array();

        // undot the csv array
        foreach ($row as $key => $value) {
            $cell = $value;
            if (!is_string($cell)) {
                $cell = (string)$cell;
            }

            if ($cell === '' || $cell === 'null') {
                $cell = null;
            } elseif (is_numeric($cell)) {
                $cell = $cell + 0;
            }

            array_set($array, $key, $cell);
        }

        // validate data
        $data = Validator::make($array, $this->createValidation(true))
            ->validate();

        $item           = new Recipe($data);
        $item->space_id = $space_id;

        if (!$item->save()) {
            throw new GeneralException(__('exceptions.recipe.create'));
        }

        // create recipe image sync job
        ProcessRecipeImageUrl::dispatch($item);
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'import' => 'required',
        ]);

        $csv = Reader::createFromFileObject($request->file('import')->openFile())->setHeaderOffset(0);

        $self = $this;

        // wrap import in a transaction
        \DB::transaction(function () use ($csv, $self) {
            foreach ($csv as $row) {
                $self->importRow($row);
            }
        });
    }

    public function createValidation($forImport = false)
    {
        $rst = [
            'name'                    => 'required|max:190',
            'image_url'               => 'nullable|url|max:190',
            'recipe_instructions'     => 'required',
            'description'             => 'nullable',
            'recipe_ingredient'       => 'nullable',
            'keywords'                => 'nullable|string|max:190',
            'recipe_cuisine'          => 'nullable|string|max:190',
            'recipe_category'         => 'nullable|string|max:190',
            'recipe_diet'             => 'nullable|string|max:190',
            'recipe_yield'            => 'nullable',

            'rating_value'            => 'nullable|numeric|between:0,5',
            'rating_count'            => 'nullable|integer',
            'review_count'            => 'nullable|integer',
            'rating_at'               => 'nullable|date',

            'prep_time'               => 'nullable',
            'cook_time'               => 'nullable',
            'rest_time'               => 'nullable',
            'total_time'              => 'nullable',

            'author_name'             => 'nullable|string|max:190',
            'date_published'          => 'nullable|string|max:190',

            'skill_level'             => 'nullable|integer|between:1,5',
            'cook_method'             => 'nullable|string|max:190',
            'recipe_tip'              => 'nullable',
            'recipe_note'             => 'nullable',
            'serving_size'            => 'nullable|string|max:190',
            'legacy_id'               => 'nullable|string|max:190',
            'export_id'               => 'nullable',
            'export_to'               => 'nullable|string|max:190',
            'export_rating_value'     => 'nullable|numeric|between:0,5',
            'export_rating_at'        => 'nullable|date',
            'export_category1'        => 'nullable|string|max:190',
            'export_category2'        => 'nullable|string|max:190',
            'export_category3'        => 'nullable|string|max:190',
            'export_category4'        => 'nullable|string|max:190',

            'src_recipe_id'           => 'nullable',
            'src_url'                 => 'nullable|url|max:190',
            'src_image_url'           => 'nullable|url|max:190',
            'nutrition.*'             => 'nullable|numeric',
        ];

        if ($forImport === true) {
            $rst['src_image_url'] = 'nullable|required|url|max:190';
        } else {
            $rst['image_url'] = 'nullable|required|url|max:190';
        }

        return $rst;
    }
}
