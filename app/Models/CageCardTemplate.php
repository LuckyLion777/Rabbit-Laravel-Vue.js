<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CageCardTemplate
 * @package App\Models
 */
class CageCardTemplate extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cage_card_templates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'size',
        'hole',
        'orientation',
        'fields'
    ];

    /**
     * The attributes that should be cast to native types.
     * @var array
     */
    protected $casts = [
        'name'   => 'string',
        'type'   => 'string',
        'size'   => 'string',
        'orientation' => 'string',
        'hole'   => 'boolean',
        'fields' => 'array'
    ];

    /**
     * Get fields.
     *
     * @param array $value
     *
     * @return array
     */
    public function getFieldsAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * Set fields.
     *
     * @param array $value
     *
     * @return array
     */
    public function setFieldsAttribute($value)
    {
        $this->attributes['fields'] = is_array($value) ? json_encode($value, true) : $value;
    }

    /**
     * Author of this cage card template.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}