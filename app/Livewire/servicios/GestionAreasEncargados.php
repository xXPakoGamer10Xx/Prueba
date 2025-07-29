<?php

// El namespace coincide con tu estructura de carpetas en minúsculas.
namespace App\Livewire\Servicios;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\servicios\Area;
use App\Models\servicios\EncargadoArea;

// El nombre de la clase es 'GestionAreasEncargados'
class GestionAreasEncargados extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Propiedades para ÁREAS
    public $searchArea = '';
    public $areaId, $nombreArea, $id_encargado_area_fk;
    public $isAreaModalOpen = false;
    public $isAreaEditMode = false;

    // Propiedades para ENCARGADOS
    public $searchEncargado = '';
    public $encargadoId, $nombreEncargado, $apellidosEncargado, $cargoEncargado;
    public $isEncargadoModalOpen = false;
    public $isEncargadoEditMode = false;

    protected function rules()
    {
        if ($this->isAreaModalOpen) {
            return [
                'nombreArea' => 'required|string|max:100',
                'id_encargado_area_fk' => 'required|exists:encargados_area,id_encargado_area',
            ];
        }
        if ($this->isEncargadoModalOpen) {
            return [
                'nombreEncargado' => 'required|string|max:100',
                'apellidosEncargado' => 'required|string|max:100',
                'cargoEncargado' => 'required|string|max:100',
            ];
        }
        return [];
    }

    public function render()
    {
        $areas = Area::with('encargado')
            ->where(function ($query) {
                $query->where('nombre', 'like', '%' . $this->searchArea . '%')
                    ->orWhereHas('encargado', function ($q) {
                        $q->where('nombre', 'like', '%' . $this->searchArea . '%')
                          ->orWhere('apellidos', 'like', '%' . $this->searchArea . '%');
                    });
            })
            ->orderBy('nombre')
            ->paginate(5, ['*'], 'pagina_a');

        $encargados = EncargadoArea::query()
            ->where(function ($query) {
                $query->where('nombre', 'like', '%' . $this->searchEncargado . '%')
                    ->orWhere('apellidos', 'like', '%' . $this->searchEncargado . '%')
                    ->orWhere('cargo', 'like', '%' . $this->searchEncargado . '%');
            })
            ->orderBy('apellidos')
            ->paginate(5, ['*'], 'pagina_e');

        $listaEncargados = EncargadoArea::orderBy('nombre')->get();

        // El nombre de la vista 'gestion-areas-encargados' coincide con el nombre de la clase en kebab-case.
        return view('livewire.servicios.gestion-areas-encargados', [
            'areas' => $areas,
            'encargados' => $encargados,
            'listaEncargados' => $listaEncargados,
        ]);
    }

    // --- MÉTODOS CRUD PARA ÁREAS ---

    public function createArea()
    {
        $this->resetAreaFields();
        $this->isAreaEditMode = false;
        $this->isAreaModalOpen = true;
    }

    public function editArea($id)
    {
        $area = Area::findOrFail($id);
        $this->areaId = $id;
        $this->nombreArea = $area->nombre;
        $this->id_encargado_area_fk = $area->id_encargado_area_fk;
        $this->isAreaEditMode = true;
        $this->isAreaModalOpen = true;
    }

    public function storeArea()
    {
        $this->validate();
        Area::updateOrCreate(['id_area' => $this->areaId], [
            'nombre' => $this->nombreArea,
            'id_encargado_area_fk' => $this->id_encargado_area_fk,
        ]);
        session()->flash('mensaje', $this->isAreaEditMode ? 'Área actualizada.' : 'Área creada.');
        $this->closeAreaModal();
    }

    public function deleteArea($id)
    {
        Area::find($id)->delete();
        session()->flash('mensaje', 'Área eliminada.');
    }

    public function closeAreaModal()
    {
        $this->isAreaModalOpen = false;
        $this->resetAreaFields();
    }

    private function resetAreaFields()
    {
        $this->reset(['areaId', 'nombreArea', 'id_encargado_area_fk']);
    }

    // --- MÉTODOS CRUD PARA ENCARGADOS ---

    public function createEncargado()
    {
        $this->resetEncargadoFields();
        $this->isEncargadoEditMode = false;
        $this->isEncargadoModalOpen = true;
    }

    public function editEncargado($id)
    {
        $encargado = EncargadoArea::findOrFail($id);
        $this->encargadoId = $id;
        $this->nombreEncargado = $encargado->nombre;
        $this->apellidosEncargado = $encargado->apellidos;
        $this->cargoEncargado = $encargado->cargo;
        $this->isEncargadoEditMode = true;
        $this->isEncargadoModalOpen = true;
    }

    public function storeEncargado()
    {
        $this->validate();
        EncargadoArea::updateOrCreate(['id_encargado_area' => $this->encargadoId], [
            'nombre' => $this->nombreEncargado,
            'apellidos' => $this->apellidosEncargado,
            'cargo' => $this->cargoEncargado,
        ]);
        session()->flash('mensaje', $this->isEncargadoEditMode ? 'Encargado actualizado.' : 'Encargado creado.');
        $this->closeEncargadoModal();
    }

    public function deleteEncargado($id)
    {
        $encargado = EncargadoArea::withCount('areas')->findOrFail($id);
        if ($encargado->areas_count > 0) {
            session()->flash('error', 'No se puede eliminar. El encargado tiene áreas asignadas.');
            return;
        }
        $encargado->delete();
        session()->flash('mensaje', 'Encargado eliminado.');
    }

    public function closeEncargadoModal()
    {
        $this->isEncargadoModalOpen = false;
        $this->resetEncargadoFields();
    }

    private function resetEncargadoFields()
    {
        $this->reset(['encargadoId', 'nombreEncargado', 'apellidosEncargado', 'cargoEncargado']);
    }

    // Resetea la paginación al buscar
    public function updatingSearchArea() { $this->resetPage('pagina_a'); }
    public function updatingSearchEncargado() { $this->resetPage('pagina_e'); }
}
