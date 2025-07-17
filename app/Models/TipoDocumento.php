<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    use HasFactory;
    protected $table = 'tipos_documento';

    protected $fillable = ['nombre'];

    public function pacientes()
    {
        return $this->hasMany(Paciente::class);
    }
}