<?php

declare(strict_types=1);

namespace codicastudio\Statistics\Models;

use Illuminate\Database\Eloquent\Model;
use codicastudio\Support\Traits\ValidatingTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agent extends Model
{
    use ValidatingTrait;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'name',
        'kind',
        'family',
        'version',
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'name' => 'string',
        'kind' => 'string',
        'family' => 'string',
        'version' => 'string',
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
        'name' => 'required|string|strip_tags|max:150',
        'kind' => 'required|string|strip_tags|max:150',
        'family' => 'required|string|strip_tags|max:150',
        'version' => 'nullable|string|strip_tags|max:150',
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

        $this->setTable(config('codicastudio.statistics.tables.agents'));
    }

    /**
     * The agent may have many requests.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requests(): HasMany
    {
        return $this->hasMany(config('codicastudio.statistics.models.request'), 'agent_id', 'id');
    }
}
