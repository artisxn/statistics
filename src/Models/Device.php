<?php

declare(strict_types=1);

namespace codicastudio\Statistics\Models;

use Illuminate\Database\Eloquent\Model;
use codicastudio\Support\Traits\ValidatingTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    use ValidatingTrait;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'family',
        'model',
        'brand',
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'family' => 'string',
        'model' => 'string',
        'brand' => 'string',
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
        'family' => 'required|string',
        'model' => 'nullable|string',
        'brand' => 'nullable|string',
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

        $this->setTable(config('codicastudio.statistics.tables.devices'));
    }

    /**
     * The device may have many requests.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requests(): HasMany
    {
        return $this->hasMany(config('codicastudio.statistics.models.request'), 'device_id', 'id');
    }
}
