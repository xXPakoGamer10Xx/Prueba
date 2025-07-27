<?php

namespace App\Livewire\Servicios;

use App\Models\servicios\Inventario;
use App\Models\servicios\Equipo;
use App\Models\servicios\Area;
use App\Models\servicios\Garantia;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Componente de Livewire para gestionar la tabla de Inventarios.
 * Se encarga de la paginación y visualización de los registros de inventario.
 */
class GestionarInventarios extends Component
{
    use WithPagination;

    /**
     * Define el tema de paginación para que coincida con Tailwind CSS.
     *
     * @var string
     */
    protected $paginationTheme = 'tailwind';

    // Propiedades para el modal
    public $isModalOpen = false;
    public $isEditMode = false;
    public $inventarioId;
    public $num_serie;
    public $num_serie_sicopa;
    public $num_serie_sia;
    public $pertenencia;
    public $status;
    public $id_equipo_fk;
    public $id_area_fk;
    public $id_garantia_fk;

    // Listas para los selects
    public $equipos;
    public $areas;
    public $garantias;

    protected $rules = [
        'num_serie' => 'nullable|string|max:50',
        'num_serie_sicopa' => 'nullable|string|max:50',
        'num_serie_sia' => 'nullable|string|max:50',
        'pertenencia' => 'required|in:propia,prestamo,comodato',
        'status' => 'required|in:funcionando,sin funcionar,parcialmente funcional,proceso de baja',
        'id_equipo_fk' => 'required|exists:equipos,id_equipo',
        'id_area_fk' => 'required|exists:areas,id_area',
        'id_garantia_fk' => 'nullable|exists:garantias,id_garantia',
    ];

    protected $messages = [
        'pertenencia.required' => 'La pertenencia es obligatoria.',
        'pertenencia.in' => 'La pertenencia debe ser: propia, préstamo o comodato.',
        'status.required' => 'El estado es obligatorio.',
        'status.in' => 'El estado debe ser uno de los valores permitidos.',
        'id_equipo_fk.required' => 'Debe seleccionar un equipo.',
        'id_equipo_fk.exists' => 'El equipo seleccionado no existe.',
        'id_area_fk.required' => 'Debe seleccionar un área.',
        'id_area_fk.exists' => 'El área seleccionada no existe.',
        'id_garantia_fk.exists' => 'La garantía seleccionada no existe.',
    ];

    public function mount()
    {
        $this->loadLists();
    }

    private function loadLists()
    {
        $this->equipos = Equipo::all();
        $this->areas = Area::all();
        $this->garantias = Garantia::all();
    }

    public function create()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $inventario = Inventario::findOrFail($id);
        $this->inventarioId = $id;
        $this->num_serie = $inventario->num_serie;
        $this->num_serie_sicopa = $inventario->num_serie_sicopa;
        $this->num_serie_sia = $inventario->num_serie_sia;
        $this->pertenencia = $inventario->pertenencia;
        $this->status = $inventario->status;
        $this->id_equipo_fk = $inventario->id_equipo_fk;
        $this->id_area_fk = $inventario->id_area_fk;
        $this->id_garantia_fk = $inventario->id_garantia_fk;
        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate();

        if ($this->isEditMode) {
            Inventario::where('id_inventario', $this->inventarioId)->update([
                'num_serie' => $this->num_serie,
                'num_serie_sicopa' => $this->num_serie_sicopa,
                'num_serie_sia' => $this->num_serie_sia,
                'pertenencia' => $this->pertenencia,
                'status' => $this->status,
                'id_equipo_fk' => $this->id_equipo_fk,
                'id_area_fk' => $this->id_area_fk,
                'id_garantia_fk' => $this->id_garantia_fk,
            ]);
            session()->flash('message', 'Inventario actualizado exitosamente.');
        } else {
            Inventario::create([
                'num_serie' => $this->num_serie,
                'num_serie_sicopa' => $this->num_serie_sicopa,
                'num_serie_sia' => $this->num_serie_sia,
                'pertenencia' => $this->pertenencia,
                'status' => $this->status,
                'id_equipo_fk' => $this->id_equipo_fk,
                'id_area_fk' => $this->id_area_fk,
                'id_garantia_fk' => $this->id_garantia_fk,
            ]);
            session()->flash('message', 'Inventario creado exitosamente.');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        Inventario::find($id)->delete();
        session()->flash('message', 'Inventario eliminado exitosamente.');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset(['inventarioId', 'num_serie', 'num_serie_sicopa', 'num_serie_sia', 'pertenencia', 'status', 'id_equipo_fk', 'id_area_fk', 'id_garantia_fk']);
    }

    /**
     * Renderiza la vista del componente con los datos paginados.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.servicios.gestionar-inventarios', [
            'inventarios' => Inventario::with(['equipo', 'area', 'garantia'])->paginate(10),
        ]);
    }
}