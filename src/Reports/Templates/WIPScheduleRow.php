<?php
namespace WeDevs\PM\Reports\Templates;

class WIPScheduleRow
{

    private object $db;
    private int $recnum;
    private int $endper;
    private int $year;
    //   report values
    public string | null $record_number;
    public float | null $total_contract;
    public float | null $total_budget;
    public float | null $estimated_gross_profit;
    public float | null $contract_revenue_earned;
    public float | null $direct_job_cost;
    public float | null $gross_profit;
    public float | null $total_billed_to_date;
    public float | null $percent_budget_used;
    public float | null $costs_and_earnings_in_excess_of_billings;
    public float | null $billings_in_excess_of_costs_and_earnings;
    public float | null $prior_year_costs;
    public float | null $current_year_earned_revenue;
    public float | null $current_year_costs;
    public float | null $current_year_gross_profit;
    public string | null $salesperson;
    public string | null $estimator;
    public string | null $created_at;
    public int | null $status;
    //    non-report values
    private bool | null $under_budget;
    private float | null $contract_income;
    private float | null $all_change_orders;
    private float | null $change_orders_prior_yr;
    private float | null $change_orders_current_yr;
    private float | null $all_job_cost_hours;
    private float | null $job_cost_hours_prior_yr;
    private float | null $job_cost_hours_current_yr;
    private float | null $invoices_current_yr;
    private float | null $invoices_prior_yr;
    private float | null $costs_billings_difference;
    private float | null $costs_over_money;

