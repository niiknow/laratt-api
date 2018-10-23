<?php

namespace Api\Models;

use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;
use Api\Models\Traits\HasUuid;
use Api\Extra\FileManager;
use Api\Models\Traits\HasSpace;

class Recipe extends Model
{
    use Userstamps,
        HasSpace,
        HasUuid;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'image_url',
        'description',
        'recipe_ingredient',
        'recipe_instructions',
        'keywords',
        'recipe_cuisine',
        'recipe_category',
        'recipe_yield',
        'recipe_diet',

        'rating_value',
        'rating_count',
        'review_count',
        'rating_at',

        'prep_time',
        'cook_time',
        'rest_time',
        'total_time',

        'author_name',
        'date_published',

        'skill_level',
        'recipe_tip',
        'recipe_note',
        'cook_method',
        'serving_size',
        'nutrition',
        'legacy_id',
        'export_id',
        'export_to',
        'export_rating_value',
        'export_rating_at',
        'export_category1',
        'export_category2',
        'export_category3',
        'export_category4',

        'src_recipe_id',
        'src_image_url',
        'src_url'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'rating_count'            => 'integer',
        'review_count'            => 'integer',
        'cook_time'               => 'integer',
        'prep_time'               => 'integer',
        'total_time'              => 'integer',
        'skill_level'             => 'integer',
        'nutrition'               => 'object'
    ];

    /**
     * @var string
     */
    protected $table = 'recipe';

    /**
     * The attributes that should be casted by Carbon
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'approved_at',
        'verified_at',
        'submitted_at',
        'rating_at',
        'export_rating_at'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!isset($model->image_url)) {
                $model->image_url = $model->src_image_url;
            }
        });
    }

    public function getRatingValueAttribute($value)
    {
        if (isset($value)) {
            return number_format($value, 2);
        }

        return 0.00;
    }

    private function parseTime($value)
    {
        $time = $value;

        if (is_string($value)) {
            $value = trim(strtoupper($value));
            if ($value === 'NONE'
                || $value === 'NIL'
                || $value === '0'
                || $value === 'NULL'
                || $value === 'UNDEFINED') {
                return null;
            }

            $time = 0;
            if (preg_match('/PT(?:(\d+)H)?(\d+)M/', $value, $regs)) {
                if (!empty($regs[1])) {
                    $time = 60 * $regs[1];
                }

                $time = $time + $regs[2];
            } else {
                $time = (int) $value;
            }
        }

        return $time;
    }

    public function setPrepTimeAttribute($value)
    {
        $this->attributes['prep_time'] = $this->parseTime($value);
    }

    public function setCookTimeAttribute($value)
    {
        $this->attributes['cook_time'] = $this->parseTime($value);
    }

    public function setRestTimeAttribute($value)
    {
        $this->attributes['rest_time'] = $this->parseTime($value);
    }

    public function setTotalTimeAttribute($value)
    {
        $this->attributes['total_time'] = $this->parseTime($value);
    }

    public function getNutritionAttribute($value)
    {
        if (is_string($value)) {
            $this->setNutritionAttribute($value);
        }

        $existingValue = $this->attributes['nutrition'];
        return isset($existingValue) ? $existingValue : new Nutrition();
    }

    public function setNutritionAttribute($value)
    {
        if (!isset($this->attributes['nutrition'])) {
            $this->attributes['nutrition'] = new Nutrition();
        }

        $currentValue = $this->attributes['nutrition'];
        if (is_string($currentValue)) {
            $this->attributes['nutrition'] = new Nutrition(json_decode($currentValue, true));
        }

        if (is_array($value)) {
            $this->attributes['nutrition']->fill($value);
        } elseif (is_object($value)) {
            $this->attributes['nutrition'] = $value;
        } elseif (is_string($value)) {
            $this->attributes['nutrition'] = new Nutrition(json_decode($value, true));
        }
    }

    public function setDescriptionAttribute($value)
    {
        if (isset($value)) {
            // no html in description
            $value = strip_tags($value);
            $value = str_replace('&amp;', '&', $value);
            $value = str_replace(array('&nbsp;', '&#160;'), array(' ', ' '), $value);
            $value = preg_replace('/\xC2\xA0/', ' ', $value);
            $value = preg_replace('/\r/', '', $value);
            $value = preg_replace('/\s+/', ' ', $value);

            // store html in raw format
            $value = htmlspecialchars_decode($value);
        }

        $this->attributes['description'] = $value;
    }

    public function getTextBetweenTags($string, $tagname)
    {
        preg_match_all("'<$tagname>(.*?)</$tagname>'si", $string, $match);
        if ($match) {
            // \Log::info($match);
            return $match[1];
        }
        return [];
    }

    public function parseStringArray($value)
    {
        if (is_array($value)) {
            // store as string if passed in as array
            $value = implode("\n", $value);
        } else {
            // you are allow HTML on each line, but not multi-line html
            $noHtml = strip_tags($value);

            // if there are html in string
            if (strlen($noHtml) < strlen($value)) {
                // loop through all lines
                $lines = explode("\n", $value);

                // should not have empty line
                $hasEmptyLine = false;
                foreach ($lines as $line) {
                    $cleanLine = trim(strip_tags($line));
                    if (strlen($cleanLine) <= 0) {
                        $hasEmptyLine = true;
                        break;
                    }
                }

                if ($hasEmptyLine === true) {
                    // punish user entering bad html on the line
                    // put everything into one line
                    $value = str_replace(array('&nbsp;', '&#160;'), array(' ', ' '), $value);
                    $value = preg_replace('/\xC2\xA0/', ' ', $value);
                    $value = preg_replace('/\s+/', ' ', $value);
                    $value = trim($value);
                }
            }
        }

        if (isset($value)) {
            // store html in raw format
            $value = preg_replace('/\r/', '', $value);
            $value = preg_replace('/\n+/', "\n", $value);
            $value = preg_replace('/\|\n/', "\n", $value);
            $value = str_replace('&amp;', '&', $value);
            $value = htmlspecialchars_decode($value);
        }

        return isset($value) ? trim($value) : null;
    }

    public function setRecipeIngredientAttribute($value)
    {
        $this->attributes['recipe_ingredient'] = $this->parseStringArray($value);
    }

    public function setRecipeInstructionsAttribute($value)
    {
        // this is to check if there is a new line character in the file
        /*if (strpos('\n', $value) !== false) {
            throw new GeneralException('testing');
        }*/

        $this->attributes['recipe_instructions'] = $this->parseStringArray($value);
    }

    public function parseDate($value) {
        if (is_string($value)) {
            $value = trim($value);
            if (empty($value)) {
                $value = null;
            }

            try {
                $value = \Carbon\Carbon::parse($value);
            } catch (\Exception $err) {
                $value = null;
            }
        }

        return $value;
    }

    public function setRatingAtAttribute($value)
    {
        $this->attributes['rating_at'] = $this->parseDate($value);
    }

    public function setExportRatingAtAttribute($value)
    {
        $this->attributes['export_rating_at'] = $this->parseDate($value);
    }

    public function syncRecipeImage()
    {
        if (!isset($this->image_url)) {
            $this->image_url = $this->src_image_url;
        }

        if (strpos($this->image_url, 'brickinc.net') === false) {
            $uploader = new FileManager(
                's3-legacy',
                config('admin.upload.path.recipe', 'recipe') . '/' . $this->space_id
            );

            // handle all recipes image resize here
            $url  = str_replace('/560x315/', '/', $this->image_url);
            $name = null;

            if (isset($this->export_id)) {
                $info = pathinfo(strtolower($url));
                $ext  = $info['extension'];
                $name = str_slug($this->export_id) . '.' . $ext;
            }

            $rst = $uploader->uploadFromUrl($url, $name);

            $this->image_url = $rst['url'];
            $this->save();
        }

        return $this;
    }
}
