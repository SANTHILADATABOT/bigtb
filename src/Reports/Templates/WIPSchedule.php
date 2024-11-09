<?php

namespace WeDevs\PM\Reports\Templates;

class WIPSchedule
{
    private object $db;
    public array $rows = [];

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
    }

    public function generate():void
    {
        $activeJobs = $this->getActiveJobs();
        $this->generateReportRows($activeJobs);
    }

    public function getActiveJobs():array
    {
        $sql = "SELECT recnum FROM actrec WHERE status = 4";
        return $this->db->get_results($sql);
    }

    public function generateReportRows($activeJobs):void
    {
        foreach ($activeJobs as $job) {
            $endper = 8;
            $year = date('Y');
            $this->rows[] = new WIPScheduleRow($job->recnum, $endper, $year);
        }
    }
}
