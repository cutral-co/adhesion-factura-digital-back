<?php

namespace App\Http\Controllers;

use App\Models\BarrioMunicipio;
use App\Models\Solicitud;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

class MonitorController extends Controller
{
    public function index()
    {
        return sendResponse([
            'total_aprobadas' => $this->getCantidadTotalAprobadas(),
            'basic_monitor' => $this->getBasicData(),
            'month_monitor' => $this->getCantidadSolicitudesMes(),
            'barrio' => $this->getCantidadSolicitudesBarrio()
        ]);
    }

    public function basicData()
    {
        return sendResponse($this->getBasicData());
    }

    protected function getBasicData()
    {
        return [
            'total' => Solicitud::all()->count(),
            'pendientes' => Solicitud::getCountPendientes(),
            'aprobadas' => Solicitud::getCountAprobadas(),
            'rechazadas' => Solicitud::where('estado_id', 3)->count(),
            'sin_verificar' => Solicitud::getCountSinVerificar(),
        ];
    }

    protected function cantidadTotalAprobadas()
    {
        return sendResponse($this->getCantidadTotalAprobadas());
    }

    protected function getCantidadTotalAprobadas()
    {
        return [
            'labels' => ['Detalle general'],
            'series' => [
                [
                    'name' => 'Solicitudes generadas',
                    'data' => [
                        Solicitud::all()->count(),
                    ]
                ],
                [
                    'name' => 'Solicitudes aprobadas',
                    'data' => [
                        Solicitud::getCountAprobadas(),
                    ]
                ],
                [
                    'name' => 'Solicitudes pendientes',
                    'data' => [
                        Solicitud::getCountPendientes(),
                    ]
                ],
                [
                    'name' => 'Sin verificar',
                    'data' => [
                        Solicitud::getCountSinVerificar(),
                    ]
                ],
            ]
        ];
    }

    public function cantidadSolicitudesMes()
    {
        return sendResponse($this->getCantidadSolicitudesMes());
    }

    protected function getCantidadSolicitudesMes()
    {
        $meses = [
            'January' => 'Enero',
            'February' => 'Febrero',
            'March' => 'Marzo',
            'April' => 'Abril',
            'May' => 'Mayo',
            'June' => 'Junio',
            'July' => 'Julio',
            'August' => 'Agosto',
            'September' => 'Septiembre',
            'October' => 'Octubre',
            'November' => 'Noviembre',
            'December' => 'Diciembre',
        ];

        $solicitudesPorMes = Solicitud::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as total')
        )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $labels = [];
        $data = [];

        foreach ($solicitudesPorMes as $solicitud) {
            // Extraemos el mes y el año del campo 'month'.
            list($year, $month) = explode('-', $solicitud->month);

            // Convertimos el nombre del mes al español utilizando el array $meses.
            $nombreMes = $meses[DateTime::createFromFormat('!m', $month)->format('F')];

            $labels[] = $nombreMes;

            // Almacenamos la cantidad de solicitudes en el mes.
            $data[] = $solicitud->total;
        }


        return [
            'labels' => $labels,
            'series' => [
                [
                    'name' => 'Solicitudes Creadas',
                    'data' => $data
                ]
            ]
        ];
    }

    protected function getCantidadSolicitudesBarrio()
    {
        $solicitudesPorBarrio = Solicitud::select('barrio_id')
            ->whereNotNull('barrio_id')
            ->selectRaw('count(*) as total')
            ->selectRaw('sum(case when estado_id = 1 then 1 else 0 end) as pendientes')
            ->selectRaw('sum(case when estado_id = 2 then 1 else 0 end) as aprobadas')
            ->selectRaw('sum(case when fecha_verificado is null then 1 else 0 end) as sin_verificar')
            ->groupBy('barrio_id')
            ->get();


        $labels = [];
        $solicitudes_generadas = [];
        $solicitudes_pendientes = [];
        $solicitudes_aprobadas = [];
        /* $solicitudes_sin_verificar = []; */
        foreach ($solicitudesPorBarrio as $solicitudPorBarrio) {
            $barrio = BarrioMunicipio::find($solicitudPorBarrio->barrio_id);
            $labels[] = $barrio->name;
            $solicitudes_generadas[] = $solicitudPorBarrio->total;
            $solicitudes_pendientes[] = (int) $solicitudPorBarrio->pendientes;
            $solicitudes_aprobadas[] = (int) $solicitudPorBarrio->aprobadas;
            /* $solicitudes_sin_verificar[] = $solicitudPorBarrio->sin_verificar; */
        }


        return [
            'labels' => $labels,
            'series' => [
                [
                    'name' => 'Solicitudes generadas',
                    'data' => $solicitudes_generadas
                ],
                [
                    'name' => 'Solicitudes aprobadas',
                    'data' => $solicitudes_aprobadas
                ],
                [
                    'name' => 'Solicitudes pendientes',
                    'data' => $solicitudes_pendientes
                ],
                /* [
                    'name' => 'Solicitudes sin verificar',
                    'data' => $solicitudes_sin_verificar
                ], */
            ]
        ];;
    }
}
