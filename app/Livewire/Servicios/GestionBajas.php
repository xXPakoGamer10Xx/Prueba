<?php

namespace App\Livewire\Servicios;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Servicios\ProcesoBaja;
use App\Models\Servicios\Inventario;
use App\Models\Servicios\Mantenimiento;
use Illuminate\Support\Facades\DB;

class GestionBajas extends Component
{
    use WithPagination;

    // Propiedades públicas
    public $search = '';
    public $showModal = false;

    // Propiedades del formulario
    public $id_inventario_fk;
    public $id_mantenimiento_fk;
    public $estado;
    public $motivo;
    public $fecha_baja;

    // Propiedades de estado del componente
    public $ultimoMantenimientoInfo = '';
    public $currentBajaId = null;
    public $isEditing = false;
    public $originalBajaEstado = null;

    // Nueva propiedad para manejar las opciones del select de estado dinámicamente
    public $availableEstados = [];

    // Listeners de eventos
    protected $listeners = ['flashMessage'];

    /**
     * Reglas de validación para el formulario.
     */
    protected function rules()
    {
        return [
            'id_inventario_fk' => 'required|integer|exists:inventarios,id_inventario',
            'id_mantenimiento_fk' => 'nullable|integer|exists:mantenimientos,id_mantenimiento',
            // CORREGIDO: 'cancelada' a 'cancelado' para coincidir con el ENUM de la BD.
            'estado' => 'required|in:en proceso,baja completa,cancelado',
            'motivo' => 'required|string',
            'fecha_baja' => 'required|date',
        ];
    }

    /**
     * Muestra un mensaje flash en la sesión.
     */
    public function flashMessage($type, $message)
    {
        session()->flash($type, $message);
    }

    /**
     * Se ejecuta al inicializar el componente.
     */
    public function mount()
    {
        $this->fecha_baja = now()->format('Y-m-d');

        if (request()->has('equipo_id') && request()->has('abrir_modal')) {
            $inventarioId = request('equipo_id');
            $this->openModal(); // openModal ahora preparará los estados para una nueva baja
            $this->id_inventario_fk = $inventarioId;
            $this->updatedIdInventarioFk($inventarioId);
        }
    }

    /**
     * Se ejecuta cuando cambia la selección de inventario.
     */
    public function updatedIdInventarioFk($inventarioId)
    {
        $this->resetInputForNewSelection();

        if (!empty($inventarioId)) {
            $ultimoMantenimiento = Mantenimiento::where('id_inventario_fk', $inventarioId)
                                                ->orderBy('fecha', 'desc')->first();
            if ($ultimoMantenimiento) {
                $this->id_mantenimiento_fk = $ultimoMantenimiento->id_mantenimiento;
                $this->ultimoMantenimientoInfo = "Último mantenimiento: " . \Carbon\Carbon::parse($ultimoMantenimiento->fecha)->format('d/m/Y') . " (Tipo: " . $ultimoMantenimiento->tipo . ")";
            } else {
                $this->id_mantenimiento_fk = null;
                $this->ultimoMantenimientoInfo = 'Este equipo no tiene mantenimientos registrados.';
            }

            $existingPendingBaja = ProcesoBaja::where('id_inventario_fk', $inventarioId)
                                              ->where('estado', 'en proceso')->first();

            if ($existingPendingBaja) {
                $this->loadBajaForEditing($existingPendingBaja);
                $this->dispatch('flashMessage', 'warning', 'Este equipo ya tiene un proceso de baja activo. Se han cargado sus datos.');
            } else {
                // Si no hay una baja en proceso, es una nueva baja.
                $this->isEditing = false;
                $this->availableEstados = [
                    'en proceso' => 'En proceso',
                    'baja completa' => 'Baja completa'
                ];
                $this->estado = 'en proceso'; // Estado por defecto para nuevas bajas
            }
        }
    }

