<?php

namespace App\Livewire\Servicios;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Servicios\Inventario;
use App\Models\Servicios\Equipo;
use App\Models\Servicios\Area;
use App\Models\Servicios\Garantia;
use App\Models\Servicios\ProcesoBaja; // <-- Añadido para que la función de status no falle
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

class GestionInventario extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $search = '';

    // --- PROPIEDADES PARA EL MODAL Y ESTADO ---
    public $isModalOpen = false;
    public $isEditMode = false;
    public $inventarioId;
    public $equipoId;

    // --- PROPIEDADES DE LOS CAMPOS DEL FORMULARIO ---
    public $nombre;
    public $marca;
    public $modelo;
    public $frecuencia_mantenimiento = 6;
    public $num_serie;
    public $num_serie_sicopa;
    public $num_serie_sia;
    public $pertenencia = 'propia';
    public $status = 'funcionando';
    public $id_area_fk;
    public $id_garantia_fk;

    // --- PROPIEDADES PARA LA NUEVA GARANTÍA ---
    public $showNewWarrantyFields = false;
    public $nueva_garantia_status = 'activa';
    public $nueva_garantia_fecha;
    public $nueva_garantia_empresa;
    public $nueva_garantia_contacto;

    // --- NUEVO: PROPIEDADES PARA LOS FILTROS ---
    public $filtroStatus = '';
    public $filtroArea = '';
    public $filtroGarantia = '';

    protected function rules()
    {
        // (El método rules se mantiene sin cambios)
        $rules = [
            'nombre' => 'required|string|max:100',
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'frecuencia_mantenimiento' => 'required|integer',
            'num_serie' => 'nullable|string|max:50',
            'num_serie_sicopa' => 'nullable|string|max:50|unique:inventarios,num_serie_sicopa,' . $this->inventarioId . ',id_inventario',
            'num_serie_sia' => 'nullable|string|max:50|unique:inventarios,num_serie_sia,' . $this->inventarioId . ',id_inventario',
            'pertenencia' => 'required|in:propia,comodato',
            'status' => 'required|in:funcionando,sin funcionar,parcialmente funcional,proceso de baja,baja',
            'id_area_fk' => 'required|exists:areas,id_area',
            'id_garantia_fk' => 'nullable|exists:garantias,id_garantia',
        ];

        if ($this->showNewWarrantyFields) {
            $rules['nueva_garantia_status'] = 'required|in:activa,terminada';
            $rules['nueva_garantia_fecha'] = 'nullable|date';
            $rules['nueva_garantia_empresa'] = 'nullable|string|max:100';
            $rules['nueva_garantia_contacto'] = 'nullable|string|max:100';

            if ($this->nueva_garantia_status === 'activa') {
                $rules['nueva_garantia_fecha'] = 'required|date';
                $rules['nueva_garantia_empresa'] = 'required|string|max:100';
                $rules['nueva_garantia_contacto'] = 'required|string|max:100';
            }
        }
        return $rules;
    }

    /**
     * MODIFICADO: Añade la lógica para los nuevos filtros.
     */
    public function render()
    {
        $inventarioQuery = Inventario::with(['equipo', 'area', 'garantia'])
            // Filtro de búsqueda general
            ->when($this->search, function ($query) {
                $query->whereHas('equipo', function ($q) {
                    $q->where('nombre', 'like', '%' . $this->search . '%')
                      ->orWhere('marca', 'like', '%' . $this->search . '%')
                      ->orWhere('modelo', 'like', '%' . $this->search . '%');
                })
                ->orWhere('num_serie', 'like', '%' . $this->search . '%')
                ->orWhere('num_serie_sicopa', 'like', '%' . $this->search . '%')
                ->orWhere('num_serie_sia', 'like', '%' . $this->search . '%');
            })
            // --- NUEVOS FILTROS APLICADOS ---
            ->when($this->filtroStatus, fn ($q) => $q->where('status', $this->filtroStatus))
            ->when($this->filtroArea, fn ($q) => $q->where('id_area_fk', $this->filtroArea))
            ->when($this->filtroGarantia, function ($query) {
                if ($this->filtroGarantia === 'con') {
                    return $query->whereNotNull('id_garantia_fk');
                }
                if ($this->filtroGarantia === 'sin') {
                    return $query->whereNull('id_garantia_fk');
                }
            });

        $inventario = $inventarioQuery->orderBy('id_inventario', 'desc')->paginate(10);
        
        $areas = Area::orderBy('nombre')->get();
        $garantias = Garantia::all();

        return view('livewire.servicios.gestion-inventario', [
            'inventario' => $inventario,
            'areas' => $areas,
            'garantias' => $garantias,
        ]);
    }

    // --- NUEVO: Función para limpiar filtros ---
    public function resetearFiltros()
    {
        $this->reset(['search', 'filtroStatus', 'filtroArea', 'filtroGarantia']);
        $this->resetPage();
    }

    // --- NUEVO: Función para exportar a CSV ---
    public function exportarCSV()
    {
        // Se reutiliza la misma lógica de consulta que en render() para consistencia
        $inventarioQuery = Inventario::with(['equipo', 'area', 'garantia'])
            ->when($this->search, function ($query) { /* ... */ })
            ->when($this->filtroStatus, fn ($q) => $q->where('status', $this->filtroStatus))
            ->when($this->filtroArea, fn ($q) => $q->where('id_area_fk', $this->filtroArea))
            ->when($this->filtroGarantia, function ($query) {
                if ($this->filtroGarantia === 'con') return $query->whereNotNull('id_garantia_fk');
                if ($this->filtroGarantia === 'sin') return $query->whereNull('id_garantia_fk');
            });
        
        $inventario = $inventarioQuery->orderBy('id_inventario', 'desc')->get();
        $fileName = 'Reporte_Inventario_' . date('Y-m-d_H-i') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($inventario) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['HOSPITAL MUNICIPAL DE CHICONCUAC']);
            fputcsv($file, ['Reporte General de Inventario de Equipos']);
            fputcsv($file, ['Generado el: ' . Carbon::now('America/Mexico_City')->format('d/m/Y H:i')]);
            fputcsv($file, []);

            $columnas = ['ID', 'Equipo', 'Marca', 'Modelo', 'Núm. Serie', 'Serie SICOPA', 'Serie SIA', 'Pertenencia', 'Estado', 'Área de Ubicación', 'Garantía', 'Vencimiento Garantía', 'Empresa Garantía'];
            fputcsv($file, $columnas);

            foreach ($inventario as $item) {
                fputcsv($file, [
                    $item->id_inventario,
                    $item->equipo->nombre ?? 'N/A',
                    $item->equipo->marca ?? 'N/A',
                    $item->equipo->modelo ?? 'N/A',
                    $item->num_serie ?? 'N/A',
                    $item->num_serie_sicopa ?? 'N/A',
                    $item->num_serie_sia ?? 'N/A',
                    ucfirst($item->pertenencia),
                    ucfirst(str_replace('_', ' ', $item->status)),
                    $item->area->nombre ?? 'N/A',
                    $item->garantia ? ucfirst($item->garantia->status) : 'Sin Garantía',
                    $item->garantia ? Carbon::parse($item->garantia->fecha_garantia)->format('d/m/Y') : 'N/A',
                    $item->garantia->empresa ?? 'N/A',
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    // --- EL RESTO DE FUNCIONES SE MANTIENEN IGUAL ---

    public function actualizarStatus($inventarioId, $nuevoStatus)
    {
        $inventario = Inventario::find($inventarioId);
        if ($inventario) {
            if ($inventario->status === 'baja' && $nuevoStatus !== 'baja') {
                session()->flash('error', 'Este equipo ya ha sido dado de baja y su estado no puede ser modificado.');
                $this->js('window.location.reload()');
                return;
            }
            if ($nuevoStatus === 'baja') {
                $procesoBajaActivo = ProcesoBaja::where('id_inventario_fk', $inventarioId)
                                                ->where('estado', 'en proceso')->first();
                if ($procesoBajaActivo) {
                    session()->flash('error', 'Existe un proceso de baja "en proceso" para este equipo. Finalícelo desde la sección de Bajas.');
                    return;
                }
            }
            $inventario->status = $nuevoStatus;
            $inventario->save();
            session()->flash('success', 'Estado del equipo actualizado correctamente.');
        } else {
            session()->flash('error', 'Equipo de inventario no encontrado.');
        }
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isEditMode = false;
        $this->isModalOpen = true;
        $this->nueva_garantia_fecha = now()->format('Y-m-d');
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
        $this->num_serie_sicopa = $inventario->num_serie_sicopa;
        $this->num_serie_sia = $inventario->num_serie_sia;
        $this->pertenencia = $inventario->pertenencia;
        $this->status = $inventario->status;
        $this->id_area_fk = $inventario->id_area_fk;
        $this->id_garantia_fk = $inventario->id_garantia_fk;
        $this->showNewWarrantyFields = false;
        $this->reset(['nueva_garantia_status', 'nueva_garantia_fecha', 'nueva_garantia_empresa', 'nueva_garantia_contacto']);
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
                    'fecha_garantia' => $this->nueva_garantia_fecha,
                    'empresa' => $this->nueva_garantia_empresa,
                    'contacto' => $this->nueva_garantia_contacto,
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

        session()->flash('success', $this->isEditMode ? 'Equipo actualizado en el inventario.' : 'Equipo agregado al inventario.');
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
        $this->reset(['nueva_garantia_status', 'nueva_garantia_fecha', 'nueva_garantia_empresa', 'nueva_garantia_contacto']);
        if ($this->showNewWarrantyFields) {
            $this->nueva_garantia_fecha = now()->format('Y-m-d');
        }
    }
    
    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }
    
    private function resetInputFields()
    {
        $this->reset();
        $this->frecuencia_mantenimiento = 6;
        $this->pertenencia = 'propia';
        $this->status = 'funcionando';
        $this->nueva_garantia_status = 'activa';
        $this->nueva_garantia_fecha = now()->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}