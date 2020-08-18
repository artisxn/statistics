<?php

declare(strict_types=1);

namespace codicastudio\Statistics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use codicastudio\Support\Traits\ValidatingTrait;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Datum extends Model
{
    use ValidatingTrait;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'session_id',
        'user_id',
        'user_type',
        'status_code',
        'uri',
        'method',
        'server',
        'input',
        'created_at',
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'session_id' => 'string',
        'user_id' => 'integer',
        'user_type' => 'string',
        'status_code' => 'integer',
        'uri' => 'string',
        'method' => 'string',
        'server' => 'json',
        'input' => 'json',
        'created_at' => 'datetime',
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
        'session_id' => 'required|string',
        'user_id' => 'nullable|integer',
        'user_type' => 'nullable|string|strip_tags|max:150',
        'status_code' => 'required|integer',
        'uri' => 'required|string',
        'method' => 'required|string',
        'server' => 'required|array',
        'input' => 'nullable|array',
        'created_at' => 'required|date',
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

        $this->setTable(config('codicastudio.statistics.tables.data'));
    }

    /**
     * Get the owning user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function user(): MorphTo
    {
        return $this->morphTo('user', 'user_type', 'user_id', 'id');
    }

    /**
     * Get bookings of the given user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model   $user
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfUser(Builder $builder, Model $user): Builder
    {
        return $builder->where('user_type', $user->getMorphClass())->where('user_id', $user->getKey());
    }
}
