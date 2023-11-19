<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ChatSettings
 *
 * @property int $id
 * @property int $user_id
 * @property string $model
 * @property float $temperature
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ChatSettings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatSettings newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatSettings query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatSettings whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatSettings whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatSettings whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatSettings whereTemperature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatSettings whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatSettings whereUserId($value)
 * @mixin \Eloquent
 */
class ChatSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'model',
        'temperature',
    ];
}
