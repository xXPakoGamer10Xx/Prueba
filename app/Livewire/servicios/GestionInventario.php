<?php

namespace App\Livewire\servicios;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\servicios\Inventario;
use App\Models\servicios\Equipo;
use App\Models\servicios\Area;
use App\Models\servicios\Garantia;
use Illuminate\Support\Facades\DB;

class GestionInventario extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $search = '';

    // Propiedades para el modal
    public $isModalOpen = false;
    public $isEditMode = false;
    public $inventarioId, $equipoId;

    // Propiedades del formulario
    public $nombre, $marca, $modelo, $cantidad, $frecuencia_mantenimiento = 'semestral';
    public $num_serie, $num_serie_sicopa, $num_serie_sia;
    public $pertenencia = 'propia', $status = 'funcionando', $id_area_fk, $id_garantia_fk;
    
    public $showNewWarrantyFields = false;
    public $nueva_garantia_status = 'activa', $nueva_garantia_fecha, $nueva_garantia_empresa, $nueva_garantia_contacto;

    protected function rules()
    {
        $rules = [
            'nombre' => 'required|string|max:100',
            'cantidad' => 'required|integer|min:1',
            'frecuencia_mantenimiento' => 'required|string',
            'id_area_fk' => 'required|exists:areas,id_area',
        ];

        if ($this->showNewWarrantyFields && $this->nueva_garantia_status === 'activa') {
            $rules['nueva_garantia_fecha'] = 'required|date';
            $rules['nueva_garantia_empresa'] = 'required|string|max:100';
        }

        return $rules;
    }
    
    public function render()
    {
        $inventario = Inventario::with(['equipo', 'area', 'garantia'])
            ->whereHas('equipo', function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%');
            })
            ->orWhere('num_serie', 'like', '%' . $this->search . '%')
            ->orderBy('id_inventario', 'desc')
            ->paginate(10);

        return view('livewire.servicios.gestion-inventario', [
            'inventario' => $inventario,
            'areas' => Area::orderBy('nombre')->get(),
            'garantias' => Garantia::all(),
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isEditMode = false;
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $inventario = Inventario::with('equipo')->findOrFail($id);
        $this->inventarioId = $inventario->id_inventario;
        $this->equipoId = $inventario->id_equipo_fk;

        $this->nombre = $inventario->equipo->nombre;
        $this->marca = $inventario->equipo->marca;
        $this->modelo = $inventario->equipo->modelo;
        $this->cantidad = $inventario->equipo->cantidad;

        $this->frecuencia_mantenimiento = match ($inventario->equipo->frecuencia_mantenimiento) {
            1 => 'mensual',
            3 => 'trimestral',
            6 => 'semestral',
            12 => 'anual',
            default => 'semestral',
        };

        $this->num_serie = $inventario->num_serie;
        $this->pertenencia = $inventario->pertenencia;
        $this->status = $inventario->status;
        $this->id_area_fk = $inventario->id_area_fk;
        $this->id_garantia_fk = $inventario->id_garantia_fk;

        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate();

        DB::transaction(function () {
            $garantiaId = $this->id_garantia_fk;
            
            if ($this->id_garantia_fk === 'new_warranty') {
                $garantia = Garantia::create([
                    'status' => $this->nueva_garantia_status,
                    'fecha_garantia' => $this->nueva_garantia_status === 'activa' ? $this->nueva_garantia_fecha : null,
                    'empresa' => $this->nueva_garantia_status === 'activa' ? $this->nueva_garantia_empresa : null,
                    'contacto' => $this->nueva_garantia_status === 'activa' ? $this->nueva_garantia_contacto : null,
                ]);
                $garantiaId = $garantia->id_garantia;
            }

            $frecuenciaEnMeses = match($this->frecuencia_mantenimiento) {
                'mensual' => 1,
                'trimestral' => 3,
                'semestral' => 6,
                'anual' => 12,
                default => 6,
            };

            $equipo = Equipo::updateOrCreate(
                ['id_equipo' => $this->equipoId],
                [
                    'nombre' => $this->nombre,
                    'marca' => $this->marca,
                    'modelo' => $this->modelo,
                    'cantidad' => $this->cantidad,
                    'frecuencia_mantenimiento' => $frecuenciaEnMeses,
                ]
            );

            Inventario::updateOrCreate(
                ['id_inventario' => $this->inventarioId],
                [
                    'id_equipo_fk' => $equipo->id_equipo,
                    'num_serie' => $this->num_serie,
                    'pertenencia' => $this->pertenencia,
                    'status' => $this->status,
                    'id_area_fk' => $this->id_area_fk,
                    // CORRECCIÓN: Si $garantiaId es una cadena vacía (por la opción "Sin garantía"),
                    // se convierte a null para que sea aceptado por la base de datos.
                    'id_garantia_fk' => $garantiaId === '' ? null : $garantiaId,
                ]
            );
        });

        session()->flash('mensaje', $this->isEditMode ? 'Equipo actualizado.' : 'Equipo agregado.');
        $this->closeModal();
    }
    
    public function registrarMantenimiento($inventarioId)
    {
        return redirect()->route('servicios.mantenimiento', ['inventario' => $inventarioId]);
    }

    public function registrarBaja($inventarioId)
    {
        return redirect()->route('servicios.bajas', ['inventario' => $inventarioId]);
    }
    
    public function updatedIdGarantiaFk($value)
    {
        $this->showNewWarrantyFields = ($value === 'new_warranty');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->reset();
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
}
