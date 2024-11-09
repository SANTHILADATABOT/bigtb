<?php

namespace WeDevs\PM\Reports\Controllers;

use Reflection;
use WeDevs\PM\Reports\Transformers\EmployeeTransformer;
use WP_REST_Request;
use League\Fractal;
use League\Fractal\Resource\Item as Item;
use League\Fractal\Resource\Collection as Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use WeDevs\PM\Common\Traits\Transformer_Manager;
use WeDevs\PM\Common\Traits\Request_Filter;
use Carbon\Carbon;
use WeDevs\PM\Reports\Models\ReportData;
use WeDevs\PM\Reports\Transformers\RecordsTransformer;
use WeDevs\PM\Reports\Transformers\ReportTransformer;
use WeDevs\PM\Reports\Transformers\ReportDataTransformer;
use WeDevs\PM\Reports\Templates\WIPSchedule;
use WeDevs\PM\Reports\Templates\StatusReport;

class ReportsController
{
    use Transformer_Manager, Request_Filter;

    public function getReports(): array {
        $results = ReportData::all()->load('columnDataTypes');
        $results = new Collection($results, new ReportDataTransformer());
        return $this->get_response($results);
    }

    public function getEmployees(): array {
        global $wpdb;
        $query = "SELECT `recnum`, `fstnme`, `lstnme` FROM `employ` WHERE `status` = 1";
        $results = $wpdb->get_results($query);
        $results = new Collection($results, new EmployeeTransformer());
        return $this->get_response($results);
    }

    public function getRecords(WP_REST_Request $request): array
    {
        global $wpdb;
        $report_id = $request->get_param('report_id');
        $table = '';

        switch ($report_id) {
            case 'budget':
                $sql = "SELECT DISTINCT `recnum` FROM `bdglin`";
                break;
            case 'inventory-quantity-by-location':
                $sql = "SELECT DISTINCT `locnum` FROM `invqty`";
                break;
            case 'wac-value-report':
                $sql = "SELECT DISTINCT `prtnum` FROM `invqty`";
                break;
            case 'job-cost-totals':
            case 'job-cost-journal':
            case 'job-labor-journal-by-phase':
                $sql = "SELECT DISTINCT `jobnum` FROM `jobcst`";
                break;
            case 'job-cost-by-cost-code':
                $sql = "SELECT DISTINCT `cstcde` FROM `jobcst`";
                break;
            case 'part-record':
                $sql = "SELECT DISTINCT `recnum` FROM `tkfprt`";
                break;
            case 'purchase-orders':
                $sql = "SELECT DISTINCT `ordnum` FROM `pchord`";
                break;
            case 'receivable-invoice-retention':
                $sql = "SELECT DISTINCT `recnum` FROM `arivln`";
                break;
            case 'wip-schedule':


                $sql = "SELECT DISTINCT `recnum` FROM `actrec`";
                break;
            default:
                return ['error' => 'Invalid report id, or we are working on it'];
        }

        $records = $wpdb->get_results($sql);
        $records = new Collection($records, new RecordsTransformer());

        return $this->get_response($records);
    }

    public function showBudget(WP_REST_Request $request): array
    {
        global $wpdb;
        $recnum = $request->get_param('record');

        $sql = "SELECT `matbdg` AS 'materials',
       `labbdg` AS 'labor', `eqpbdg` AS 'equipment',
       `subbdg` AS 'subcontract', `othbdg` AS 'other', 
       `ttlbdg` AS 'total'
        FROM `bdglin`
        WHERE `recnum` = {$recnum}";
        $results = $wpdb->get_results($sql);
        $results = new Collection($results, new ReportTransformer());

        return $this->get_response($results);
    }

    public function showInventoryByLocation(WP_REST_Request $request): array
    {
        global $wpdb;
        $locnum = $request->get_param('record');

        $sql = $locnum ?
            "SELECT `qty`.`locnum` AS 'location',
        `qty`.`prtnum` AS 'part-number', `qty`.`qtyohn` AS 'on-hand',
        `tln`.`untdsc` AS 'unit', `loc`.`locnme` AS 'location-name',
        `tln`.`prtdsc` AS 'description'
        FROM `invqty` AS `qty`
        LEFT JOIN `invloc` AS `loc` ON `qty`.`locnum` = `loc`.`recnum`
        LEFT JOIN `invtln` AS `tln` ON `qty`.`prtnum` = `tln`.`prtnum`
        WHERE `qty`.`locnum` = {$locnum} AND qty.qtyohn > 0
        ORDER BY `qty`.`locnum`"
        :
            "SELECT `qty`.`locnum` AS 'location',
        `qty`.`prtnum` AS 'part-number', `qty`.`qtyohn` AS 'on-hand',
        `tln`.`untdsc` AS 'unit', `loc`.`locnme` AS 'location-name',
        `tln`.`prtdsc` AS 'description'
        FROM `invqty` AS `qty`
        LEFT JOIN `invloc` AS `loc` ON `qty`.`locnum` = `loc`.`recnum`
        LEFT JOIN `invtln` AS `tln` ON `qty`.`prtnum` = `tln`.`prtnum`
        WHERE `qty`.`qtyohn` >= 0
        ORDER BY `qty`.`locnum`"
        ;
        $results = $wpdb->get_results($sql);
        $results = new Collection($results, new ReportTransformer());
        return $this->get_response($results);
    }

