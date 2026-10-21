<?php

namespace App\Models\Platform;

use Illuminate\Database\Eloquent\Model;

class AiSetting extends Model
{
    protected $fillable = ['restaurant_id', 'llm_url', 'llm_key', 'llm_model', 'target_margin'];
    protected $casts = ['llm_key' => 'encrypted'];
    protected $hidden = ['llm_key'];

    public static function forRestaurant(int $id): self
    {
        return static::firstOrNew(['restaurant_id' => $id], ['target_margin' => 65]);
    }

    public function llmConfigured(): bool
    {
        return filled($this->llm_url) && filled($this->llm_key);
    }
}
