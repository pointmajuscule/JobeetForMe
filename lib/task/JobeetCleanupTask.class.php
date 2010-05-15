<?php
protected function execute($arguments = array(), $options = array())
{
  $databaseManager = new sfDatabaseManager($this->configuration);

  $index = JobeetJobTable::getLuceneIndex();

  $q = Doctrine_Query::create()
    ->from('JobeetJob j')
    ->where('j.expires_at < ?', date('Y-m-d'));

  $jobs = $q->execute();
  foreach ($jobs as $job)
  {
    if ($hit = $index->find('pk:'.$job->getId()))
    {
      $index->delete($hit->id);
    }
  }

  $index->optimize();

  $this->logSection('lucene', 'Cleaned up and optimized the job index');

  $nb = Doctrine_Core::getTable('JobeetJob')->cleanup($options['days']);

  $this->logSection('doctrine', sprintf('Removed %d stale jobs', $nb));
}
