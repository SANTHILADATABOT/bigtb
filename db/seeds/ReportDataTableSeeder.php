<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use WeDevs\PM\Reports\Models\ReportData;

class ReportDataTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!ReportData::count()) {
            ReportData::insert([
                [
                    'report_name' => 'budget',
                    'title' => 'Budget Reports'
                ],
                [
                    'report_name' => 'inventory-quantity-by-location',
                    'title' => 'Quantity by Location'
                ],
                [
                    'report_name' => 'wac-value-report',
                    'title' => 'WAC Value Report~Land'
                ],
                [
                    'report_name' => 'job-cost-totals',
                    'title' => 'Job Cost Totals~Land; by Job/Phase'
                ],
                [
                    'report_name' => 'job-cost-by-cost-code',
                    'title' => 'Job Cost by Job/Cost Code with Notes'
                ],
                [
                    'report_name' => 'job-cost-journal',
                    'title' => 'Job Cost Journal'
                ],
                [
                    'report_name' => 'job-labor-journal-by-phase',
                    'title' => 'Job Labor Journal~by Job/Phase/Cost Code'
                ],
                [
                    'report_name' => 'part-record',
                    'title' => 'Part Record with Notes'
                ],
                [
                    'report_name' => 'purchase-orders',
                    'title' => 'Purchase Orders'
                ],
                [
                    'report_name' => 'receivable-invoice-retention',
                    'title' => 'Receivable Invoice~Retention'
                ],
                [
                    'report_name' => 'wip-schedule',
                    'title' => 'WIP Schedule - Fencing Specialists'
                ]
            ]);
        }
    }
}
