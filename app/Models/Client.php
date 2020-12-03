<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Database\Eloquent\Model;
  use Illuminate\Database\Eloquent\Relations\HasMany;
  use Illuminate\Database\Eloquent\SoftDeletes;

  class Client extends Model
  {
    use HasFactory;
    use SoftDeletes;

    /**
     * All counterparties
     *
     * @return HasMany
     */
    public function counterparties(): HasMany
    {
      return $this->hasMany(Counterparty::class);
    }
  }
