<?php

declare(strict_types=1);

namespace codicastudio\Statistics\Models;

use Illuminate\Database\Eloquent\Model;
use codicastudio\Support\Traits\ValidatingTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Path extends Model
{
    use ValidatingTrait;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'host',
        'locale',
        'path',
        'method',
        'parameters',
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'host' => 'string',
        'locale' => 'string',
        'path' => 'string',
        'method' => 'string',
        'parameters' => 'json',
    ];

    /**
     * {@inheritdoc}
     */
    public $timestamps = false;

    /**
     * {@inheritdoc}
     */
    protected $observables = [
        'validating',
        'validated',
    ];

    /**
     * The default rules that the model will validate against.
     *
     * @var array
     */
    protected $rules = [
        'host' => 'required|string',
        'locale' => 'required|string',
        'path' => 'required|string',
        'method' => 'required|string',
        'parameters' => 'nullable|array',
    ];

    /**
     * Whether the model should throw a
     * ValidationException if it fails validation.
     *
     * @var bool
     */
    protected $throwValidationExceptions = true;

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('codicastudio.statistics.tables.paths'));
    }

    /**
     * The path may have many requests.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requests(): HasMany
    {
        return $this->hasMany(config('codicastudio.statistics.models.request'), 'path_id', 'id');
    }
}
