<?php

namespace WeDevs\PM\Reports\Templates;

class StatusReport
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
            $this->rows[] = new StatusReportRow($job->recnum);
        }
    }
}
