<?php

namespace App\Livewire\Servicios;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Servicios\Inventario;
use App\Models\Servicios\Equipo;
use App\Models\Servicios\Area;
use App\Models\Servicios\Garantia;
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
    public $nombre, $marca, $modelo, $frecuencia_mantenimiento = 6;
    public $num_serie, $num_serie_sicopa, $num_serie_sia;
    public $pertenencia = 'propia', $status = 'funcionando', $id_area_fk, $id_garantia_fk;

    public $showNewWarrantyFields = false;
    public $nueva_garantia_status = 'activa', $nueva_garantia_fecha, $nueva_garantia_empresa, $nueva_garantia_contacto;

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:100',
            'frecuencia_mantenimiento' => 'required|integer',
            'id_area_fk' => 'required|exists:areas,id_area',
        ];
    }

    public function render()
    {
        $inventario = Inventario::with(['equipo', 'area', 'garantia'])
            ->whereHas('equipo', function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%');
            })
            ->orWhere('num_serie', 'like', '%' . $this->search . '%')
            ->orWhere('num_serie_sicopa', 'like', '%' . $this->search . '%')
            ->orWhere('num_serie_sia', 'like', '%' . $this->search . '%')
            ->orderBy('id_inventario', 'desc')
            ->paginate(10);

        return view('livewire.servicios.gestion-inventario', [
            'inventario' => $inventario,
            'areas' => Area::orderBy('nombre')->get(),
            'garantias' => Garantia::all(),
        ]);
    }

    public function actualizarStatus($inventarioId, $nuevoStatus)
    {
        $inventario = Inventario::find($inventarioId);
        if ($inventario && $inventario->status !== 'baja') {
            Inventario::where('id_inventario', $inventarioId)->update(['status' => $nuevoStatus]);
            session()->flash('mensaje', 'Estado del equipo actualizado correctamente.');
        } else if ($inventario && $inventario->status === 'baja') {
            session()->flash('mensaje', 'Este equipo ya ha sido dado de baja y su estado no puede ser modificado.');
            $this->js('window.location.reload()');
        }
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
        $this->frecuencia_mantenimiento = $inventario->equipo->frecuencia_mantenimiento;

        $this->num_serie = $inventario->num_serie;
        // 👇 ¡AQUÍ ESTÁ EL CAMBIO! Se cargan los números de serie SIA y SICOPA.
        $this->num_serie_sicopa = $inventario->num_serie_sicopa;
        $this->num_serie_sia = $inventario->num_serie_sia;

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
            } else {
                $garantiaId = empty($garantiaId) ? null : $garantiaId;
            }

            $equipo = Equipo::updateOrCreate(
                ['id_equipo' => $this->equipoId],
                [
                    'nombre' => $this->nombre,
                    'marca' => $this->marca,
                    'modelo' => $this->modelo,
                    'frecuencia_mantenimiento' => $this->frecuencia_mantenimiento,
                ]
            );

            Inventario::updateOrCreate(
                ['id_inventario' => $this->inventarioId],
                [
                    'id_equipo_fk' => $equipo->id_equipo,
                    'num_serie' => $this->num_serie,
                    'num_serie_sicopa' => $this->num_serie_sicopa,
                    'num_serie_sia' => $this->num_serie_sia,
                    'pertenencia' => $this->pertenencia,
                    'status' => $this->status,
                    'id_area_fk' => $this->id_area_fk,
                    'id_garantia_fk' => $garantiaId,
                ]
            );
        });

        session()->flash('mensaje', $this->isEditMode ? 'Equipo actualizado en el inventario.' : 'Equipo agregado al inventario.');
        $this->closeModal();
    }

    public function registrarMantenimiento($inventarioId)
    {
        return redirect()->to(route('servicios.mantenimiento') . '?equipo_id=' . $inventarioId . '&abrir_modal=true');
    }

    public function registrarBaja($inventarioId)
    {
        return redirect()->to(route('servicios.bajas') . '?equipo_id=' . $inventarioId . '&abrir_modal=true');
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
