<?php

namespace Akuma\Component\Testing\Database;

interface DatabaseIsolationInterface
{
    /** Annotation names */
    const DB_ISOLATION_PER_TEST_ANNOTATION = 'dbIsolationPerTest';

    /**
     * Use to avoid transaction rollbacks with Connection::transactional and missing on conflict in Doctrine
     * SQLSTATE[25P02] current transaction is aborted, commands ignored until end of transaction block
     */
    const NEST_TRANSACTIONS_WITH_SAVE_POINTS = 'nestTransactionsWithSavePoints';
}
