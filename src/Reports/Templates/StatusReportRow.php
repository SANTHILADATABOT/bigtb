<?php
namespace WeDevs\PM\Reports\Templates;

class StatusReportRow
{

    private object $db;
    //   report values
    public string $salesperson;
    public string $job_name;
    public string $customer;
    public int $job_num;
    public float $contract;
    public float $change_orders;
    public float $total;
    public float $direct_cost;
    public float $percent_budget_used;
    public float $total_invoiced;
    public float $unpaid_invoices;
    public float $balance_on_contract;
    public float $estimated_profit;
    public float $percent_profit;
    // Non report values
    public float $budget;

    public function __construct($jobID)
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->job_num = $jobID;
        $this->runAll();
    }

    private function runAll():void
    {
        $this->salesperson();
        $this->job_name();
        $this->customer();
        $this->contract();
        $this->change_orders();
        $this->total();
        $this->direct_cost();
        $this->percent_budget_used();
        $this->total_invoiced();
        $this->unpaid_invoices();
        $this->balance_on_contract();
        $this->estimated_profit();
        $this->percent_profit();
    }

    public function salesperson():void
    {
        global $wpdb;
        $query = $wpdb->prepare("
            SELECT a.slsemp, e.fstnme, e.lstnme
            FROM actrec a JOIN employ e
                ON CAST(a.slsemp AS UNSIGNED) = e.recnum
            WHERE a.recnum = %d
        ", $this->job_num);
        $result = $wpdb->get_results($query);
        $this->salesperson = !empty($result) ? $result[0]->fstnme . ' ' . $result[0]->lstnme : '';
    }

    public function job_name():void
    {
        $sql = "SELECT jobnme FROM actrec WHERE recnum = $this->job_num";
        $this->job_name = $this->db->get_results($sql)[0]->jobnme;
    }

    public function customer():void
    {
        $sql = $this->db->prepare("SELECT c.clnnme 
                FROM actrec a JOIN reccln c
                    ON CAST(a.clnnum AS UNSIGNED) = c.recnum
                WHERE a.recnum = %d", $this->job_num);
        $result = $this->db->get_results($sql);
        $this->customer = !empty($result) ? $result[0]->clnnme : '';
    }

    private function contract(): void
    {
        $this->contract = $this->runQuery("SELECT actrec.cntrct FROM actrec WHERE actrec.recnum = %d", [$this->job_num]);
    }

    public function change_orders():void
    {
        $this->change_orders = $this->runQuery("SELECT SUM(prmchg.appamt) FROM prmchg WHERE prmchg.status = 1 AND prmchg.jobnum = %d", [$this->job_num]);
    }

    public function total():void
    {
        $this->contract ?? $this->contract();
        $this->change_orders ?? $this->change_orders();
        $this->total = $this->contract + $this->change_orders;
    }

    public function direct_cost():void
    {
        $this->direct_cost = $this->runQuery("SELECT SUM(jobcst.cstamt) FROM jobcst WHERE jobcst.status = 1
            AND jobcst.jobnum = %d", [$this->job_num]);
    }

    public function percent_budget_used():void
    {
        $this->budget ?? $this->budget();
        $this->percent_budget_used = $this->budget > 0 ?
            100 * $this->direct_cost / $this->budget :
            0;
    }

    private function budget():void
    {
        $result = 0;

        $result += $this->runQuery("SELECT SUM(`bdglin`.`ttlbdg`)
                                        FROM `bdglin` WHERE `bdglin`.`recnum` = %d", [$this->job_num]);

        $result += $this->runQuery("SELECT SUM(prmchg.cstamt) FROM prmchg WHERE prmchg.status = 1
                                        AND prmchg.jobnum = %d", [$this->job_num]);

        $result += $this->runQuery("SELECT SUM(prmchg.cstamt) FROM prmchg WHERE prmchg.status = 1
                                        AND prmchg.jobnum = %d", [$this->job_num]);

        $this->budget = $result;
    }

    private function total_invoiced():void
    {
        $this->total_invoiced = $this->runQuery("SELECT SUM(acrinv.invttl) FROM acrinv WHERE acrinv.status < 5
        AND acrinv.invtyp = 1 AND acrinv.jobnum = %d", [$this->job_num]);
    }

    private function unpaid_invoices():void
    {
        $this->unpaid_invoices = $this->runQuery("SELECT SUM(acrinv.invttl) FROM acrinv WHERE acrinv.status < 5
        AND acrinv.invtyp = 1 AND acrinv.jobnum = %d", [$this->job_num]);
    }

    private function balance_on_contract():void
    {
        $this->balance_on_contract = $this->contract - $this->total_invoiced;
    }

    private function estimated_profit():void
    {
        $this->estimated_profit = $this->total_invoiced - $this->direct_cost;
    }

    private function percent_profit():void
    {
        $this->percent_profit = $this->total_invoiced > 0 ?
            100 * $this->estimated_profit / $this->total_invoiced :
            0;
    }

    private function runQuery($sql, $insertions): float
    {
        $statement = $this->db->prepare($sql, ...$insertions);
        $result = $this->db->get_var($statement);
        return is_numeric($result) ? $result : 0;
    }
}
