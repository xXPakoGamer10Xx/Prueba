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

    public $id_inventario_fk, $id_mantenimiento_fk, $estado, $motivo;
    public $fecha_baja;

    public $ultimoMantenimientoInfo = '';

    public $currentBajaId = null;
    public $isEditing = false;
    // ¡AÑADE ESTA LÍNEA!
    public $originalBajaEstado = null; // Variable para almacenar el estado original de la baja


    protected $rules = [
        'id_inventario_fk' => 'required|integer|exists:inventarios,id_inventario',
        'id_mantenimiento_fk' => 'nullable|integer|exists:mantenimientos,id_mantenimiento',
        'estado' => 'required|in:en proceso,baja completa,cancelada',
        'motivo' => 'required|string',
        'fecha_baja' => 'required|date',
    ];

    protected $listeners = ['flashMessage'];

    public function flashMessage($type, $message)
    {
        session()->flash($type, $message);
    }

    public function mount()
    {
        // Asegurarse de que fecha_baja siempre tenga un valor por defecto al inicializar
        $this->fecha_baja = now()->format('Y-m-d');

        if (request()->has('equipo_id') && request()->has('abrir_modal')) {
            $inventarioId = request('equipo_id');
            $this->openModal();
            $this->id_inventario_fk = $inventarioId;
            $this->updatedIdInventarioFk($inventarioId);
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
            // No es necesario pasar originalBajaEstado aquí, ya es una propiedad pública del componente.
        ]);
    }

    public function updatedIdInventarioFk($inventarioId)
    {
        $this->ultimoMantenimientoInfo = '';
        $this->currentBajaId = null;
        $this->isEditing = false;
        $this->resetValidation();
        $this->reset(['estado', 'motivo']);
        $this->fecha_baja = now()->format('Y-m-d');
        $this->originalBajaEstado = null; // Asegurarse de resetearla

        if (!empty($inventarioId)) {
            $ultimoMantenimiento = Mantenimiento::where('id_inventario', $inventarioId)
                                                ->orderBy('fecha', 'desc')
                                                ->first();
            if ($ultimoMantenimiento) {
                $this->id_mantenimiento_fk = $ultimoMantenimiento->id_mantenimiento;
                $this->ultimoMantenimientoInfo = "Último mantenimiento asociado: " . $ultimoMantenimiento->fecha . " (Tipo: " . $ultimoMantenimiento->tipo . ")";
            } else {
                $this->id_mantenimiento_fk = null;
                $this->ultimoMantenimientoInfo = 'Este equipo no tiene mantenimientos registrados.';
            }

            $existingPendingBaja = ProcesoBaja::where('id_inventario_fk', $inventarioId)
                                             ->where('estado', 'en proceso')
                                             ->first();

            if ($existingPendingBaja) {
                $this->isEditing = true;
                $this->currentBajaId = $existingPendingBaja->id_proceso_baja;
                $this->estado = $existingPendingBaja->estado;
                $this->motivo = $existingPendingBaja->motivo;
                $this->fecha_baja = $existingPendingBaja->fecha_baja;
                $this->originalBajaEstado = $existingPendingBaja->estado; // <-- ASIGNAR AQUI

                $this->dispatch('flashMessage', 'warning', 'Este equipo ya tiene un proceso de baja activo. Se han cargado sus datos para que puedas finalizarlo o cancelarlo.');
            } else {
                $lastCancelledBaja = ProcesoBaja::where('id_inventario_fk', $inventarioId)
                                                ->where('estado', 'cancelada')
                                                ->orderBy('fecha_baja', 'desc')
                                                ->first();
                if ($lastCancelledBaja) {
                    $this->dispatch('flashMessage', 'info', 'La última baja de este equipo fue cancelada. Puedes iniciar un nuevo proceso.');
                }
            }
        } else {
            $this->id_mantenimiento_fk = null;
            $this->ultimoMantenimientoInfo = '';
            $this->currentBajaId = null;
            $this->isEditing = false;
            $this->originalBajaEstado = null; // <-- RESETEAR AQUI
            $this->dispatch('flashMessage', 'info', '');
        }
    }

    public function updatedEstado($value)
    {
        if (!empty($this->id_inventario_fk)) {
            $inventario = Inventario::find($this->id_inventario_fk);
            if ($inventario) {
                $newStatus = null;
                switch ($value) {
                    case 'en proceso':
                        $newStatus = 'proceso de baja';
                        break;
                    case 'baja completa':
                        $newStatus = 'baja';
                        break;
                    case 'cancelada':
                        if ($inventario->status === 'proceso de baja' || $inventario->status === 'baja') {
                             $newStatus = 'funcionando';
                        }
                        break;
                    default:
                        break;
                }

                if ($newStatus && $inventario->status !== $newStatus) {
                    $inventario->status = $newStatus;
                    $inventario->saveQuietly();
                    $this->dispatch('flashMessage', 'info', 'Estado del inventario actualizado a "' . $newStatus . '".');
                }
            }
        }
    }

    public function openModal()
    {
        $this->resetInput();
        $this->showModal = true;
        $this->resetValidation();
    }

    public function saveBaja()
    {
        $this->validate();

        // Lógica para prevenir la modificación de bajas 'canceladas'
        if ($this->isEditing && $this->originalBajaEstado === 'cancelada') { // Usar originalBajaEstado aquí
            $this->dispatch('flashMessage', 'error', 'No se puede modificar una baja que ya ha sido cancelada.');
            $this->showModal = false;
            return;
        }

        DB::transaction(function () {
            if ($this->isEditing && $this->currentBajaId) {
                $baja = ProcesoBaja::find($this->currentBajaId);
                if ($baja) {
                    $baja->update([
                        'id_inventario_fk' => $this->id_inventario_fk,
                        'id_mantenimiento_fk' => $this->id_mantenimiento_fk,
                        'motivo' => $this->motivo,
                        'estado' => $this->estado,
                        'fecha_baja' => $this->fecha_baja,
                    ]);
                }
                $message = 'Baja actualizada correctamente.';
            } else {
                ProcesoBaja::create([
                    'id_inventario_fk' => $this->id_inventario_fk,
                    'id_mantenimiento_fk' => $this->id_mantenimiento_fk,
                    'motivo' => $this->motivo,
                    'estado' => $this->estado,
                    'fecha_baja' => $this->fecha_baja,
                ]);
                $message = 'Baja registrada correctamente.';
            }

            $inventario = Inventario::find($this->id_inventario_fk);
            if ($inventario) {
                switch ($this->estado) {
                    case 'baja completa':
                        $inventario->status = 'baja';
                        break;
                    case 'en proceso':
                        $inventario->status = 'proceso de baja';
                        break;
                    case 'cancelada':
                        if ($inventario->status === 'proceso de baja' || $inventario->status === 'baja') {
                             $inventario->status = 'funcionando';
                        }
                        break;
                }
                $inventario->save();
            }
        });

        $this->showModal = false;
        session()->flash('success', $message);
    }

    private function resetInput()
    {
        $this->id_inventario_fk = null;
        $this->id_mantenimiento_fk = null;
        $this->estado = null;
        $this->motivo = '';
        $this->ultimoMantenimientoInfo = '';
        $this->fecha_baja = now()->format('Y-m-d');
        $this->currentBajaId = null;
        $this->isEditing = false;
        $this->originalBajaEstado = null; // Asegurarse de resetearla
        $this->dispatch('flashMessage', 'info', '');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
