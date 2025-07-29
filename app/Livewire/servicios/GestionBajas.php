<?php

namespace App\Livewire\Servicios;

use Livewire\Component;
use App\Models\Servicios\ProcesoBaja;
use App\Models\Servicios\Inventario;
use App\Models\Servicios\Mantenimiento;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class GestionBajas extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;

    // Propiedades para el formulario de Baja
    public $id_inventario, $id_mantenimiento, $estado, $motivo;
    public $fecha_baja; 

    // Propiedad para mostrar información del último mantenimiento en la vista.
    public $ultimoMantenimientoInfo = '';

    protected $rules = [
        'id_inventario' => 'required|integer|exists:inventarios,id_inventario',
        'id_mantenimiento' => 'nullable|integer|exists:mantenimientos,id_mantenimiento',
        'estado' => 'required|in:en proceso,baja completa,cancelada',
        'motivo' => 'required|string',
        'fecha_baja' => 'required|date',
    ];

    /**
     * NUEVO MÉTODO: Se ejecuta al cargar el componente.
     * Revisa si se pasaron parámetros desde la vista de inventario para abrir el modal.
     */
    public function mount()
    {
        if (request()->has('equipo_id') && request()->has('abrir_modal')) {
            $inventarioId = request('equipo_id');
            $this->openModal(); // Llama a la función que resetea campos y abre el modal.
            $this->id_inventario = $inventarioId; // Asigna el ID del equipo.
            $this->updatedIdInventario($inventarioId); // Carga la info del último mantenimiento.
        }
    }

    public function render()
    {
        $bajas = ProcesoBaja::with('inventario.equipo')
            ->when($this->search, function ($query) {
                $query->where('motivo', 'like', '%' . $this->search . '%')
                    ->orWhere('estado', 'like', '%' . $this->search . '%')
                    ->orWhereHas('inventario.equipo', function ($q) {
                        $q->where('nombre', 'like', '%' . $this->search . '%');
                    });
            })
            ->orderBy('id_proceso_baja', 'desc')
            ->paginate(10);

        return view('livewire.servicios.gestion-bajas', [
            'bajas' => $bajas,
            'equipos_inventario' => Inventario::with('equipo')->where('status', '!=', 'baja')->get(),
            'mantenimientos' => Mantenimiento::orderBy('fecha', 'desc')->get(),
        ]);
    }

    public function updatedIdInventario($inventarioId)
    {
        if (!empty($inventarioId)) {
            $ultimoMantenimiento = Mantenimiento::where('id_inventario_fk', $inventarioId)
                                                 ->orderBy('fecha', 'desc')
                                                 ->first();

            if ($ultimoMantenimiento) {
                $this->id_mantenimiento = $ultimoMantenimiento->id_mantenimiento;
                $this->ultimoMantenimientoInfo = "Último mantenimiento asociado: " . $ultimoMantenimiento->fecha . " (Tipo: " . $ultimoMantenimiento->tipo . ")";
            } else {
                $this->id_mantenimiento = null;
                $this->ultimoMantenimientoInfo = 'Este equipo no tiene mantenimientos registrados.';
            }
        } else {
            $this->id_mantenimiento = null;
            $this->ultimoMantenimientoInfo = '';
        }
    }

    public function openModal()
    {
        $this->resetInput();
        $this->showModal = true;
    }

    public function saveBaja()
    {
        $this->validate();

        DB::transaction(function () {
            ProcesoBaja::create([
                'id_inventario_fk' => $this->id_inventario,
                'id_mantenimiento_fk' => $this->id_mantenimiento,
                'motivo' => $this->motivo,
                'estado' => $this->estado,
                'fecha_baja' => $this->fecha_baja,
            ]);

            if ($this->estado === 'baja completa') {
                $inventario = Inventario::find($this->id_inventario);
                $inventario->status = 'baja';
                $inventario->save();
            }
        });

        $this->showModal = false;
        session()->flash('success', 'Baja registrada correctamente.');
    }

    private function resetInput()
    {
        $this->id_inventario = null;
        $this->id_mantenimiento = null;
        $this->estado = null;
        $this->motivo = '';
        $this->ultimoMantenimientoInfo = '';
        $this->fecha_baja = now()->format('Y-m-d'); 
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