    public function showWacValues(WP_REST_Request $request): array
    {
        global $wpdb;
        $prtnum = $request->get_param('record');

        $sql = $wpdb->prepare("SELECT `qty`.`prtnum` AS 'part-number', `tln`.`prtdsc` AS 'description',
            `tln`.`untdsc` AS 'unit', `qty`.`qtyohn` AS 'on-hand',
            `tln`.`invttl` AS `average-price`, `tln`.`invttl` * `qty`.`qtyohn` AS `value`
            FROM `invqty` AS `qty`
            LEFT JOIN `invtln` AS `tln` ON `qty`.`prtnum` = `tln`.`prtnum`
            WHERE `qty`.`prtnum` = %s", $prtnum);

        $results = $wpdb->get_results($sql);
        $results = new Collection($results, new ReportTransformer());
        return $this->get_response($results);
    }

    public function showJobCostTotals(WP_REST_Request $request): array
    {
        global $wpdb;
        $jobnum = $request->get_param('record');

        $sql = $wpdb->prepare("SELECT `ar`.`jobnme` AS 'job-name',
            `phase`.`phsnme` AS 'phase-name',
            `type`.`typnme` AS 'cost-type',
            `job`.`cstamt` AS 'cost'
            FROM `jobcst` AS `job`
            LEFT JOIN `actrec` AS `ar` ON `job`.`jobnum` = `ar`.`recnum`
            LEFT JOIN `jobphs` AS `phase` ON `job`.`jobnum` = `phase`.`recnum`
            LEFT JOIN `csttyp` AS `type` ON `job`.`csttyp` = `type`.`recnum`
            WHERE `job`.`jobnum` = %s", $jobnum);

        $results = $wpdb->get_results($sql);
        $results = new Collection($results, new ReportTransformer());
        return $this->get_response($results);
    }

    public function showJobCostByCostCode(WP_REST_Request $request): array
    {
        global $wpdb;
        $cost_code = $request->get_param('record');

        $sql = $wpdb->prepare("SELECT `job`.`cstcde` AS 'cost-code',
            `code`.`cdenme` AS 'code-name',
            `job`.`recnum` AS 'record-number',
            `job`.`trnnum` AS 'transaction',
            `job`.`trndte` AS 'date',
            `job`.`dscrpt` AS 'description',
            `type`.`typnme` AS 'cost-type',
            `job`.`cstamt` AS 'amount'
            FROM `jobcst` AS `job`
            LEFT JOIN `csttyp` AS `type` ON `job`.`csttyp` = `type`.`recnum`
            LEFT JOIN `cstcde` AS `code` ON `job`.`cstcde` = `code`.`recnum`
            WHERE `job`.`cstcde` = %s", $cost_code);

        $results = $wpdb->get_results($sql);
        $results = new Collection($results, new ReportTransformer());
        return $this->get_response($results);
    }

    public function showJobCostJournal(WP_REST_Request $request): array
    {
        global $wpdb;
        $jobnum = $request->get_param('record');

        $sql = $wpdb->prepare("SELECT `job`.`recnum` AS 'record-number',
            `job`.`trnnum` AS 'transaction',
            `job`.`trndte` AS 'date',
            `job`.`dscrpt` AS 'description',
            `type`.`typnme` AS 'cost-type',
            `job`.`cstamt` AS 'amount',
            `status`.`stsnme` AS 'status'
            FROM `jobcst` AS `job`
            LEFT JOIN `csttyp` AS `type` ON `job`.`csttyp` = `type`.`recnum`
            LEFT JOIN `clnsts` AS `status` ON `job`.`status` = `status`.`recnum`
            WHERE `job`.`jobnum` = %s", $jobnum);

        $results = $wpdb->get_results($sql);
        $results = new Collection($results, new ReportTransformer());
        return $this->get_response($results);
    }

    public function showJobLaborJournal(WP_REST_Request $request): array
    {
        global $wpdb;
        $jobnum = $request->get_param('record');

        $sql = $wpdb->prepare("SELECT `ar`.`jobnme` AS 'job-name',
            `phase`.`phsnme` AS 'phase',
            `job`.`cstcde` AS 'cost-code',
            `code`.`cdenme` AS 'description',
            `job`.`recnum` AS 'record-number',
            `job`.`trnnum` AS 'transaction',
            `job`.`trndte` AS 'date',
            CONCAT(`emp`.`fstnme`, ' ', `emp`.`lstnme`) AS 'employee',
            `type`.`typnme` AS 'type',
            `job`.`csthrs` AS 'hours',
            `job`.`cstamt` AS 'amount'
            FROM `jobcst` AS `job`
            LEFT JOIN `actrec` AS `ar` ON `job`.`jobnum` = `ar`.`recnum`
            LEFT JOIN `jobphs` AS `phase` ON `job`.`jobnum` = `phase`.`recnum`
            LEFT JOIN `cstcde` AS `code` ON `job`.`cstcde` = `code`.`recnum`
            LEFT JOIN `csttyp` AS `type` ON `job`.`csttyp` = `type`.`recnum`
            LEFT JOIN `employ` AS `emp` ON `job`.`empnum` = `emp`.`recnum`
            WHERE `job`.`jobnum` = %s", $jobnum);

        $results = $wpdb->get_results($sql);
        $results = new Collection($results, new ReportTransformer());
        return $this->get_response($results);
    }

    public function showPurchaseOrders(WP_REST_Request $request): array
    {
        global $wpdb;
        $ordnum = $request->get_param('record');

        $sql = $wpdb->prepare("SELECT `pc`.`prtnum` AS 'part-number',
            `pc`.`prtdsc` AS 'description',
            `pc`.`untdsc` AS 'unit',
            `pc`.`linqty` AS 'quantity',
            `pc`.`linprc` AS 'price',
            `pc`.`linqty` * `pc`.`linprc` AS 'amount'
            FROM `pchord` AS `ords`
            INNER JOIN `pcorln` AS `pc` ON `ords`.`recnum` = `pc`.`recnum`
            WHERE `ords`.`ordnum` = %s", $ordnum);

        $results = $wpdb->get_results($sql);
        $results = new Collection($results, new ReportTransformer());
        return $this->get_response($results);
    }

    public function showReceivableInvoice(WP_REST_Request $request): array
    {
        global $wpdb;
        $recnum = $request->get_param('record');

        $sql = $wpdb->prepare("
            SELECT `inv`.`dscrpt` AS 'description',
            `inv`.`extprc` AS 'amount'
            FROM `arivln` AS `inv`
            WHERE `inv`.`recnum` = %s", $recnum);

        $results = $wpdb->get_results($sql);
        $results = new Collection($results, new ReportTransformer());
        return $this->get_response($results);
    }

    public function showPartsRecord(WP_REST_Request $request): array
    {
        global $wpdb;
        $recnum = $request->get_param('record');

        $sql = $wpdb->prepare("
            SELECT `parts`.`recnum` AS `part-number`,
            `parts`.`alpnum` AS `alpha-number`,
            `parts`.`prtunt` AS `unit`,
            `parts`.`binnum` AS `bin-number`,
            `parts`.`msdsnm` AS `msds-number`,
            `parts`.`mannme` AS `manufacturer`,
            `parts`.`mannum` AS `manufacturer-part-number`,
            `cost`.`cdenme` AS `cost-code`,
            `type`.`typnme` AS `cost-type`,
            `parts`.`prttsk` AS `task`,
            `class`.`clsnme` AS `class`,
            `parts`.`usrdf1` AS `user-defined-1`,
            `parts`.`usrdf2` AS `user-defined-2`,
            `parts`.`prtspc` AS `spec-file`,
            `parts`.`prtcst` AS `last-cost`,
            `parts`.`lstupd` AS `last-update`,
            `parts`.`mrkupr` AS `markup`,
            `parts`.`prtbil` AS `billing-amount`,
            `parts`.`avgcst` AS `average-cost`,
            `parts`.`stkitm` AS `stock-item`,
            `parts`.`serinv` AS `serialized-item`,
            `parts`.`reordr` AS `reorder-quantity`,
            `parts`.`minord` AS `minimum-order`,
            `parts`.`pkgqty` AS `package-quantity`,
            `parts`.`prtwgt` AS `weight`,
            `parts`.`qtyohn` AS `quantity-on-hand`,
            `parts`.`srvprt` AS `service-equipment-item`,
            `parts`.`oemdur` AS `oem-warranty-duration`
            FROM `tkfprt` AS `parts`
            LEFT JOIN `prtcls` AS `class` ON `parts`.`prtcls` = `class`.`recnum`
            LEFT JOIN `cstcde` AS `cost` ON `parts`.`cstcde` = `cost`.`recnum`
            LEFT JOIN `csttyp` AS `type` ON `parts`.`csttyp` = `type`.`recnum`
            WHERE `parts`.`recnum` = %s", $recnum);

        $results = $wpdb->get_results($sql);
        $results = new Collection($results, new ReportTransformer());
        return $this->get_response($results);
    }

    public function showWipSchedule(): array
    {
        global $wpdb;

        $results = new WIPSchedule();
        $results->generate();

        $results = new Collection($results->rows, new ReportTransformer());
        return $this->get_response($results);
    }

    public function showStatusReport(): array
    {
        global $wpdb;

        $results = new StatusReport();
        $results->generate();

        $results = new Collection($results->rows, new ReportTransformer());
        return $this->get_response($results);
    }

    public function showPartsOnHand(WP_REST_Request $request): array
    {
        global $wpdb;
        $recnum = $request->get_param('record');

        $subquery = $wpdb->prepare("
            SELECT `recnum`, MAX(`_Id`) as max_id
            FROM `tkfprt`
            WHERE `qtyohn` > 0
            GROUP BY `recnum`
        ", $recnum);

        $sql = $wpdb->prepare("
            SELECT `parts`.`recnum` AS `part-number`,
                `parts`.`prtnme` AS `name`,
                `parts`.`binnum` AS `bin-number`,
                `parts`.`prtunt` AS `unit`,
                `parts`.`qtyohn` AS `quantity-on-hand`,
                `parts`.`avgcst` AS `average-cost`,
                `cost`.`cdenme` AS `cost-code`,
                `type`.`typnme` AS `cost-type`,
                `parts`.`mannme` AS `manufacturer`,
                `parts`.`mannum` AS `manufacturer-part-number`,
                `parts`.`mrkupr` AS `markup`,
                `parts`.`stkitm` AS `stock-item`,
                `parts`.`serinv` AS `serialized-item`,
                `parts`.`reordr` AS `reorder-quantity`,
                `parts`.`minord` AS `minimum-order`,
                `parts`.`prtwgt` AS `weight`,
                `parts`.`oemdur` AS `oem-warranty-duration`
            FROM `tkfprt` AS `parts`
            LEFT JOIN `prtcls` AS `class` ON `parts`.`prtcls` = `class`.`recnum`
            LEFT JOIN `cstcde` AS `cost` ON `parts`.`cstcde` = `cost`.`recnum`
            LEFT JOIN `csttyp` AS `type` ON `parts`.`csttyp` = `type`.`recnum`
            INNER JOIN (" . $subquery . ") AS `max_ids` ON `parts`.`recnum` = `max_ids`.`recnum` AND `parts`.`_Id` = `max_ids`.`max_id`
            WHERE `parts`.`qtyohn` > 0", $recnum);

        $results = $wpdb->get_results($sql);

        $results = new Collection($results, new ReportTransformer());
        return $this->get_response($results);
    }

    public function showPartsOnHandTwo(): array
    {
        global $wpdb;

        $sql = $wpdb->prepare("
            SELECT `inv`.`prtnum` AS `part-number`,
                `det`.`prtdsc` AS `description`,
                `det`.`untdsc` AS `unit`,
                `inv`.`qtyohn` AS `quantity-on-hand`,
                `det`.`average-cost`,
                `loc`.`locnme` AS `location`,
                `parts`.`manufacturer`,
                `parts`.`manufacturer-part-number`,
                `parts`.`markup`,
                `parts`.`reorder-quantity`,
                `parts`.`minimum-order`,
                `parts`.`weight`,
                `parts`.`oem-warranty-duration`
            FROM `invqty` AS `inv`
            LEFT JOIN (
                SELECT FLOOR(`prtnum`) AS `prtnum`, MAX(`prtdsc`) AS `prtdsc`, MAX(`untdsc`) AS `untdsc`, AVG(`invcst`) AS `average-cost`
                FROM `invtln`
                GROUP BY `prtnum`
            ) AS `det` ON `inv`.`prtnum` = `det`.`prtnum`
            LEFT JOIN `invloc` AS `loc` ON `inv`.`locnum` = `loc`.`recnum`
            LEFT JOIN (
                SELECT `recnum`, MAX(`mannme`) AS `manufacturer`, MAX(`mannum`) AS `manufacturer-part-number`, 
                MAX(`mrkupr`) AS `markup`, MAX(`reordr`) AS `reorder-quantity`, MAX(`minord`) AS `minimum-order`, 
                MAX(`prtwgt`) AS `weight`, MAX(`oemdur`) AS `oem-warranty-duration`
                FROM `tkfprt`
                GROUP BY `recnum`
            ) AS `parts` ON `inv`.`prtnum` = `parts`.`recnum`
            WHERE `inv`.`qtyohn` > 0 AND `inv`.`qtyohn` IS NOT NULL");

        $results = $wpdb->get_results($sql);

        $results = new Collection($results, new ReportTransformer());
        return $this->get_response($results);
    }
}


