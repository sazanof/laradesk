<?php

namespace App\Http\Controllers;

use App\Enums\StatisticType;
use App\Exceptions\UserNotBelongsToDepartmentException;
use App\Helpdesk\TicketStatus;
use App\Helpers\AclHelper;
use App\Helpers\ChartHelper;
use App\Helpers\DepartmentHelper;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    /**
     * @throws UserNotBelongsToDepartmentException
     * @throws \Exception
     */
    public function getStatistics(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $type = StatisticType::tryFrom($request->get('type'));
        $rules = [
            'type' => 'required|int'
        ];
        if ($type !== StatisticType::DEPARTMENT) {
            $rules['department'] = 'required|int';
        }
        $start = null;
        $end = null;
        $labels = null;
        $datasets = null;
        $hasDates = false;
        $department = null;

        $request->validate($rules);
        if ($request->has('dates')) {
            $dates = $request->get('dates');
            if (!empty($dates['start'])) {
                $start = Carbon::parse($dates['start']);
                $end = Carbon::parse($dates['end']);
                if ($start instanceof Carbon && $end instanceof Carbon) {
                    $hasDates = true;
                }
            }
        }

        if (!empty($rules['department'])) {
            $department = Department::findOrFail($request->get('department'));
            if (!AclHelper::adminBelongsToDepartment($department->id)) {
                throw  new UserNotBelongsToDepartmentException();
            }
        }

        $chart = new ChartHelper();
        $chart->setStart($start);
        $chart->setEnd($end);
        $chart->searchBetweenDates('t.created_at');


        switch ($type) {
            case StatisticType::DEPARTMENT:
                $departments = DepartmentHelper::getUserDepartments(
                    user: $request->user(),
                    onlyIds: true
                );
                if (!empty($departments)) {
                    $chart->queryTicketsCountByStatusAndDepartments($departments);
                }

                //return $query->get();
                break;
            case StatisticType::USER :
                $chart->queryTicketsCountByUserAndDepartment($department);
                break;
        }
        return $chart->toChartData();
    }
}
