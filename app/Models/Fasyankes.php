<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fasyankes extends Model
{
    use HasFactory;

    protected $table = 'fasyankes';
    protected $primaryKey = 'fasyankesId';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'fasyankesId',
        'bisnis_owner_id',
        'type',
        'warehouse_id',
        'name',
        'address',
        'pic',
        'pic_number',
        'email',
        'is_active',
        'latitude',
        'longitude',
        'province',
        'city',
        'subdistrict',
        'village'
    ];
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function bisnis_owner()
    {
        return $this->belongsTo(BisnisOwner::class);
    }

    public function access_fasyankes()
    {
        return $this->hasMany(AccessFasyankes::class);
    }

    public function legal_doc()
    {
        return $this->hasOne(LegalDocFasyankes::class, 'fasyankes_id', 'fasyankesId');
    }

    public function subscription_plan()
    {
        return $this->hasOne(SubscriptionPlan::class, 'fasyankes_id', 'fasyankesId')
            ->select('package_plan', 'duration', 'id', 'fasyankes_id')
            ->orderBy('created_at', 'DESC');
    }
    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'password',
    ];
}