    public function __construct($recnum, $endper, $year)
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->record_number = $recnum;
        $this->recnum = $recnum;
        $this->endper = $endper;
        $this->year = $year;
        $this->runAll($this->recnum, $this->endper, $this->year);
    }

    public function runAll(): void {
        $this->try_catch($this, 'total_contract');
        $this->try_catch($this, 'total_budget');
        $this->try_catch($this, 'estimated_gross_profit');
        $this->try_catch($this, 'contract_revenue_earned');
        $this->try_catch($this, 'gross_profit');
        $this->try_catch($this, 'percent_budget_used');
        $this->try_catch($this, 'costs_over_money');
        $this->try_catch($this, 'current_year_gross_profit');
        $this->try_catch($this, 'direct_job_cost');
        $this->try_catch($this, 'total_billed_to_date');
        $this->try_catch($this, 'costs_and_earnings_in_excess_of_billings');
        $this->try_catch($this, 'billings_in_excess_of_costs_and_earnings');
        $this->try_catch($this, 'prior_year_costs');
        $this->try_catch($this, 'current_year_earned_revenue');
        $this->try_catch($this, 'current_year_costs');
        $this->try_catch($this, 'salesperson');
        $this->try_catch($this, 'estimator');
        $this->try_catch($this, 'created_at');
        $this->try_catch($this, 'status');
    }

   private function try_catch($object, $property_function): void
    {
        try {
            call_user_func([$object, $property_function]);
        } catch (\Error $e) {
            error_log("Error generating " . $property_function . " for " . $this->record_number . ": " . $e->getMessage());
            $object->$property_function = null;
        }
    }

    private function total_contract(): void
    {
        $this->contract_income ?? $this->contract_income();
        $this->all_change_orders ?? $this->all_change_orders();
        $this->total_contract = $this->contract_income + $this->all_change_orders;
    }

    private function contract_income(): void
    {
        $this->contract_income = $this->runQuery("SELECT actrec.cntrct FROM actrec WHERE actrec.recnum = %d", [$this->recnum]);
    }

    private function all_change_orders(): void
    {
        $this->change_orders_prior_yr ?? $this->change_orders_prior_yr();
        $this->change_orders_current_yr ?? $this->change_orders_current_yr();
        $this->all_change_orders = $this->change_orders_prior_yr + $this->change_orders_current_yr;
    }

    private function change_orders_prior_yr(): void
    {
        $this->change_orders_prior_yr = $this->runQuery("SELECT SUM(prmchg.appamt) FROM prmchg WHERE prmchg.status = 1 AND prmchg.jobnum = %d AND prmchg.postyr < %d", [$this->recnum, $this->year]);
    }

    private function change_orders_current_yr(): void
    {
        $this->change_orders_current_yr = $this->runQuery("SELECT SUM(prmchg.appamt) FROM prmchg WHERE prmchg.status = 1 AND prmchg.jobnum = %d AND prmchg.actper <= %d AND prmchg.postyr = %d", [$this->recnum, $this->endper, $this->year]);
    }

    private function all_job_cost_hours(): void
    {
        $this->job_cost_hours_prior_yr ?? $this->job_cost_hours_prior_yr();
        $this->job_cost_hours_current_yr ?? $this->job_cost_hours_current_yr();
        $this->all_job_cost_hours = $this->job_cost_hours_prior_yr + $this->job_cost_hours_current_yr;
    }

    private function job_cost_hours_prior_yr(): void
    {
        $this->job_cost_hours_prior_yr = $this->runQuery("SELECT SUM(jobcst.csthrs) FROM jobcst WHERE jobcst.status = 1 AND jobcst.jobnum = %d AND jobcst.postyr < %d", [$this->recnum, $this->year]);
    }

    private function job_cost_hours_current_yr(): void
    {
        $this->job_cost_hours_current_yr = $this->runQuery("SELECT SUM(jobcst.csthrs) FROM jobcst WHERE jobcst.status = 1 AND jobcst.jobnum = %d AND jobcst.actprd <= %d AND jobcst.postyr = %d", [$this->recnum, $this->endper, $this->year]);
    }

    private function total_budget(): void
    {
        $result = 0;

        $result += $this->runQuery("SELECT SUM(`bdglin`.`ttlbdg`)   
                                        FROM `bdglin` WHERE `bdglin`.`recnum` = %d", [$this->recnum]);

        $result += $this->runQuery("SELECT SUM(prmchg.cstamt) FROM prmchg WHERE prmchg.status = 1
                                        AND prmchg.jobnum = %d AND prmchg.actper <= %d
                                        AND prmchg.postyr = %d", [$this->recnum, $this->endper, $this->year]);

        $result += $this->runQuery("SELECT SUM(prmchg.cstamt) FROM prmchg WHERE prmchg.status = 1
                                        AND prmchg.jobnum = %d AND prmchg.postyr < %d", [$this->recnum, $this->year]);

        $this->total_budget = $result;
    }

    private function under_budget(): void
    {
        $this->direct_job_cost ?? $this->direct_job_cost();
        $this->all_job_cost_hours ?? $this->all_job_cost_hours();
        $this->total_budget ?? $this->total_budget();
        $this->under_budget = (($this->direct_job_cost + ($this->all_job_cost_hours * 0)) > $this->total_budget);
    }

    private function estimated_gross_profit(): void
    {
        $this->total_contract ?? $this->total_contract();
        $this->total_budget ?? $this->total_budget();
        $this->estimated_gross_profit = $this->total_contract - $this->total_budget;
    }

    private function contract_revenue_earned(): void
    {
        $this->under_budget ?? $this->under_budget();
        $this->all_job_cost_hours ?? $this->all_job_cost_hours();
        $this->direct_job_cost ?? $this->direct_job_cost();
        $this->total_budget ?? $this->total_budget();
        $this->total_contract ?? $this->total_contract();

        if ($this->under_budget) {
            $this->contract_revenue_earned = $this->total_contract;
        } else {
            $this->contract_revenue_earned = ($this->direct_job_cost
                / $this->total_budget)
                * $this->total_contract;
        }
    }

    private function direct_job_cost(): void
    {
        $this->current_year_costs ?? $this->current_year_costs();
        $this->prior_year_costs ?? $this->prior_year_costs();

        $this->direct_job_cost = $this->current_year_costs + $this->prior_year_costs;
    }

    private function gross_profit(): void
    {
        $this->under_budget ?? $this->under_budget();
        $this->all_job_cost_hours ?? $this->all_job_cost_hours();
        $this->direct_job_cost ?? $this->direct_job_cost();
        $this->total_budget ?? $this->total_budget();
        $this->estimated_gross_profit ?? $this->estimated_gross_profit();

        if ($this->under_budget) {
            $this->gross_profit = $this->estimated_gross_profit;
        } else {
            $this->gross_profit = (($this->direct_job_cost + ($this->all_job_cost_hours * 0)) / $this->total_budget) * $this->estimated_gross_profit;
        }
    }

    private function total_billed_to_date(): void
    {
        $this->invoices_current_yr ?? $this->invoices_current_yr();
        $this->invoices_prior_yr ?? $this->invoices_prior_yr();

        $this->total_billed_to_date = $this->invoices_current_yr + $this->invoices_prior_yr;
    }

    private function invoices_current_yr(): void
    {
        $this->invoices_current_yr = $this->runQuery("SELECT SUM(acrinv.invttl) FROM acrinv WHERE acrinv.status < 5
        AND acrinv.invtyp = 1 AND acrinv.jobnum = %d AND acrinv.actper <= %d
        AND acrinv.postyr = %d", [$this->recnum, $this->endper, $this->year]);
    }

    private function invoices_prior_yr(): void
    {
        $this->invoices_prior_yr = $this->runQuery("SELECT SUM(acrinv.invttl) FROM acrinv WHERE acrinv.status < 5
        AND acrinv.invtyp = 1 AND acrinv.jobnum = %d AND acrinv.postyr < %d", [$this->recnum, $this->year]);
    }

    private function percent_budget_used(): void
    {
        $this->percent_budget_used = 100 * $this->direct_job_cost / $this->total_budget;
    }

    private function costs_and_earnings_in_excess_of_billings(): void
    {
        $this->under_budget ?? $this->under_budget();
        $this->costs_billings_difference ?? $this->costs_billings_difference();
        $this->costs_over_money ?? $this->costs_over_money();

        if ($this->under_budget) {
            $this->costs_and_earnings_in_excess_of_billings =
                $this->costs_billings_difference >= .01 ? $this->costs_billings_difference : 0;
        } else {
            $this->costs_and_earnings_in_excess_of_billings =
                $this->costs_over_money >= .01 ? $this->costs_over_money : 0;
        }
    }

    private function billings_in_excess_of_costs_and_earnings(): void
    {
        $this->under_budget ?? $this->under_budget();
        $this->costs_billings_difference ?? $this->costs_billings_difference();
        $this->costs_over_money ?? $this->costs_over_money();

        if ($this->under_budget) {
            $this->billings_in_excess_of_costs_and_earnings =
                $this->costs_billings_difference <= .01 ? $this->costs_billings_difference : 0;
        } else {
            $this->billings_in_excess_of_costs_and_earnings =
                $this->costs_over_money < .01 ? $this->costs_over_money : 0;
        }
    }

    private function costs_billings_difference(): void
    {
        $this->total_contract ?? $this->total_contract();
        $this->total_billed_to_date ?? $this->total_billed_to_date();
        $this->costs_billings_difference = $this->total_contract - $this->total_billed_to_date;
    }

    private function costs_over_money(): void
    {
        $this->costs_billings_difference ?? $this->costs_billings_difference();
        $this->direct_job_cost ?? $this->direct_job_cost();
        $this->all_job_cost_hours ?? $this->all_job_cost_hours();
        $this->total_budget ?? $this->total_budget();

        $this->costs_over_money = (($this->direct_job_cost + ($this->all_job_cost_hours * 0)) / $this->total_budget) * $this->costs_billings_difference;
    }

    private function prior_year_costs(): void
    {
        $this->prior_year_costs = $this->runQuery("SELECT SUM(jobcst.cstamt) FROM jobcst WHERE jobcst.status = 1
            AND jobcst.jobnum = %d AND jobcst.postyr < %d", [$this->recnum, $this->year]);
    }

    private function current_year_earned_revenue(): void
    {
        $this->total_billed_to_date ?? $this->total_billed_to_date();
        $lotprm = $this->runQuery("SELECT actrec.lotprm FROM actrec WHERE actrec.recnum = %d", [$this->recnum]);
        $this->current_year_earned_revenue = $this->total_billed_to_date - $lotprm;
    }

    private function current_year_costs(): void
    {
        $this->current_year_costs = $this->runQuery("SELECT SUM(jobcst.cstamt) FROM jobcst WHERE jobcst.status = 1
            AND jobcst.jobnum = %d AND jobcst.actprd <= %d
            AND jobcst.postyr = %d", [$this->recnum, $this->endper, $this->year]);
    }

    private function current_year_gross_profit(): void
    {
        $this->under_budget ?? $this->under_budget();
        $this->estimated_gross_profit ?? $this->estimated_gross_profit();
        $this->direct_job_cost ?? $this->direct_job_cost();
        $this->all_job_cost_hours ?? $this->all_job_cost_hours();
        $this->total_budget ?? $this->total_budget();

        $plnprc = $this->runQuery("SELECT actrec.plnprc FROM actrec WHERE actrec.recnum = %d", [$this->recnum]);

        if ($this->under_budget) {
            $this->current_year_gross_profit = $this->estimated_gross_profit - $plnprc;
        } else {
            $this->current_year_gross_profit =
                (($this->direct_job_cost - ($this->all_job_cost_hours * 0)) / $this->total_budget)
                * ($this->estimated_gross_profit - $plnprc);
        }
    }

    private function salesperson(): void {
        $slsemp = $this->runQuery("SELECT actrec.slsemp FROM actrec WHERE actrec.recnum = %d", [$this->recnum]);
        $employRow = $this->db->get_row($this->db->prepare("SELECT * FROM employ WHERE recnum = %d", $slsemp), ARRAY_A);
        $this->salesperson = $employRow['fstnme'] . ' ' . $employRow['lstnme'];
    }

    private function estimator(): void {
        $estemp = $this->runQuery("SELECT actrec.estemp FROM actrec WHERE actrec.recnum = %d", [$this->recnum]);
        $employRow = $this->db->get_row($this->db->prepare("SELECT * FROM employ WHERE recnum = %d", $estemp), ARRAY_A);
        $this->estimator = $employRow['fstnme'] . ' ' . $employRow['lstnme'];
    }

    private function created_at(): void {
        $query = $this->db->prepare("SELECT DATE(actrec.insdte) FROM actrec WHERE actrec.recnum = %d", $this->recnum);
        $this->created_at = $this->db->get_var($query);
    }

    private function status(): void {
        $this->status = $this->runQuery("SELECT actrec.status FROM actrec WHERE actrec.recnum = %d", [$this->recnum]);
    }

    private function runQuery($sql, $insertions): float
    {
        $statement = $this->db->prepare($sql, ...$insertions);
        $result = $this->db->get_var($statement);
        return is_numeric($result) ? $result : 0;
    }
}