    /**
     * Actualiza el estado del inventario cuando cambia el estado de la baja.
     */
    public function updatedEstado($value)
    {
        if (!empty($this->id_inventario_fk)) {
            $inventario = Inventario::find($this->id_inventario_fk);
            if ($inventario) {
                $newStatus = null;
                switch ($value) {
                    case 'en proceso': $newStatus = 'proceso de baja'; break;
                    case 'baja completa': $newStatus = 'baja'; break;
                    // CORREGIDO: 'cancelada' a 'cancelado'
                    case 'cancelado':
                        if (in_array($inventario->status, ['proceso de baja', 'baja'])) {
                            $newStatus = 'funcionando';
                        }
                        break;
                }

                if ($newStatus && $inventario->status !== $newStatus) {
                    $inventario->status = $newStatus;
                    $inventario->saveQuietly();
                    $this->dispatch('flashMessage', 'info', 'Estado del inventario actualizado a "' . ucfirst($newStatus) . '".');
                }
            }
        }
    }

    /**
     * Guarda o actualiza un proceso de baja.
     */
    public function saveBaja()
    {
        $this->validate();

        // CORREGIDO: 'cancelada' a 'cancelado'
        if ($this->isEditing && $this->originalBajaEstado === 'cancelado') {
            $this->dispatch('flashMessage', 'error', 'No se puede modificar una baja que ya ha sido cancelada.');
            $this->showModal = false;
            return;
        }

        DB::transaction(function () {
            $data = [
                'id_inventario_fk'    => $this->id_inventario_fk,
                'id_mantenimiento_fk' => $this->id_mantenimiento_fk,
                'motivo'              => $this->motivo,
                'estado'              => $this->estado,
                'fecha_baja'          => $this->fecha_baja,
            ];

            ProcesoBaja::updateOrCreate(['id_proceso_baja' => $this->currentBajaId], $data);

            $this->updatedEstado($this->estado); // Sincroniza el estado del inventario

            $message = $this->isEditing ? 'Baja actualizada correctamente.' : 'Baja registrada correctamente.';
            session()->flash('success', $message);
        });

        $this->showModal = false;
    }

    /**
     * Renderiza la vista del componente.
     */
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

        $equipos_inventario = Inventario::with('equipo')->where('status', '!=', 'baja')->get();

        return view('livewire.servicios.gestion-bajas', [
            'bajas' => $bajas,
            'equipos_inventario' => $equipos_inventario,
        ]);
    }

    // --- Métodos de Ayuda ---

    public function openModal()
    {
        $this->resetInput();
        $this->showModal = true;
    }

    public function editBaja($id)
    {
        $baja = ProcesoBaja::findOrFail($id);
        $this->loadBajaForEditing($baja);
        $this->showModal = true;
    }

    private function loadBajaForEditing(ProcesoBaja $baja)
    {
        $this->currentBajaId      = $baja->id_proceso_baja;
        $this->id_inventario_fk   = $baja->id_inventario_fk;
        $this->id_mantenimiento_fk= $baja->id_mantenimiento_fk;
        $this->estado             = $baja->estado;
        $this->motivo             = $baja->motivo;
        $this->fecha_baja         = \Carbon\Carbon::parse($baja->fecha_baja)->format('Y-m-d');
        $this->originalBajaEstado = $baja->estado;
        $this->isEditing          = true;

        // Lógica para definir los estados disponibles al editar
        if ($this->originalBajaEstado === 'en proceso') {
            $this->availableEstados = [
                'baja completa' => 'Baja completa',
                'cancelado'     => 'Cancelar Baja'
            ];
        } else {
            // Si ya está completada o cancelada, solo se muestra su estado actual.
            $this->availableEstados = [$baja->estado => ucfirst($baja->estado)];
        }
    }

    private function resetInput()
    {
        $this->reset();
        $this->fecha_baja = now()->format('Y-m-d');
        // Prepara los estados para una NUEVA baja
        $this->availableEstados = [
            'en proceso' => 'En proceso',
            'baja completa' => 'Baja completa'
        ];
        $this->estado = 'en proceso'; // Estado por defecto
    }

    private function resetInputForNewSelection()
    {
        $this->resetValidation();
        $this->ultimoMantenimientoInfo = '';
        $this->currentBajaId = null;
        $this->isEditing = false;
        $this->originalBajaEstado = null;
        $this->reset(['estado', 'motivo']);
        $this->fecha_baja = now()->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
