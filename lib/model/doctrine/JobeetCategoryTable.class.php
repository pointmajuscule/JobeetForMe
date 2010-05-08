<?php


class JobeetCategoryTable extends Doctrine_Table
{
    public static function getInstance()
    {
        return Doctrine_Core::getTable('JobeetCategory');
    }

    public function getWithJobs()
    {
      $q = $this->createQuery('c')
        ->leftJoin('c.JobeetJobs j')
        ->where('j.expires_at > ?', date('Y-m-d H:i:s', time()));

      return $q->execute();
    }
}
