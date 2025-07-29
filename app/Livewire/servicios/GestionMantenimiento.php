<?php

namespace App\Livewire\Servicios;

use Livewire\Component;
use App\Models\Servicios\Mantenimiento;
use App\Models\Servicios\Inventario;
use App\Models\Servicios\EncargadoMantenimiento;
use Livewire\WithPagination;

class GestionMantenimiento extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $showEncargadoModal = false;

    // CAMBIO AQUÍ: Volver a 'id_inventario' y 'id_encargado_man'
    public $id_inventario, $id_encargado_man, $fecha, $tipo, $refacciones_material, $observaciones;

    // Propiedades para el formulario de Encargado (se mantienen igual)
    public $nombre, $apellidos, $cargo, $contacto;

    protected $rules = [
        // CAMBIO AQUÍ: Volver a 'id_inventario' y 'id_encargado_man'
        'id_inventario' => 'required|integer|exists:inventarios,id_inventario',
        'id_encargado_man' => 'required|integer|exists:encargados_mantenimiento,id_encargado_man',
        'fecha' => 'required|date',
        'tipo' => 'required|in:preventivo,correctivo',
        'refacciones_material' => 'nullable|string',
        'observaciones' => 'nullable|string',
    ];

    public function mount()
    {
        if (request()->has('equipo_id') && request()->has('abrir_modal')) {
            $this->openModal();
            // CAMBIO AQUÍ: Asigna a la propiedad 'id_inventario'
            $this->id_inventario = request('equipo_id');
        }
    }

    public function render()
    {
        // ... (Tu código render() se mantiene igual) ...
        $reportes = Mantenimiento::with(['inventario.equipo', 'encargadoMantenimiento'])
            ->when($this->search, function ($query) {
                $query->where('tipo', 'like', '%' . $this->search . '%')
                    ->orWhereHas('inventario.equipo', function ($q) {
                        $q->where('nombre', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('encargadoMantenimiento', function ($q) {
                        $q->where('nombre', 'like', '%' . $this->search . '%')
                            ->orWhere('apellidos', 'like', '%' . $this->search . '%');
                    });
            })
            ->orderBy('fecha', 'desc')
            ->paginate(10);

        return view('livewire.servicios.gestion-mantenimiento', [
            'reportes' => $reportes,
            'equipos_inventario' => Inventario::with('equipo')->where('status', '!=', 'proceso de baja')->get(),
            'encargados' => EncargadoMantenimiento::orderBy('nombre')->get(),
        ]);
    }

    public function openModal() { $this->resetInput(); $this->showModal = true; }
    public function openEncargadoModal() { $this->resetEncargadoInput(); $this->showEncargadoModal = true; }

    public function saveMantenimiento()
    {
        $this->validate();

        Mantenimiento::create([
            // CAMBIO AQUÍ: Usar las propiedades 'id_inventario' y 'id_encargado_man'
            'id_inventario' => $this->id_inventario,
            'id_encargado_man' => $this->id_encargado_man,
            'fecha' => $this->fecha,
            'tipo' => $this->tipo,
            'refacciones_material' => $this->refacciones_material,
            'observaciones' => $this->observaciones,
        ]);

        $this->showModal = false;
        session()->flash('success', 'Reporte de mantenimiento guardado exitosamente.');
    }

    public function saveEncargado()
    {
        // ... (Tu código saveEncargado() se mantiene igual) ...
        $this->validate([
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'cargo' => 'required|string|max:100',
            'contacto' => 'nullable|string|max:100',
        ]);

        EncargadoMantenimiento::create([
            'nombre' => $this->nombre,
            'apellidos' => $this->apellidos,
            'cargo' => $this->cargo,
            'contacto' => $this->contacto,
        ]);

        $this->showEncargadoModal = false;
        session()->flash('success', 'Encargado registrado exitosamente.');
    }

    private function resetInput() {
        // CAMBIO AQUÍ: Resetear 'id_inventario' y 'id_encargado_man'
        $this->id_inventario = null;
        $this->id_encargado_man = null;
        $this->fecha = now()->format('Y-m-d');
        $this->tipo = null;
        $this->refacciones_material = '';
        $this->observaciones = '';
    }

    private function resetEncargadoInput() { $this->nombre = ''; $this->apellidos = ''; $this->cargo = ''; $this->contacto = ''; }
    public function updatingSearch() { $this->resetPage(); }
}
