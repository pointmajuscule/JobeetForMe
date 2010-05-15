<?php


class JobeetJobTable extends Doctrine_Table
{
  static public $types = array(
    'full-time' => 'Full time',
    'part-time' => 'Part time',
    'freelance' => 'Freelance',
  );

  public function getForToken(array $parameters)
  {
    $affiliate = Doctrine_Core::getTable('JobeetAffiliate')
      ->findOneByToken($parameters['token']);
    if (!$affiliate || !$affiliate->getIsActive())
    {
      throw new sfError404Exception(sprintf('Affiliate with token "%s" does not exist or is not activated.', $parameters['token']));
    }

    return $affiliate->getActiveJobs();
  }

  public function getTypes()
  {
    return self::$types;
  }

  public static function getInstance()
  {
      return Doctrine_Core::getTable('JobeetJob');
  }

  public function getActiveJobs(Doctrine_Query $q = null)
  {
    return $this->addActiveJobsQuery($q)->execute();
  }

  public function retrieveActiveJob(Doctrine_Query $q)
  {
    return $this->addActiveJobsQuery($q)->fetchOne();
  }

  public function countActiveJobs(Doctrine_Query $q = null)
  {
    return $this->addActiveJobsQuery($q)->count();
  }

  public function addActiveJobsQuery(Doctrine_Query $q = null)
  {
    if (is_null($q))
    {
      $q = Doctrine_Query::create()
        ->from('JobeetJob j');
    }

    $alias = $q->getRootAlias();

    $q->andWhere($alias . '.expires_at > ?', date('Y-m-d H:i:s', time()))
      ->addOrderBy($alias . '.created_at DESC');

    $q->andWhere($alias . '.is_activated = ?', 1);

    return $q;
  }

  public function retrieveBackendJobList(Doctrine_Query $q)
  {
    $rootAlias = $q->getRootAlias();

    $q->leftJoin($rootAlias . '.JobeetCategory c');

    return $q;
  }

  public function getLatestPost()
  {
    $q = Doctrine_Query::create()->from('JobeetJob j');

    $this->addActivejobsQuery($q);

    return $q->fetchOne();
  }
}
